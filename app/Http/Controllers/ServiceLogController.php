<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServiceLogController extends Controller
{
    const TIME_FORMAT = "d/M/Y:G:i:s";

    /**
     * @group ServiceLogs
     *
     * Get Count Of Logs Match The Filter Criteria
     *
     * @queryParam filter[serviceName]
     * @queryParam filter[statusCode]
     * @queryParam filter[startDate]
     * @queryParam filter[endDate]
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCount(Request $request)
    {
        $serviceName = null;
        $statusCode = null;
        $startDate = null;
        $endDate = null;

        if ($request->filled('serviceName'))
        {
            $serviceName = $request->string('serviceName');
        }

        if ($request->filled('statusCode'))
        {
            $statusCode = $request->integer('statusCode');
        }

        if ($request->filled('startDate'))
        {
            $startDate = $request->query('startDate');
            $startDate = DateTime::createFromFormat(self::TIME_FORMAT, $startDate);
        }

        if ($request->filled('endDate'))
        {
            $endDate = $request->query('endDate');
            $endDate = DateTime::createFromFormat(self::TIME_FORMAT, $endDate);
        }

        $count = ServiceLog::when($serviceName, function($query, $serviceName) {
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

        return response()->json([
            'count' => $count
        ]);
    }
}
