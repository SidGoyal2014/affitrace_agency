<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayWeeklyPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payout_ids' => ['required', 'array', 'min:1'],
            'payout_ids.*' => ['integer', 'distinct'],
            'payment_mode' => ['required', Rule::in(['Manual', 'Online'])],
            'payment_from' => ['required', Rule::in(['Agency'])],
            'payment_to' => ['required', Rule::in(['Affiliate'])],
            'transaction_amount' => ['required', 'numeric', 'min:0.01'],
            'activity_date' => ['nullable', 'date'],
            'transaction_code' => ['nullable', 'string', 'max:100'],
            'manual_invoice_no' => ['nullable', 'string', 'max:100'],
            'transaction_details' => ['nullable', 'string'],
        ];
    }
}