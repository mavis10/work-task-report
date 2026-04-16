<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WorkTask
 *
 * Represents a unit of work performed for a Call.
 *
 * Responsibilities:
 * - Links a Call to a ResolutionType
 * - Tracks work start and completion times
 *
 * Relationships:
 * - Belongs to Call
 * - Belongs to ResolutionType
 */
class WorkTask extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'call_id',
        'resolution_type_id',
        'work_started_at',
        'work_completed_at',
    ];

    /**
     * Attribute casting
     *
     * @var array<string, string>
    */
    protected $casts = [
        'work_started_at' => 'datetime',
        'work_completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the call associated with this work task
     *
     * @return BelongsTo
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get the resolution type associated with this work task
     *
     * @return BelongsTo
    */
    public function resolutionType(): BelongsTo
    {
        return $this->belongsTo(ResolutionType::class);
    }
}
