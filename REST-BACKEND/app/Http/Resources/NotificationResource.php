<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for AgencyNotification.
 *
 * Maps the model onto a stable, versioned JSON contract.
 *
 * Conditional relationships:
 *   - 'sender'           — included when the 'sender' relation is loaded
 *   - 'recipients_count' — included when withCount('recipients') was applied
 */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'agency_id'     => $this->agency_id,

            // ── Content ──────────────────────────────────────────────────────
            'title' => $this->title,
            'body'  => $this->body,

            // ── Enum fields ───────────────────────────────────────────────────
            'type'        => $this->type,
            'category'    => $this->category,
            'priority'    => $this->priority,
            'target_role' => $this->target_role,
            'channel'     => $this->channel,

            // ── Status flags ─────────────────────────────────────────────────
            'is_active'    => (bool) $this->is_active,
            'is_dispatched' => (bool) $this->is_dispatched,

            // ── Scheduling ───────────────────────────────────────────────────
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'expires_at'   => $this->expires_at?->toIso8601String(),

            // ── Timestamps ───────────────────────────────────────────────────
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // ── Conditional: sender (load with ->with('sender')) ─────────────
            'sender' => $this->whenLoaded('sender', function () {
                return [
                    'id'    => $this->sender?->id,
                    'name'  => $this->sender?->name,
                    'email' => $this->sender?->email,
                ];
            }),

            // ── Conditional: recipients_count (apply ->withCount('recipients')) ─
            'recipients_count' => $this->whenCounted('recipients'),
        ];
    }
}
