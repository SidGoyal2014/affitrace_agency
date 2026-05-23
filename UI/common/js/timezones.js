/**
 * Timezone list for the timezone settings dropdown.
 * This is the authoritative source of timezone data for the UI.
 * Each key is a valid PHP/IANA timezone identifier; the value is a human-readable label.
 */
const TIMEZONE_LIST = {
    // ── UTC ────────────────────────────────────────────
    "UTC": "(UTC+00:00) UTC",

    // ── Americas ───────────────────────────────────────
    "America/New_York": "(UTC-05:00) Eastern Time – New York",
    "America/Chicago": "(UTC-06:00) Central Time – Chicago",
    "America/Denver": "(UTC-07:00) Mountain Time – Denver",
    "America/Los_Angeles": "(UTC-08:00) Pacific Time – Los Angeles",
    "America/Anchorage": "(UTC-09:00) Alaska – Anchorage",
    "Pacific/Honolulu": "(UTC-10:00) Hawaii – Honolulu",
    "America/Toronto": "(UTC-05:00) Eastern Time – Toronto",
    "America/Vancouver": "(UTC-08:00) Pacific Time – Vancouver",
    "America/Sao_Paulo": "(UTC-03:00) Brasilia – São Paulo",
    "America/Argentina/Buenos_Aires": "(UTC-03:00) Buenos Aires",
    "America/Mexico_City": "(UTC-06:00) Mexico City",
    "America/Bogota": "(UTC-05:00) Bogotá",
    "America/Lima": "(UTC-05:00) Lima",
    "America/Santiago": "(UTC-04:00) Santiago",

    // ── Europe ─────────────────────────────────────────
    "Europe/London": "(UTC+00:00) London",
    "Europe/Paris": "(UTC+01:00) Paris",
    "Europe/Berlin": "(UTC+01:00) Berlin",
    "Europe/Madrid": "(UTC+01:00) Madrid",
    "Europe/Rome": "(UTC+01:00) Rome",
    "Europe/Amsterdam": "(UTC+01:00) Amsterdam",
    "Europe/Brussels": "(UTC+01:00) Brussels",
    "Europe/Zurich": "(UTC+01:00) Zurich",
    "Europe/Vienna": "(UTC+01:00) Vienna",
    "Europe/Stockholm": "(UTC+01:00) Stockholm",
    "Europe/Warsaw": "(UTC+01:00) Warsaw",
    "Europe/Athens": "(UTC+02:00) Athens",
    "Europe/Bucharest": "(UTC+02:00) Bucharest",
    "Europe/Helsinki": "(UTC+02:00) Helsinki",
    "Europe/Istanbul": "(UTC+03:00) Istanbul",
    "Europe/Moscow": "(UTC+03:00) Moscow",
    "Europe/Kiev": "(UTC+02:00) Kyiv",

    // ── Asia ───────────────────────────────────────────
    "Asia/Kolkata": "(UTC+05:30) India – Kolkata",
    "Asia/Dubai": "(UTC+04:00) Dubai",
    "Asia/Riyadh": "(UTC+03:00) Riyadh",
    "Asia/Tehran": "(UTC+03:30) Tehran",
    "Asia/Karachi": "(UTC+05:00) Karachi",
    "Asia/Dhaka": "(UTC+06:00) Dhaka",
    "Asia/Bangkok": "(UTC+07:00) Bangkok",
    "Asia/Jakarta": "(UTC+07:00) Jakarta",
    "Asia/Singapore": "(UTC+08:00) Singapore",
    "Asia/Kuala_Lumpur": "(UTC+08:00) Kuala Lumpur",
    "Asia/Hong_Kong": "(UTC+08:00) Hong Kong",
    "Asia/Shanghai": "(UTC+08:00) Beijing / Shanghai",
    "Asia/Taipei": "(UTC+08:00) Taipei",
    "Asia/Seoul": "(UTC+09:00) Seoul",
    "Asia/Tokyo": "(UTC+09:00) Tokyo",
    "Asia/Manila": "(UTC+08:00) Manila",

    // ── Oceania ────────────────────────────────────────
    "Australia/Sydney": "(UTC+10:00) Sydney",
    "Australia/Melbourne": "(UTC+10:00) Melbourne",
    "Australia/Brisbane": "(UTC+10:00) Brisbane",
    "Australia/Perth": "(UTC+08:00) Perth",
    "Australia/Adelaide": "(UTC+09:30) Adelaide",
    "Pacific/Auckland": "(UTC+12:00) Auckland",
    "Pacific/Fiji": "(UTC+12:00) Fiji",

    // ── Africa ─────────────────────────────────────────
    "Africa/Cairo": "(UTC+02:00) Cairo",
    "Africa/Lagos": "(UTC+01:00) Lagos",
    "Africa/Nairobi": "(UTC+03:00) Nairobi",
    "Africa/Johannesburg": "(UTC+02:00) Johannesburg",
    "Africa/Casablanca": "(UTC+01:00) Casablanca"
};
