<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\DTO\WorkTaskReportRequestDTO;
use App\Http\Requests\WorkTaskReportRequest;
use App\Services\WorkTaskReportService;
use Illuminate\Http\JsonResponse;

/**
 * Class WorkTaskReportController
 *
 * Handles API endpoints for Work Task reporting.
 */
class WorkTaskReportController extends Controller
{
    public function __construct(
        private WorkTaskReportService $workTaskReportService
    )
    {}

    /**
     * Get resolution-based work task report
     *
     * @param WorkTaskReportRequest $request
     *
     * @return JsonResponse
    */
    public function resolutions(WorkTaskReportRequest $request): JsonResponse
    {
         // Convert request to DTO
        $requestDTO =  WorkTaskReportRequestDTO::fromRequest($request);
       
        // Fetch report data
        $data = $this->workTaskReportService->getReportOnResolutionDTO($requestDTO);

        // Return JSON response
        return response()->json([
            'resolution_types' => $data,
        ]);
    }
}
