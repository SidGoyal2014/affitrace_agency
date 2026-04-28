<?php

/**
 * notifications-tinker-script.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Laravel Tinker script for manual testing of the Notifications system.
 *
 * HOW TO RUN:
 *   cd REST-BACKEND
 *   php artisan tinker
 *   >>> require base_path('docs/notifications-tinker-script.php');
 *
 * Or run all steps in one hit:
 *   php artisan tinker --execute="require base_path('docs/notifications-tinker-script.php');"
 *
 * PREREQUISITES:
 *   1. Migrations ran successfully (agency_notifications + agency_notification_recipients tables exist)
 *   2. At least 3 users exist in the tenant DB
 *   3. QUEUE_CONNECTION in .env is set to 'sync' for instant dispatch during testing,
 *      OR a queue worker is running (`php artisan queue:work`) if QUEUE_CONNECTION=database/redis
 *
 * TENANT NOTE:
 *   This script switches the DB connection to a sample tenant DB. Change
 *   TENANT_DB_NAME below to match a real database name in your pgsql_main.tenants table.
 */

// ─────────────────────────────────────────────────────────────────────────────
// CONFIGURATION — edit these values before running
// ─────────────────────────────────────────────────────────────────────────────
$TENANT_DB   = 'agency_demo';     // Change to your tenant's database name
$AGENCY_ID   = 'agency_demo';     // Matches X-Agency-ID header / agency username
$ADMIN_USER_ID = 1;               // The admin/sender user ID that exists in the tenant DB

// ─────────────────────────────────────────────────────────────────────────────
// STEP 0: Switch to tenant DB connection  (mirrors ApiSetTenantConnection middleware)
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 0: Switch to tenant DB connection ===\n";

\Illuminate\Support\Facades\Config::set("database.connections.pgsql.database", $TENANT_DB);
\Illuminate\Support\Facades\DB::purge('pgsql');
\Illuminate\Support\Facades\DB::reconnect('pgsql');

echo "Active DB: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 1: Find (or create) 3 test users
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 1: Locate 3 test users ===\n";

$users = \App\Models\User::limit(3)->get(['id', 'name', 'email']);

if ($users->count() < 3) {
    echo "WARNING: fewer than 3 users found — seeding test users.\n";

    \App\Models\User::factory()->count(3 - $users->count())->create([
        'agency_id' => $AGENCY_ID,
    ]);

    $users = \App\Models\User::limit(3)->get(['id', 'name', 'email']);
}

$userIds = $users->pluck('id')->toArray();
echo "Using user IDs: " . implode(', ', $userIds) . "\n";
foreach ($users as $u) {
    echo "  [{$u->id}] {$u->name} <{$u->email}>\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// STEP 2: Create a test AgencyNotification manually
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 2: Create AgencyNotification ===\n";

$notification = \App\Models\AgencyNotification::create([
    'agency_id'     => $AGENCY_ID,
    'sender_id'     => $ADMIN_USER_ID,
    'title'         => '[Tinker Test] Hello from Affitrace',
    'message'       => 'This notification was created manually via Tinker for testing purposes.',
    'type'          => 'notification',
    'category'      => 'general',
    'priority'      => 'normal',
    'target_role'   => 'all',
    'channel'       => 'in-app',
    'is_active'     => true,
    'is_dispatched' => false,
    'scheduled_at'  => null,
    'expires_at'    => null,
]);

echo "Created AgencyNotification ID: {$notification->id}\n";
echo "  title:      {$notification->title}\n";
echo "  agency_id:  {$notification->agency_id}\n";
echo "  dispatched: " . ($notification->is_dispatched ? 'yes' : 'no') . "\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 3: Dispatch the notification to the 3 test users
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 3: Dispatch to 3 users (IDs: " . implode(', ', $userIds) . ") ===\n";

/** @var \App\Services\NotificationDispatchService $dispatcher */
$dispatcher = app(\App\Services\NotificationDispatchService::class);

$dispatcher->send($notification, $userIds);

// Refresh to get updated is_dispatched flag
$notification->refresh();
echo "is_dispatched after send(): " . ($notification->is_dispatched ? 'TRUE ✓' : 'FALSE — check dispatch service') . "\n";

// Verify recipient rows were created
$recipientCount = \App\Models\AgencyNotificationRecipient::where(
    function($q) use ($notification) {
        $q->where('notification_id', $notification->id)
          ->orWhere('agency_notification_id', $notification->id);
    }
)->count();

echo "AgencyNotificationRecipient rows created: {$recipientCount} (expected: " . count($userIds) . ")\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 4: Check unread count for each test user
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 4: Check unread counts ===\n";

foreach ($userIds as $uid) {
    $unread = $dispatcher->getUnreadCount($uid);
    echo "  User ID {$uid}: {$unread} unread notification(s)\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// STEP 5: Simulate inbox read for first user
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 5: Mark notification as read for user ID {$userIds[0]} ===\n";

$dispatcher->markAsRead($notification->id, $userIds[0]);

$unreadAfter = $dispatcher->getUnreadCount($userIds[0]);
echo "  User ID {$userIds[0]} unread count after markAsRead(): {$unreadAfter}\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 6: Broadcast to ALL users in tenant
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 6: Create + broadcast to ALL users ===\n";

$broadcastNotif = \App\Models\AgencyNotification::create([
    'agency_id'     => $AGENCY_ID,
    'sender_id'     => $ADMIN_USER_ID,
    'title'         => '[Tinker Test] Broadcast to All',
    'message'       => 'This was sent to every user in the tenant database.',
    'type'          => 'broadcast',
    'category'      => 'general',
    'priority'      => 'low',
    'target_role'   => 'all',
    'channel'       => 'in-app',
    'is_active'     => true,
    'is_dispatched' => false,
]);

$dispatcher->broadcastToAll($broadcastNotif);
$broadcastNotif->refresh();

$totalUsers   = \App\Models\User::count();
$totalRecips  = \App\Models\AgencyNotificationRecipient::where(
    function($q) use ($broadcastNotif) {
        $q->where('notification_id', $broadcastNotif->id)
          ->orWhere('agency_notification_id', $broadcastNotif->id);
    }
)->count();

echo "  Total users in DB:  {$totalUsers}\n";
echo "  Recipient rows:     {$totalRecips}\n";
echo "  Match: " . ($totalUsers === $totalRecips ? 'YES ✓' : "NO — mismatch! check broadcastToAll()") . "\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 7: Test pendingDispatch scope (for scheduler)
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== STEP 7: Create scheduled notification + verify pendingDispatch scope ===\n";

$scheduledNotif = \App\Models\AgencyNotification::create([
    'agency_id'     => $AGENCY_ID,
    'sender_id'     => $ADMIN_USER_ID,
    'title'         => '[Tinker Test] Scheduled — past due',
    'message'       => 'This was scheduled in the past and should appear in pendingDispatch().',
    'type'          => 'alert',
    'category'      => 'general',
    'priority'      => 'normal',
    'target_role'   => 'all',
    'channel'       => 'in-app',
    'is_active'     => true,
    'is_dispatched' => false,
    'scheduled_at'  => now()->subMinutes(5),  // 5 minutes in the past
]);

$pending = \App\Models\AgencyNotification::pendingDispatch()->get();
echo "  pendingDispatch() count: {$pending->count()} (should include ID {$scheduledNotif->id})\n";
echo "  IDs in pending: " . $pending->pluck('id')->implode(', ') . "\n";

// ─────────────────────────────────────────────────────────────────────────────
// STEP 8: Summary
// ─────────────────────────────────────────────────────────────────────────────
echo "\n=== SUMMARY ===\n";
echo "  AgencyNotification rows created this session:\n";
echo "    - Targeted dispatch: ID {$notification->id}\n";
echo "    - Broadcast to all:  ID {$broadcastNotif->id}\n";
echo "    - Scheduled (past):  ID {$scheduledNotif->id}\n";
echo "\n  Next steps:\n";
echo "    php artisan notifications:process-scheduled   -- dispatches scheduledNotif ID {$scheduledNotif->id}\n";
echo "    php artisan notifications:subscription-expiry -- scans all tenants for expiring subscriptions\n";
echo "    php artisan queue:work                         -- only needed if QUEUE_CONNECTION != sync\n";
echo "\nDone.\n";
