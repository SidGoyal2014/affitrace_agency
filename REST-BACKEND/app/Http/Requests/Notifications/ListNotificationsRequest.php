<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates query-string parameters for paginated notification list endpoints.
 *
 * Used by both sentIndex() (admin view) and inbox() (recipient view).
 *
 * Enum reference (from agency_notifications migration):
 *   type:     notification | news | alert | broadcast
 *   category: offer | payment | transaction | subscription | general
 *   priority: low | normal | high | urgent
 */
class ListNotificationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Filters ──────────────────────────────────────────────────────
            'type' => [
                'nullable',
                Rule::in(['notification', 'news', 'alert', 'broadcast']),
            ],
            'category' => [
                'nullable',
                Rule::in(['offer', 'payment', 'transaction', 'subscription', 'general']),
            ],
            'priority' => [
                'nullable',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],

            // ── Inbox-specific filter ────────────────────────────────────────
            'is_read' => ['nullable', 'boolean'],

            // ── Sender-list filter (sentIndex only) ──────────────────────────
            'is_dispatched' => ['nullable', 'boolean'],

            // ── Pagination ───────────────────────────────────────────────────
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],

            // ── Date range filter ────────────────────────────────────────────
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],            
            
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'     => 'The type must be one of: notification, news, alert, broadcast.',
            'category.in' => 'The category must be one of: offer, payment, transaction, subscription, general.',
            'priority.in' => 'The priority must be one of: low, normal, high, urgent.',
            'per_page.max' => 'You may request a maximum of 100 records per page.',
        ];
    }
}
