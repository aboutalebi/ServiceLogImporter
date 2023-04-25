<?php
namespace App\Repositories;

use App\Models\ServiceLog;
use Illuminate\Database\Eloquent\Builder;

class LogRepository
{
    public function count(string $serviceName = null,
                          int $statusCode = null,
                          string $startDate = null,
                          string $endDate = null): int
    {
        $logModel = new ServiceLog();

        return $logModel->when($serviceName, function($query, $serviceName) {
            return $query->where('service_name', $serviceName);

        })->when($statusCode, function($query, $statusCode) {
            return $query->where('status_code', $statusCode);

        })->when($startDate, function(Builder $query, $startDate) use ($endDate) {
            if (isset($endDate))
            {
                return $query->where(function (Builder $q) use ($startDate, $endDate) {
                    $q->whereTime('log_at', '>=', $startDate)
                        ->whereTime('log_at', '<=', $endDate);
                });

            } else
            {
                return $query->whereTime('log_at', '>=', $startDate);
            }

        })->when($endDate, function($query, $endDate) {
            if (!isset($startDate)) {
                return $query->whereTime('log_at', '<=', $endDate);
            }
            return $query;

        })->count();
    }

    public function insert(array $mustInsertData): void
    {
        ServiceLog::insert($mustInsertData);
    }
}
