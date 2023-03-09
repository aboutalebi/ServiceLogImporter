<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceLogTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Storage::append('testLogs.txt', 'order-service - [17/Sep/2022:10:21:53] "POST /orders HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'order-service - [17/Sep/2022:10:21:54] "POST /orders HTTP/1.1" 422');
        Storage::append('testLogs.txt', 'invoice-service - [17/Sep/2022:10:21:55] "POST /invoices HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'order-service - [17/Sep/2022:10:21:56] "POST /orders HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'order-service - [17/Sep/2022:10:21:57] "POST /orders HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'invoice-service - [17/Sep/2022:10:22:58] "POST /invoices HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'invoice-service - [17/Sep/2022:10:22:59] "POST /invoices HTTP/1.1" 422');
        Storage::append('testLogs.txt', 'invoice-service - [17/Sep/2022:10:23:53] "POST /invoices HTTP/1.1" 201');
        Storage::append('testLogs.txt', 'order-service - [17/Sep/2022:10:23:54] "POST /orders HTTP/1.1" 422');
    }

    public function tearDown(): void
    {
        Storage::delete('testLogs.txt');
        parent::tearDown();
    }

    /**
     * A basic feature test example.
     */
    public function test_console_command_not_exist_log_file(): void
    {
        $this->artisan('app:pars-log-file', [
            'file' => "asdasd.txt"
        ])->expectsOutput('Log File Not Found!');
    }

    /**
     * A basic feature test example.
     */
    public function test_console_command_import_logs(): void
    {
        $this->artisan('app:pars-log-file', [
            'file' => "d:/testSimpleLogs.txt"
        ])->expectsOutput("Success.");
    }
}
