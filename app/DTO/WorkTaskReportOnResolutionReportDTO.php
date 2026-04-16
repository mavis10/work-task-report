<?php

namespace App\DTO;

/**
 * Class WorkTaskReportOnResolutionReportDTO
 *
 * Data Transfer Object for resolution-based work task reports.
 *
 * Represents aggregated report data:
 * - Resolution type details
 * - Total count of associated work tasks
 */
final class WorkTaskReportOnResolutionReportDTO
{
    /**
     * @param int         $id
     * @param string      $name
     * @param string|null $description
     * @param int         $count
    */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public int $count
    )
    {}

    /**
     * Create DTO from raw query result
     *
     * @param object $task
     *
     * @return self
     */
    public static function fromTask(object $task): self
    {
        return new self(
            id: (int) $task->id,
            name: (string) $task->name,
            description: $task->description ?? null,
            count: (int) $task->count
        );
    }

    /**
     * Convert DTO to array (API-friendly format)
     *
     * @return array<string, int|string|null>
     */
    public function toArray()
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'count' =>  $this->count
        ];
    }

    /**
     * JSON serialization
    */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
