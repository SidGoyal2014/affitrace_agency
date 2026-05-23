<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the payload for creating and dispatching a new agency notification.
 *
 * Enum reference (from agency_notifications migration):
 *   type:        notification | news | alert | broadcast
 *   category:    offer | payment | transaction | subscription | general
 *   priority:    low | normal | high | urgent
 *   target_role: affiliate | advertiser | employee | all
 *   channel:     in-app | email
 */
class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gate-level authorization is enforced by the route middleware (admin/employee).
        // All authenticated & role-checked callers are permitted here.
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Required core fields ─────────────────────────────────────────
            'title'   => ['required', 'string', 'max:255'],
            // TODO: The DB column is named 'body' in the migration but 'message'
            //       in the model's fillable. Verify and align before running tests.
            'message' => ['required', 'string'],

            // ── Enum-constrained fields ──────────────────────────────────────
            'type' => [
                'required',
                Rule::in(['notification', 'news', 'alert', 'broadcast']),
            ],
            'category' => [
                'required',
                Rule::in(['offer', 'payment', 'transaction', 'subscription', 'general']),
            ],
            'priority' => [
                'nullable',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
            'target_role' => [
                'nullable',
                Rule::in(['affiliate', 'advertiser', 'employee', 'all']),
            ],
            'channel' => [
                'nullable',
                Rule::in(['in-app', 'email']),
            ],

            // ── Optional recipient targeting ─────────────────────────────────
            // When present, overrides target_role-based broadcast.
            'recipient_ids'   => ['nullable', 'array'],
            'recipient_ids.*' => ['integer', 'min:1'],

            // ── Scheduling ───────────────────────────────────────────────────
            'scheduled_at' => ['nullable', 'date', 'after_or_equal:now'],
            'expires_at'   => ['nullable', 'date', 'after:scheduled_at'],

            // ── Misc ─────────────────────────────────────────────────────────
            // TODO: 'data' column exists in model fillable but is absent from the
            //       current migration. Uncomment when the column is added to the DB.
            // 'data' => ['nullable', 'array'],

            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'        => 'The type must be one of: notification, news, alert, broadcast.',
            'category.in'    => 'The category must be one of: offer, payment, transaction, subscription, general.',
            'priority.in'    => 'The priority must be one of: low, normal, high, urgent.',
            'target_role.in' => 'The target role must be one of: affiliate, advertiser, employee, all.',
            'channel.in'     => 'The channel must be one of: in-app, email.',
        ];
    }
}
