<?php

namespace App\Services;

use App\Repositories\WorkTaskRepository;
use App\DTO\WorkTaskReportOnResolutionReportDTO;
use App\DTO\WorkTaskReportRequestDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class WorkTaskReportService
 *
 * Handles business logic for generating Work Task reports grouped by resolution type.
 *
 * Responsibilities:
 * - Parse and normalize input dates
 * - Apply caching for performance optimization
 * - Delegate data fetching to repository layer
 * - Transform raw data into structured DTOs
 *
 * This service acts as the orchestration layer between Controller and Repository.
 */
class WorkTaskReportService
{
    /**
     *
     * @param WorkTaskRepository $workTaskRepo Handles database queries
     * @param DataCacheService $dataCacheService Handles caching logic
     */
    public function __construct(
        private WorkTaskRepository $workTaskRepo,
        private DataCacheService $dataCacheService
    )
    {}

    /**
     * DTO-based entry point (cleaner API)
     */
    public function getReportOnResolutionDTO(WorkTaskReportRequestDTO $requestDto): Collection
    {
        $cacheKey = "report:v1:resolution:{$requestDto->from}:{$requestDto->to}";
        $data = $this->dataCacheService->rememberData(
            $cacheKey,
            fn () => $this->getWorkTasksOnResolution(
                $requestDto->fromDate(),
                $requestDto->toDate()
            )->toArray() 
        );

        return collect($data);
    }

    /**
     * Fetch and transform report data
     */
    private function getWorkTasksOnResolution(Carbon $from, Carbon $to): Collection
    {
        $results = $this->workTaskRepo->getResolutionTypeReport($from, $to);

        return $this->returnValidReportData($results);
    }

     /**
     * Convert raw DB results into API-ready arrays
     *
     * @param Collection<int, object> $results
     * @return Collection<int, array>
     */
    private function returnValidReportData(Collection $results): Collection
    {
        return $results
        ->map(
            fn ($task): array =>
                WorkTaskReportOnResolutionReportDTO::fromTask($task)->toArray()
        )
        ->values();
    }
}