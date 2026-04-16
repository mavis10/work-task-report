<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ResolutionType
 *
 * Represents the resolution outcome of a WorkTask.
 *
 * Responsibilities:
 * - Stores resolution metadata (name, description)
 * - Defines relationship with WorkTasks
 *
 * Examples:
 * - Fix Complete – Parts Collection Required
 * - Awaiting Purchase Order
 * - Call on Hold
 */
class ResolutionType extends Model
{
    use HasFactory;

     protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all work tasks associated with this resolution type
     *
     * @return HasMany
     */
    public function workTasks(): HasMany
    {
        return $this->hasMany(WorkTask::class);
    }
}
