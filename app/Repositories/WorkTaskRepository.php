<?php

namespace App\Repositories;

use App\Models\Call;
use App\Models\WorkTask;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class WorkTaskRepository
 *
 * Handles data access for WorkTask reporting.
 */
class WorkTaskRepository
{
    public function getResolutionTypeReport(Carbon $from, Carbon $to): Collection
    {
       return WorkTask::query()
        ->join('calls', 'work_tasks.call_id', '=', 'calls.id')
        ->leftJoin('resolution_types', 'work_tasks.resolution_type_id', '=', 'resolution_types.id')

        // Filters
        ->whereBetween('work_tasks.created_at', [$from, $to])
        ->whereNotNull('work_tasks.resolution_type_id')
        ->whereNotIn('calls.stage', [
            Call::STAGE_DRAFT,
            Call::STAGE_ARCHIVED,
        ])

        // Aggregation
        ->selectRaw('
            resolution_types.id,
            resolution_types.name,
            resolution_types.description,
            COUNT(work_tasks.id) as count
        ')
        ->groupBy(
            'resolution_types.id',
            'resolution_types.name',
            'resolution_types.description'
        )

        ->orderByDesc('count')
        ->get();
    }
}

