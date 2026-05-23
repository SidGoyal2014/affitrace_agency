<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarkInvoicePaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth is handled by route middleware (sanctum + role/module guards)
    }

    public function rules(): array
    {
        return [
            'payment_mode'        => ['required', Rule::in(['Manual', 'Online'])],
            'payment_from'        => ['required', Rule::in(['Advertiser', 'Agency'])],
            'payment_to'          => ['required', Rule::in(['Agency', 'Affiliate'])],
            'transaction_amount'  => ['required', 'numeric', 'min:0.01'],
            'activity_date'       => ['nullable', 'date'],
            'transaction_code'    => ['nullable', 'string', 'max:100'],
            'manual_invoice_no'   => ['nullable', 'string', 'max:100'],
            'transaction_details' => ['nullable', 'string'],
        ];
    }

    /**
     * For invoice mark-paid the payment direction is always Advertiser → Agency.
     * Reject any other combination so the payload cannot be abused for payout flows.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $from = $this->input('payment_from');
            $to   = $this->input('payment_to');

            if ($from === 'Advertiser' && $to !== 'Agency') {
                $validator->errors()->add(
                    'payment_to',
                    'When payment is from Advertiser, payment must go to Agency.'
                );
            }

            if ($from === 'Agency' && $to !== 'Affiliate') {
                $validator->errors()->add(
                    'payment_to',
                    'Agency can only pay to Affiliate.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'payment_mode.required'       => 'Payment mode is required.',
            'payment_mode.in'             => 'Payment mode must be Manual or Online.',
            'payment_from.required'       => 'Payment from is required.',
            'payment_from.in'             => 'Payment from must be Advertiser or Agency.',
            'payment_to.required'         => 'Payment to is required.',
            'payment_to.in'               => 'Payment to must be Agency or Affiliate.',
            'transaction_amount.required' => 'Transaction amount is required.',
            'transaction_amount.numeric'  => 'Transaction amount must be a number.',
            'transaction_amount.min'      => 'Transaction amount must be greater than zero.',
            'activity_date.date'          => 'Payment date must be a valid date.',
            'transaction_code.max'        => 'Transaction code may not exceed 100 characters.',
            'manual_invoice_no.max'       => 'Manual invoice number may not exceed 100 characters.',
        ];
    }
}
