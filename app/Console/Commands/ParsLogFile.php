<?php

namespace App\Console\Commands;

use App\Services\ILogService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class ParsLogFile extends Command implements Isolatable
{
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
    public function handle(ILogService $logService): void
    {
        $file = (string) $this->argument('file');

        $logService->insertFromFile($file);

        $this->newLine();
        $this->info("Success.");
    }
}
