<?php
namespace App\Services;

interface ILogService
{
    public function getCount(array $data): int;

    public function insert(array $mustInsertData): void;

    public function insertFromFile(string $fileLocation): void;
}
