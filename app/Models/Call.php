<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class Call
 *
 * Represents a service call in the system.
 *
 * Responsibilities:
 * - Stores call metadata (notes, stage)
 * - Defines relationship with WorkTasks
 *
 * Stages:
 * - Open: Active call
 * - Draft: Not yet finalized (excluded from reports)
 * - Archived: Closed/archived (excluded from reports)
 */
class Call extends Model
{
    use HasFactory;

    /**
     * Call stages
     */
    public const STAGE_OPEN = 'Open';
    public const STAGE_DRAFT = 'Draft';
    public const STAGE_ARCHIVED = 'Archived';

    /**
     * Mass assignable attributes
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'notes',
        'stage',
    ];

    /**
     * Get all work tasks associated with the call
     *
     * A call can have multiple work tasks.
     *
     * @return HasMany
     */
    public function workTasks(): HasMany
    {
        return $this->hasMany(WorkTask::class);
    }
}
