<?php

namespace App\DTO;

use App\Http\Requests\WorkTaskReportRequest;
use Carbon\Carbon;

/**
 * Class WorkTaskReportRequestDTO
 *
 * Data Transfer Object for Work Task report requests.
 *
 * Encapsulates validated request data and provides a clean,
 * typed structure for use in the service layer.
 */
final class WorkTaskReportRequestDTO
{
    /**
     * @param string $from Start date (Y-m-d)
     * @param string $to   End date (Y-m-d)
     */
    public function __construct(
        public string $from,
        public string $to,
    )
    {}

    /**
     * Create DTO from validated request
     *
     * @param WorkTaskReportRequest $request
     *
     * @return self
     */
    public static function fromRequest(WorkTaskReportRequest $request): self
    {
        return new self(
            from: $request->validated()['from'],
            to: $request->validated()['to'],
        );
    }

    /**
     * Create DTO from array
     *
     * @param array{from:string,to:string} $data
    */
    public static function fromArray(array $data): self
    {
        return new self(
            from: $data['from'],
            to: $data['to'],
        );
    }

    public function fromDate(): Carbon
    {
        return Carbon::parse($this->from)->startOfDay();
    }

    public function toDate(): Carbon
    {
        return Carbon::parse($this->to)->endOfDay();
    }
}
