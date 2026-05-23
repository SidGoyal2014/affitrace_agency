# Notifications System — Manual Test Checklist & Troubleshooting Guide

> Generated for: Affitrace Agency — Laravel 10 Multi-Tenant SaaS
> Tested against: `REST-BACKEND/app/Http/Controllers/Api/V1/NotificationController.php`
> Last updated: 2026-04-19

---

## Prerequisites

Before running any test:

- [ ] Migrations executed: `php artisan migrate` — tables `agency_notifications` and `agency_notification_recipients` exist
- [ ] At least 3 users exist in the tenant DB (affiliates, advertisers, admin)
- [ ] `.env` has `NOTIFICATION_TRIGGER_SECRET=<some-strong-secret>`
- [ ] Queue worker running OR `QUEUE_CONNECTION=sync` for synchronous dispatch:
  ```
  php artisan queue:work --queue=default
  ```
- [ ] Scheduler running (for cron tests):
  ```
  php artisan schedule:work   # local dev
  ```

---

## Test 1 — Send to All Affiliates

**Endpoint:** `POST /api/admin/v1/notifications`
**Auth:** Admin token + `X-Agency-ID` header

### Steps
1. Login as admin and capture the bearer token
2. Send request:
   ```json
   {
     "title":       "Affiliate Program Update",
     "message":     "New payout rules are effective from May 1.",
     "type":        "news",
     "category":    "offer",
     "priority":    "normal",
     "target_role": "affiliate",
     "channel":     "in-app",
     "is_active":   true
   }
   ```
3. Note the returned `notification.id`

### Verification
- [ ] HTTP 201 returned
- [ ] `success: true` in response
- [ ] `is_dispatched: true` on the returned notification resource
- [ ] In DB: `SELECT COUNT(*) FROM agency_notification_recipients WHERE notification_id = <id>;`
  — count must equal total affiliate users in the tenant
- [ ] Login as any affiliate → `GET /api/v1/notifications/unread-count` → count increased by 1
- [ ] `GET /api/v1/notifications/inbox` as that affiliate → notification appears with `is_read: false`

---

## Test 2 — Send to One Specific User

**Endpoint:** `POST /api/admin/v1/notifications`

### Steps
1. Send request with `recipient_ids` — replaces `target_role` broadcast:
   ```json
   {
     "title":         "Personal Message",
     "message":       "Your affiliate account has been reviewed.",
     "type":          "notification",
     "category":      "general",
     "priority":      "high",
     "channel":       "in-app",
     "is_active":     true,
     "recipient_ids": [<target_user_id>]
   }
   ```

### Verification
- [ ] HTTP 201 returned
- [ ] Exactly 1 row in `agency_notification_recipients` for that notification
- [ ] Unread count for the specific user increments by 1
- [ ] Other users' unread counts are **unchanged**
- [ ] Login as target user → inbox shows the notification with correct title/message

---

## Test 3 — Schedule a Notification 2 Minutes in the Future

### Part A: Create the scheduled notification via API

**Endpoint:** `POST /api/admin/v1/notifications`

1. Get current UTC time + 2 minutes, format as ISO 8601: e.g. `2026-04-19T12:02:00Z`
2. Send:
   ```json
   {
     "title":        "Scheduled Test Notification",
     "message":      "This was dispatched by the scheduler.",
     "type":         "alert",
     "category":     "general",
     "priority":     "normal",
     "target_role":  "all",
     "channel":      "in-app",
     "is_active":    true,
     "scheduled_at": "2026-04-19T12:02:00Z"
   }
   ```
3. Note the `notification.id` from the response

### Verification — immediately after creation
- [ ] `is_dispatched: false` in response (not yet dispatched)
- [ ] In Tinker: `\App\Models\AgencyNotification::pendingDispatch()->count()` returns 0 (too early)
- [ ] In DB: `SELECT * FROM agency_notifications WHERE id = <id>` — `scheduled_at` is correct

### Part B: Wait 2 minutes, then run the scheduler command

```bash
php artisan notifications:process-scheduled
```

### Verification — after running scheduler
- [ ] Console output shows: `Processing tenant: <your_db_name>` and dispatched count > 0
- [ ] In DB: `SELECT is_dispatched FROM agency_notifications WHERE id = <id>;` → `true`
- [ ] `agency_notification_recipients` rows exist for the notification
- [ ] Any recipient user's unread count incremented
- [ ] In Tinker: `\App\Models\AgencyNotification::pendingDispatch()->count()` returns 0 again

### Shortcut (skip waiting — simulate past schedule in Tinker)
```php
// In php artisan tinker:
\App\Models\AgencyNotification::find(<id>)->update(['scheduled_at' => now()->subMinutes(5)]);
// Then run the command manually
```

---

## Test 4 — Trigger Subscription Expiry Cron Manually

### Method A: Artisan command (direct)

```bash
php artisan notifications:subscription-expiry
```

### Verification
- [ ] Output: `Scanning subscriptions expiring soon...`
- [ ] Output: `Checking tenant: <db_name>` for each tenant
- [ ] Notifications created for subscriptions expiring in 1, 3, and 7 days
- [ ] Each affected user has new unread notifications
- [ ] No duplicate notifications for the same subscription + threshold day

### Method B: HTTP trigger endpoint

**Endpoint:** `POST /api/v1/notifications/triggers/subscription-expiry`

Headers required:
```
Authorization: Bearer <valid_token>
X-Agency-ID: <agency_id>
X-Notification-Secret: <value_matching_NOTIFICATION_TRIGGER_SECRET_in_env>
```

Body (optional custom message):
```json
{
  "message": "Your subscription expires in 3 days. Renew at /billing."
}
```

### Verification
- [ ] HTTP 201 returned with `notification_id`
- [ ] Log entry in `storage/logs/laravel.log`: `triggerSubscriptionExpiry — notification X dispatched`
- [ ] All users in the tenant have a new unread notification in their inbox
- [ ] **403 scenario**: Send with wrong/missing `X-Notification-Secret` → must return HTTP 403

---

## Test 5 — Mark All Notifications as Read

**Endpoint:** `PATCH /api/v1/notifications/inbox/read-all`
**Auth:** User token (any authenticated user)

### Steps
1. First, ensure the user has unread notifications (run tests 1–4 first for the user)
2. Confirm unread count > 0: `GET /api/v1/notifications/unread-count`
3. Send: `PATCH /api/v1/notifications/inbox/read-all` (no body needed)

### Verification
- [ ] HTTP 200 returned
- [ ] `{ "success": true, "message": "All notifications marked as read." }`
- [ ] Immediately after: `GET /api/v1/notifications/unread-count` → `unread_count: 0`
- [ ] `GET /api/v1/notifications/inbox?is_read=false` → empty `data` array
- [ ] `GET /api/v1/notifications/inbox?is_read=true` → all previous notifications listed
- [ ] In DB: `SELECT COUNT(*) FROM agency_notification_recipients WHERE recipient_id = <uid> AND is_read = false;` → 0

---

## Test 6 — Update a Pre-Dispatch Notification

**Endpoint:** `PUT /api/admin/v1/notifications/sent/{id}`

1. Create a scheduled notification (use Test 3 body but set `scheduled_at` 1 hour ahead)
2. Note its ID (`is_dispatched` = false)
3. Send update:
   ```json
   {
     "title":    "Updated Title",
     "message":  "Revised content before dispatch.",
     "priority": "urgent"
   }
   ```

### Verification
- [ ] HTTP 200 returned with updated fields
- [ ] Attempt the same update on an already-dispatched notification → HTTP 422 with message about `dispatched`

---

## Test 7 — Delete a Notification

**Endpoint:** `DELETE /api/admin/v1/notifications/sent/{id}`

1. Create a test notification
2. Note its ID
3. Send DELETE

### Verification
- [ ] HTTP 200 returned: `{ "success": true, "message": "Notification deleted successfully." }`
- [ ] `GET /api/admin/v1/notifications/sent/{id}` → HTTP 404
- [ ] Recipient rows in `agency_notification_recipients` removed (check cascade delete in migration)
- [ ] Non-existent ID → HTTP 404

---

## Quick curl Commands Reference

Replace `<TOKEN>`, `<AGENCY_ID>`, and `<BASE>` with real values.

```bash
BASE="http://localhost/affitrace_agency/REST-BACKEND/public/api"
TOKEN="<your_admin_bearer_token>"
AGENCY="agency_demo"
SECRET="<NOTIFICATION_TRIGGER_SECRET>"

# Login and capture token
curl -s -X POST "$BASE/login" \
  -H "Content-Type: application/json" \
  -H "X-Agency-ID: $AGENCY" \
  -d '{"email":"admin@example.com","password":"password"}'

# B1 — Send broadcast notification
curl -s -X POST "$BASE/admin/v1/notifications" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY" \
  -d '{"title":"Test","message":"Hello all","type":"news","category":"general"}'

# B4 — List sent notifications
curl -s "$BASE/admin/v1/notifications/sent?per_page=5" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY"

# A1 — Unread count
curl -s "$BASE/v1/notifications/unread-count" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY"

# A2 — Inbox
curl -s "$BASE/v1/notifications/inbox?is_read=false" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY"

# A5 — Mark all read
curl -s -X PATCH "$BASE/v1/notifications/inbox/read-all" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY"

# C1 — Trigger subscription expiry
curl -s -X POST "$BASE/v1/notifications/triggers/subscription-expiry" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Agency-ID: $AGENCY" \
  -H "X-Notification-Secret: $SECRET" \
  -d '{"message":"Subscription expiring in 3 days."}'
```

---

## Common Errors and Fixes

### Error 1: HTTP 500 — Column `body` does not exist

**Symptom:** Any write to `agency_notifications` throws a PostgreSQL column-not-found error.

**Cause:** The model's `$fillable` uses `message` but the migration may have created the column as `body`, or vice versa.

**Fix:**
1. Check the actual migration: look for `$table->text('body')` vs `$table->text('message')` in the `agency_notifications` migration.
2. If the column is `body`:
   - Either rename the column: `php artisan make:migration rename_body_to_message_in_agency_notifications`
   - Or update the model `$fillable` and all controller references to use `body`.
3. There is a TODO comment in `NotificationController::send()` and `SendNotificationRequest` flagging this mismatch.

---

### Error 2: HTTP 403 on Trigger Endpoints

**Symptom:** `POST /api/v1/notifications/triggers/*` returns `{"success":false,"message":"Unauthorized."}`

**Causes & Fixes:**

| Cause | Fix |
|-------|-----|
| `NOTIFICATION_TRIGGER_SECRET` not set in `.env` | Add `NOTIFICATION_TRIGGER_SECRET=your-strong-secret` to `.env` and run `php artisan config:clear` |
| Wrong secret value in request header | Ensure `X-Notification-Secret` header value exactly matches the `.env` value |
| `.env` cached | `php artisan config:clear && php artisan cache:clear` |
| Log warning: "NOTIFICATION_TRIGGER_SECRET is not configured" | Check `storage/logs/laravel.log` for this warning |

---

### Error 3: Unread Count Never Decrements / markAsRead has No Effect

**Symptom:** `getUnreadCount()` always returns the same value after calling `markAsRead()`.

**Cause:** Column name mismatch — migration may have created FK as `notification_id` but the service queries `agency_notification_id`.

**Fix:**
1. Check `agency_notification_recipients` table: `\Schema::getColumnListing('agency_notification_recipients')` in Tinker.
2. Open `NotificationDispatchService::markAsRead()` and verify the FK column name matches.
3. There is a TODO comment in `NotificationController::inbox()` flagging this exact issue.

---

### Error 4: Notifications Not Appearing in Inbox (Missing recipient rows)

**Symptom:** Notification created, `is_dispatched` = true, but recipient user sees empty inbox.

**Causes & Fixes:**

| Cause | Fix |
|-------|-----|
| `agency_id` is empty string `""` on created notification | Ensure `X-Agency-ID` header is sent on all requests; check that `ApiSetTenantConnection` middleware is applied to the route group |
| Queue worker not running | Set `QUEUE_CONNECTION=sync` in `.env` **for testing**, or run `php artisan queue:work` |
| Job failed silently | Check `failed_jobs` table: `\DB::table('failed_jobs')->latest()->first()` in Tinker |
| `broadcastToAll()` queries wrong tenant DB | Verify tenant DB switch happened before calling the service (check middleware order in route groups) |

---

### Error 5: Scheduled Notifications Not Dispatching

**Symptom:** `php artisan notifications:process-scheduled` runs but dispatches 0 notifications.

**Causes & Fixes:**

| Cause | Fix |
|-------|-----|
| `scheduled_at` stored in wrong timezone | Store in UTC: `now()->utc()->addMinutes(2)` or use Carbon with `->setTimezone('UTC')` |
| `is_active` is `false` | `pendingDispatch()` scope requires `is_active = true` |
| `is_dispatched` already `true` | Notification was already processed |
| Tenant not found in `pgsql_main.tenants` | Command queries `pgsql_main` connection; verify the main DB `tenants` table has the tenant row |
| Scope query: `scheduled_at <= now()` not matching | Verify DB server time is correct: `\DB::select('SELECT NOW()')` |

---

### Error 6: Missing `agency_id` on New Records

**Symptom:** Notifications created without `agency_id` — cross-tenant data leaks in reports.

**Root Cause:** `X-Agency-ID` header not passed, or middleware not applied to the route.

**Fix:**
1. All new routes must be inside the `auth:sanctum` group which is paired with `ApiSetTenantConnection` middleware (check `app/Http/Kernel.php` for the `api` middleware group).
2. Always verify: `$agencyId = (string) $request->header('X-Agency-ID', '');`
3. Add a DB-level check in tests: `\App\Models\AgencyNotification::whereNull('agency_id')->count()` must be 0.

---

### Error 7: 419 / CSRF Token Mismatch on PATCH routes

**Symptom:** `PATCH /api/v1/notifications/inbox/read-all` returns 419.

**Cause:** API routes are exempt from CSRF but only if they are under the `api` middleware group defined in `Kernel.php`.

**Fix:** Confirm the route file is `routes/api.php` (not `routes/web.php`), and the route group uses `middleware('api')` or is handled by `bootstrap/app.php` route loading.

---

### Error 8: Queue Worker Not Processing Dispatch Jobs

**Symptom:** HTTP 201 returned, but users never receive notifications (inbox stays empty).

**Quick Fix for Development:**
```ini
# .env
QUEUE_CONNECTION=sync
```

**Production Fix:**
```bash
php artisan queue:work --queue=default --tries=3 --timeout=60
# Or with Supervisor for persistent worker
```

Check failed jobs:
```bash
php artisan queue:failed
php artisan queue:retry all
```

---

### Error 9: Subscription Expiry Cron Sends Duplicate Notifications

**Symptom:** Running `notifications:subscription-expiry` multiple times sends duplicate reminders.

**Cause:** The command may not track which reminders were already sent for a given subscription + threshold.

**Workaround (until dedup logic is added):**
- Check the command's TODO comments
- Add a database flag or Redis key to track sent reminders per subscription + days_ahead combination
- Only run the cron once per day via the scheduler (not manually multiple times in testing)

---

## Artisan Reference

```bash
# Run scheduler once (all due tasks)
php artisan schedule:run

# Run scheduler in dev (watches for due tasks every minute)
php artisan schedule:work

# Process scheduled notifications immediately
php artisan notifications:process-scheduled

# Run subscription expiry check across all tenants
php artisan notifications:subscription-expiry

# Clear all caches after .env changes
php artisan optimize:clear

# Check registered scheduled commands
php artisan schedule:list
```

---

## Endpoint Summary Table

| # | Method | Path | Auth | Purpose |
|---|--------|------|------|---------|
| A1 | GET | `/api/v1/notifications/unread-count` | Bearer | Count of unread inbox items |
| A2 | GET | `/api/v1/notifications/inbox` | Bearer | Paginated inbox (filterable) |
| A3 | GET | `/api/v1/notifications/inbox/{id}` | Bearer | Open item (auto-marks read) |
| A4 | PATCH | `/api/v1/notifications/inbox/{id}/read` | Bearer | Mark one item read |
| A5 | PATCH | `/api/v1/notifications/inbox/read-all` | Bearer | Mark all items read |
| B1 | POST | `/api/admin/v1/notifications` | Admin | Create + dispatch notification |
| B2 | GET | `/api/admin/v1/notifications/sent` | Admin | Paginated sent list |
| B3 | GET | `/api/admin/v1/notifications/sent/{id}` | Admin | Single sent notification |
| B4 | PUT | `/api/admin/v1/notifications/sent/{id}` | Admin | Update (pre-dispatch only) |
| B5 | DELETE | `/api/admin/v1/notifications/sent/{id}` | Admin | Delete notification |
| C1 | POST | `/api/v1/notifications/triggers/subscription-expiry` | Bearer + Secret | System trigger: subscription |
| C2 | POST | `/api/v1/notifications/triggers/payment-reminder` | Bearer + Secret | System trigger: payment |
| C3 | POST | `/api/v1/notifications/triggers/custom` | Bearer + Secret | System trigger: custom |
