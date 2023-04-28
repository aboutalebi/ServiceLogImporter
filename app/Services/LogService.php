<?php
namespace App\Services;

use App\Repositories\LogRepository;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class LogService implements ILogService
{
    const TIME_FORMAT = "d/M/Y:G:i:s";
    const CHUNK_SIZE = 40;

    public function __construct(private readonly LogRepository $logRepository)
    {
    }

    public function getCount(array $data): int
    {
        $serviceName = null;
        $statusCode = null;
        $startDate = null;
        $endDate = null;

        if (isset($data['serviceName']))
        {
            $serviceName = $data['serviceName'];
        }

        if (isset($data['statusCode']))
        {
            $statusCode = $data['statusCode'];
        }

        if (isset($data['startDate']))
        {
            $startDate = $data['startDate'];
            $startDate = DateTime::createFromFormat(self::TIME_FORMAT, $startDate);
        }

        if (isset($data['endDate']))
        {
            $endDate = $data['endDate'];
            $endDate = DateTime::createFromFormat(self::TIME_FORMAT, $endDate);
        }

        return $this->logRepository->count($serviceName, $statusCode, $startDate, $endDate);
    }
    public function insert(array $mustInsertData): void
    {
        $this->logRepository->insert($mustInsertData);
    }

    public function insertFromFile(string $fileLocation): void
    {
        if (!File::exists($fileLocation))
        {
            //$this->warn("Log File Not Found!");
            return;
        }

        if (($fileLines = $this->getLines($fileLocation)) == 0)
        {
            //$this->warn("Log File Is Empty!");
            return;
        }

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $fileLines);
        $progress->start();

        LazyCollection::make(function () use ($fileLocation) {
            $handle = fopen($fileLocation, 'r');

            while (($line = fgets($handle)) !== false)
            {
                yield $line;
            }

        })->chunk(self::CHUNK_SIZE)->map(function ($lines) {
            $pattern = '/(?<serviceName>.*)-service - \[(?<logAt>.*)\] "(?<requestType>.*) \/(?<queryString>.*) (?<extraInfo>.*)" (?<statusCode>\d+)/';

            $result = [];
            foreach ($lines as $line)
            {
                preg_match($pattern, $line, $matches);
                $result[] = $matches;
            }

            return $result;

        })->each(function ($logs) use ($progress) {
            $mustInsertData = [];

            foreach ($logs as $log)
            {
                $logAt = DateTime::createFromFormat(self::TIME_FORMAT, $log['logAt']);

                $mustInsertData[] = [
                    "service_name" => $log['serviceName'],
                    "log_at" => $logAt,
                    "request_type" => $log['requestType'],
                    "query_string" => $log['queryString'],
                    "status_code" => $log['statusCode'],
                ];
            }

            $this->insert($mustInsertData);

            $progress->advance(self::CHUNK_SIZE);

        });

        $progress->finish();
    }

    private function getLines($file): int
    {
        $file = fopen($file, 'rb');
        $lines = 0;
        $buffer = '';

        while (!feof($file))
        {
            $buffer = fread($file, 1024);
            $lines += substr_count($buffer, "\n");
        }

        fclose($file);

        if (strlen($buffer) > 0 && $buffer[-1] != "\n")
        {
            ++$lines;
        }

        return $lines;
    }
}
