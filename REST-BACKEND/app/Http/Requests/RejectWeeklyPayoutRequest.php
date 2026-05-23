<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectWeeklyPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payout_id' => ['required', 'integer'],
            'rejected_reason' => ['nullable', 'string'],
        ];
    }
}