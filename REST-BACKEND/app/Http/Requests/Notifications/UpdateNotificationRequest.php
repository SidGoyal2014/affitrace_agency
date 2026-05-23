<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the payload for updating an existing (not-yet-dispatched) notification.
 *
 * All fields are optional (use 'sometimes') — only provided fields are updated.
 *
 * Enum reference (from agency_notifications migration):
 *   type:        notification | news | alert | broadcast
 *   category:    offer | payment | transaction | subscription | general
 *   priority:    low | normal | high | urgent
 *   target_role: affiliate | advertiser | employee | all
 *   channel:     in-app | email
 */
class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Core fields ──────────────────────────────────────────────────
            'title'   => ['sometimes', 'string', 'max:255'],
            // TODO: Align column name 'body' (migration) vs 'message' (model fillable).
            'message' => ['sometimes', 'string'],

            // ── Enum-constrained fields ──────────────────────────────────────
            'type' => [
                'sometimes',
                Rule::in(['notification', 'news', 'alert', 'broadcast']),
            ],
            'category' => [
                'sometimes',
                Rule::in(['offer', 'payment', 'transaction', 'subscription', 'general']),
            ],
            'priority' => [
                'sometimes',
                'nullable',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
            'target_role' => [
                'sometimes',
                'nullable',
                Rule::in(['affiliate', 'advertiser', 'employee', 'all']),
            ],
            'channel' => [
                'sometimes',
                'nullable',
                Rule::in(['in-app', 'email']),
            ],

            // ── Scheduling ───────────────────────────────────────────────────
            'scheduled_at' => ['sometimes', 'nullable', 'date'],
            'expires_at'   => ['sometimes', 'nullable', 'date'],

            // ── Misc ─────────────────────────────────────────────────────────
            // TODO: Uncomment 'data' when the DB column is confirmed to exist.
            // 'data' => ['sometimes', 'nullable', 'array'],

            'is_active' => ['sometimes', 'boolean'],
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
