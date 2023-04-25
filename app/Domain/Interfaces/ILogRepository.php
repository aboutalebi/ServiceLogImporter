<?php
namespace App\Domain\Interfaces;

interface ILogRepository
{
    public function count(string $serviceName = null,
                          int $statusCode = null,
                          string $startDate = null,
                          string $endDate = null): int;

    public function insert(array $mustInsertData): void;


}
