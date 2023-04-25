<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterLogsRequest;
use App\Services\ILogService;
use Illuminate\Http\JsonResponse;

class ServiceLogController extends Controller
{
    public function __construct(private readonly ILogService $logService)
    {
    }

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
     * @param FilterLogsRequest $request
     * @return JsonResponse
     */
    public function getCount(FilterLogsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $count = $this->logService->getCount($validated);

        return response()->json([
            'count' => $count
        ]);
    }
}
