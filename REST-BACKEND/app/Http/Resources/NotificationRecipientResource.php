<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for AgencyNotificationRecipient.
 *
 * Represents a single inbox row for a recipient, optionally embedding
 * the parent notification via the 'notification' eager-loaded relation.
 *
 * Usage:
 *   new NotificationRecipientResource($recipientModel)
 *   NotificationRecipientResource::collection($paginator->items())
 *
 * Conditional relationship:
 *   - 'notification' — included when ->with('notification') was applied on the query
 */
class NotificationRecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,

            // TODO: The migration creates a foreign key column named 'notification_id',
            //       but the model and service reference 'agency_notification_id'.
            //       Exposing the resolved ID regardless of column name:
            'notification_id' => $this->attributes['agency_notification_id']
                ?? $this->attributes['notification_id']
                ?? null,

            'recipient_id' => $this->recipient_id,

            // ── Read state ────────────────────────────────────────────────────
            'is_read' => (bool) $this->is_read,
            'read_at' => $this->read_at?->toIso8601String(),

            // ── Dismissal state (if column exists in DB) ──────────────────────
            // TODO: 'is_dismissed', 'dismissed_at' are in model fillable but absent
            //       from the current migration. Expose once column is confirmed.
            // 'is_dismissed' => (bool) ($this->is_dismissed ?? false),
            // 'dismissed_at' => $this->dismissed_at?->toIso8601String(),

            // ── Delivery state ────────────────────────────────────────────────
            // TODO: 'delivery_status' and 'delivered_at' are in model fillable but
            //       absent from the current migration. Expose once confirmed.
            // 'delivery_status' => $this->delivery_status,
            // 'delivered_at'    => $this->delivered_at?->toIso8601String(),

            // ── Timestamps ───────────────────────────────────────────────────
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // ── Conditional: parent notification ─────────────────────────────
            // Only serialised when ->with('notification') was applied.
            'notification' => $this->whenLoaded(
                'notification',
                fn () => new NotificationResource($this->notification)
            ),
        ];
    }
}
