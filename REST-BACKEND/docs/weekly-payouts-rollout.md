# Weekly Payouts Rollout and Rollback Runbook

## Feature Switch
- Config key: `reports.weekly_payouts_management_enabled`
- Environment variable: `WEEKLY_PAYOUTS_MANAGEMENT_ENABLED`
- Config source: `REST-BACKEND/config/reports.php`

## Enable Weekly Payouts
1. Set `WEEKLY_PAYOUTS_MANAGEMENT_ENABLED=true` in the active env file.
2. Run:
   - `php artisan config:clear`
   - `php artisan cache:clear`
3. Verify feature status endpoint:
   - `GET /api/admin/reports/weekly-payouts/feature-status`
   - Expect: `{ "success": true, "enabled": true, ... }`

## Disable Weekly Payouts (Fast Rollback)
1. Set `WEEKLY_PAYOUTS_MANAGEMENT_ENABLED=false` in the active env file.
2. Run:
   - `php artisan config:clear`
   - `php artisan cache:clear`
3. Verify feature status endpoint:
   - `GET /api/admin/reports/weekly-payouts/feature-status`
   - Expect: `{ "success": true, "enabled": false, ... }`
4. Expected behavior after disable:
   - Weekly endpoints return controlled feature-disabled responses.
   - Legacy all payouts endpoints remain available.

## Endpoint Matrix
### Always Available (Legacy)
- `GET /api/admin/reports/all-payouts`
- `POST /api/admin/reports/all-payouts/search`

### Weekly Management (Switch-Gated)
- `GET /api/admin/reports/weekly-payouts/feature-status`
- `GET /api/admin/reports/weekly-payouts`
- `GET /api/admin/reports/weekly-payouts/stats`
- `POST /api/admin/reports/weekly-payouts/pay`
- `POST /api/admin/reports/weekly-payouts/reject`
- `POST /api/admin/reports/weekly-payouts/manual`

## Smoke Checks
1. Weekly list loads for a target month when enabled.
2. Weekly stats load when enabled.
3. Bulk pay transitions pending rows to paid.
4. Reject transitions pending row to rejected.
5. Manual payout creation adds pending manual row.
6. Disable switch and confirm weekly endpoints are blocked while legacy endpoints continue.

## Full Rollback (Only if required)
1. Keep switch OFF first.
2. Roll back weekly migrations if needed:
   - `php artisan migrate:rollback --path=database/migrations/2026_04_12_100100_add_weekly_payout_id_to_transactions_table.php`
   - `php artisan migrate:rollback --path=database/migrations/2026_04_12_100000_create_affiliate_weekly_payouts_table.php`
3. Remove weekly payouts code paths after approval.
