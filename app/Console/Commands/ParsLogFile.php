<?php

namespace App\Console\Commands;

use App\Models\ServiceLog;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;

class ParsLogFile extends Command implements Isolatable
{
    const CHUNK_SIZE = 40;
    const TIME_FORMAT = "d/M/Y:G:i:s";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pars-log-file {file : Location of logs file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read Logs From File And Insert To DB';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $file = (string) $this->argument('file');

        if (!File::exists($file))
        {
            $this->warn("Log File Not Found!");
            return;
        }

        if (($fileLines = $this->getLines($file)) == 0)
        {
            $this->warn("Log File Is Empty!");
            return;
        }

        $bar = $this->output->createProgressBar($fileLines);

        $bar->start();

        LazyCollection::make(function () use ($file) {
            $handle = fopen($file, 'r');

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

        })->each(function ($logs) use ($bar) {
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

            ServiceLog::insert($mustInsertData);
            $bar->advance(self::CHUNK_SIZE);

        });

        $this->newLine();
        $this->info("Success.");
        $bar->finish();
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
