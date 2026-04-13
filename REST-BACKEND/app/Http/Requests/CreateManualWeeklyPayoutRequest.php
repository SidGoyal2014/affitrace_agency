<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateManualWeeklyPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affiliate_id' => ['required', 'integer'],
            'week_start' => ['required', 'date'],
            'week_end' => ['required', 'date', 'after_or_equal:week_start'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:10'],
            'work_units' => ['nullable', 'numeric', 'min:0'],
            'invoice_code' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'entry_type' => ['nullable', Rule::in(['Manual'])],
            'work_basis' => ['nullable', Rule::in(['Manual'])],
        ];
    }
}