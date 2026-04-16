<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkTaskReportRequest
 *
 * Handles validation for Work Task report requests.
 *
 * Validates:
 * - Required date range inputs
 * - Ensures "to" date is not before "from"
 */
class WorkTaskReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['required','date_format:Y-m-d'],
            'to' => ['required','date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

     /**
     * Prepare the data for validation.
     *
     * Normalizes input before validation runs.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'from' => $this->input('from'),
            'to'   => $this->input('to'),
        ]);
    }
}
