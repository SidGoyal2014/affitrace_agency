<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait TimezoneHelper
{
    /**
     * Get the timezone of the currently authenticated user.
     * Fallback to UTC if not logged in or not set.
     */
    public function getUserTimezone()
    {
        if (Auth::check() && Auth::user()->timezone) {
            return Auth::user()->timezone;
        }
        return config('app.timezone', 'UTC');
    }

    /**
     * Given a local user date (e.g., '2026-04-05'), create the absolute UTC start boundary.
     * 'America/New_York' -> '2026-04-05' becomes '2026-04-05 04:00:00' UTC.
     */
    public function getUtcStartOfDay($dateStr)
    {
        if (!$dateStr) return null;
        return Carbon::parse($dateStr, $this->getUserTimezone())
            ->startOfDay()
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');
    }

    /**
     * Given a local user date (e.g., '2026-04-05'), create the absolute UTC end boundary.
     * 'America/New_York' -> '2026-04-05' becomes '2026-04-06 03:59:59' UTC.
     */
    public function getUtcEndOfDay($dateStr)
    {
        if (!$dateStr) return null;
        return Carbon::parse($dateStr, $this->getUserTimezone())
            ->endOfDay()
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');
    }

    /**
     * Given any database UTC timestamp, quickly format it to the user's local timezone.
     */
    public function formatUtcToLocal($utcTimestamp, $format = 'd-m-Y H:i:s')
    {
        if (!$utcTimestamp) return null;
        return Carbon::parse($utcTimestamp, 'UTC')
            ->setTimezone($this->getUserTimezone())
            ->format($format);
    }
}
