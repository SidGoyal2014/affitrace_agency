<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Payouts';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Payouts</h1>
            </div>

            <div id="featureDisabledAlert" class="alert alert-warning d-none mb-4">
                Weekly payouts management is disabled. Showing legacy payouts report.
            </div>

            <div class="row mb-4" id="weeklyStatsRow">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern">
                        <div class="stat-card">
                            <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Pending</div>
                                <div class="stat-value" id="statPending">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern">
                        <div class="stat-card">
                            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Paid</div>
                                <div class="stat-value" id="statPaid">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern">
                        <div class="stat-card">
                            <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Rejected</div>
                                <div class="stat-value" id="statRejected">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern mb-4" id="weeklyControlsCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Weekly Payouts</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="filterMonth" class="font-weight-bold">Month</label>
                            <input type="month" class="form-control" id="filterMonth">
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-primary" id="btnRefreshWeekly">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                        <div class="col-md-6 mb-3 text-md-right">
                            <button class="btn btn-outline-success mr-2" id="btnBulkPay" disabled>
                                <i class="fas fa-hand-holding-usd mr-1"></i> Record Payment
                            </button>
                            <button class="btn btn-outline-primary" id="btnAddCustomPayment">
                                <i class="fas fa-plus mr-1"></i> Add Custom Payment
                            </button>
                        </div>
                    </div>
                </div>
            <!--</div>

            <div class="card-modern" id="weeklyTableCard">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Weekly Payouts</h6>
                </div>-->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="weeklyDataTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllRows"></th>
                                    <th>Affiliate</th>
                                    <th>Week</th>
                                    <th>Work Basis</th>
                                    <th>Work Units</th>
                                    <th>Amount (USD)</th>
                                    <th>Status</th>
                                    <th>Invoice Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="weeklyTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-modern" id="legacyTableCard">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">All Payouts (Legacy)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="legacyDataTable">
                            <thead>
                                <tr>
                                    <th>Affiliate</th>
                                    <th>Offer</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="legacyTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>
    <script src="../../common/js/affiliate-payment-modal.js"></script>

    <script>
        function formatDecimal(v) { return Number(v || 0).toFixed(2); }

        function statusBadge(status) {
            const normalized = String(status || '').toUpperCase();
            if (normalized === 'PAID') return '<span class="badge badge-success">PAID</span>';
            if (normalized === 'REJECTED') return '<span class="badge badge-danger">REJECTED</span>';
            return '<span class="badge badge-warning">PENDING</span>';
        }

        function currentMonth() {
            const d = new Date();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            return `${d.getFullYear()}-${m}`;
        }

        function formatDate(isoString) {
            if (!isoString) return null;
            // Extract just the date part (YYYY-MM-DD) from ISO string
            return isoString.split('T')[0];
        }

        let weeklyEnabled = false;
        let weeklyRows = [];
        let legacyTable = null;
        let weeklyTable = null;

        function selectedWeeklyIds() {
            const ids = [];
            $('.weekly-row-check:checked').each(function () {
                ids.push(parseInt($(this).val(), 10));
            });
            return ids;
        }

        function refreshBulkButton() {
            const ids = selectedWeeklyIds();
            $('#btnBulkPay').prop('disabled', ids.length === 0);
        }

        function renderWeeklyRows(rows) {
            const tbody = $('#weeklyTableBody');
            tbody.empty();

            if (!rows || rows.length === 0) {
                tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">No weekly payouts found</td></tr>');
                refreshBulkButton();
                return;
            }

            rows.forEach(row => {
                const canAct = String(row.status || '').toUpperCase() === 'PENDING';
                const week = `${formatDate(row.week_start) || '-'} to ${formatDate(row.week_end) || '-'}`;
                const actionHtml = canAct
                    ? `<button class="btn btn-sm btn-success mr-1 btn-pay-row" data-id="${row.id}" data-amount="${formatDecimal(row.total_amount)}"><i class="fas fa-dollar-sign"></i></button>
                       <button class="btn btn-sm btn-danger btn-reject-row" data-id="${row.id}"><i class="fas fa-times"></i></button>`
                    : '<span class="text-muted">-</span>';

                const checkHtml = canAct
                    ? `<input type="checkbox" class="weekly-row-check" value="${row.id}">`
                    : '';

                tbody.append(`
                    <tr>
                        <td>${checkHtml}</td>
                        <td>${row.affiliate_id || '-'}</td>
                        <td>${week}</td>
                        <td>${row.work_basis || '-'}</td>
                        <td>${row.work_units == null ? '-' : row.work_units}</td>
                        <td><strong>${formatDecimal(row.total_amount)}</strong></td>
                        <td>${statusBadge(row.status)}</td>
                        <td>${row.invoice_code || '-'}</td>
                        <td>${actionHtml}</td>
                    </tr>
                `);
            });

            refreshBulkButton();
        }

        async function loadWeeklyData() {
            const month = String($('#filterMonth').val() || '').trim();
            console.log('Loading weekly payouts data for month:', month);

            const [dataRes, statsRes] = await Promise.all([
                API.fetchWithAuth('admin/reports/weekly-payouts?month=' + encodeURIComponent(month) + '&sync=1'),
                API.fetchWithAuth('admin/reports/weekly-payouts/stats?month=' + encodeURIComponent(month))
            ]);

            weeklyRows = Array.isArray(dataRes.data) ? dataRes.data : [];
            renderWeeklyRows(weeklyRows);

            const stats = (statsRes && statsRes.stats) || {};
            $('#statPending').text((stats.pending_count || 0) + ' / $' + formatDecimal(stats.pending_amount));
            $('#statPaid').text((stats.paid_count || 0) + ' / $' + formatDecimal(stats.paid_amount));
            $('#statRejected').text((stats.rejected_count || 0) + ' / $' + formatDecimal(stats.rejected_amount));

        }

        async function loadLegacyPayouts() {
            const response = await API.fetchWithAuth('admin/reports/all-payouts');
            const payouts = response.data?.data || response.data || [];
            const tbody = $('#legacyTableBody');
            tbody.empty();

            if (payouts.length > 0) {
                payouts.forEach(p => {
                    tbody.append(`
                        <tr>
                            <td>${p.affiliate_name || p.aid}</td>
                            <td>${p.offer_name || p.oid}</td>
                            <td><strong>${formatDecimal(p.amount)}</strong></td>
                            <td><strong>${(p.currency)}</strong></td>
                            <td><strong>${(p.status)}</strong></td>
                            <td>${p.created_at || '-'}</td>
                        </tr>
                    `);
                });
            } else {
                tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No payouts found</td></tr>');
            }

            if (legacyTable) {
                legacyTable.destroy();
            }
            legacyTable = $('#legacyDataTable').DataTable({ pageLength: 25 });
        }

        async function rejectRow(id) {
            const reason = prompt('Optional reject reason:') || '';

            const response = await API.fetchWithAuth('admin/reports/weekly-payouts/reject', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payout_id: id, rejected_reason: reason })
            });

            if (response && response.success) {
                Toast.success('Rejected', 'Payout row rejected successfully.');
                await loadWeeklyData();
            } else {
                throw new Error(response.message || 'Failed to reject payout row.');
            }
        }

        async function openPayModalFor(ids, amount) {
            if (typeof AffiliatePaymentPlugin === 'undefined') {
                throw new Error('Affiliate payment modal plugin failed to load. Please refresh and try again.');
            }

            AffiliatePaymentPlugin.open({
                amount: formatDecimal(amount),
                paymentMode: 'Manual',
                onSubmit: async function (formData, done, onError) {
                    try {
                        const payload = {
                            payout_ids: ids,
                            payment_mode: formData.payment_mode,
                            payment_from: 'Agency',
                            payment_to: 'Affiliate',
                            transaction_amount: parseFloat(formData.transaction_amount),
                            activity_date: formData.activity_date,
                            transaction_code: formData.transaction_code,
                            manual_invoice_no: formData.manual_invoice_no || formData.manual_invoice_number || null,
                            transaction_details: formData.transaction_details
                        };

                        const response = await API.fetchWithAuth('admin/reports/weekly-payouts/pay', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        if (response && response.success) {
                            done();
                            Toast.success('Payment Recorded', `Updated ${response.count || ids.length} payout row(s).`);
                            await loadWeeklyData();
                        } else {
                            onError(response.message || 'Failed to record payment.');
                        }
                    } catch (err) {
                        onError(err.message || 'Failed to record payment.');
                    }
                }
            });
        }

        async function createManualPayout() {
            const affiliateId = prompt('Affiliate ID');
            if (!affiliateId) return;

            const month = $('#filterMonth').val() || currentMonth();
            const weekStart = prompt('Week Start (YYYY-MM-DD)');
            if (!weekStart) return;
            const weekEnd = prompt('Week End (YYYY-MM-DD)');
            if (!weekEnd) return;
            const amount = prompt('Amount (USD)');
            if (!amount) return;
            const invoiceCode = prompt('Invoice Code (optional)') || '';
            const notes = prompt('Notes (optional)') || '';

            const response = await API.fetchWithAuth('admin/reports/weekly-payouts/manual', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    affiliate_id: parseInt(affiliateId, 10),
                    week_start: weekStart,
                    week_end: weekEnd,
                    total_amount: parseFloat(amount),
                    currency: 'USD',
                    invoice_code: invoiceCode || null,
                    notes: notes || null,
                    entry_type: 'Manual',
                    work_basis: 'Manual'
                })
            });

            if (response && response.success) {
                Toast.success('Saved', 'Manual payout row created successfully.');
                $('#filterMonth').val(month);
                await loadWeeklyData();
            } else {
                throw new Error(response.message || 'Failed to create manual payout row.');
            }
        }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            $('#filterMonth').val(currentMonth());

            $('#btnRefreshWeekly').on('click', async function () {
                try {
                    await loadWeeklyData();
                } catch (error) {
                    Toast.error('Refresh Failed', error.message || 'Unable to refresh weekly payouts.');
                }
            });

            $('#filterMonth').on('change', async function () {
                try {
                    await loadWeeklyData();
                } catch (error) {
                    Toast.error('Filter Failed', error.message || 'Unable to load data for selected month.');
                }
            });

            try {
                const feature = await API.fetchWithAuth('admin/reports/weekly-payouts/feature-status');
                weeklyEnabled = !!(feature && feature.enabled);
            } catch (e) {
                weeklyEnabled = false;
            }

            if (!weeklyEnabled) {
                $('#featureDisabledAlert').removeClass('d-none');
                $('#weeklyStatsRow, #weeklyControlsCard, #weeklyTableCard').addClass('d-none');
                $('#legacyTableCard').removeClass('d-none');
                try {
                    await loadLegacyPayouts();
                } catch (error) {
                    console.error(error);
                    $('#legacyTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load</td></tr>');
                }
                return;
            }

            $('#legacyTableCard').removeClass('d-none');

            try {
                await loadLegacyPayouts();
            } catch (error) {
                console.error(error);
                $('#legacyTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load all payouts</td></tr>');
            }

            try {
                await loadWeeklyData();
            } catch (error) {
                console.error(error);
                $('#weeklyTableBody').html('<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load weekly payouts</td></tr>');
            }



            $('#weeklyTableBody').on('change', '.weekly-row-check', function () {
                refreshBulkButton();
            });

            $('#selectAllRows').on('change', function () {
                const checked = $(this).is(':checked');
                $('.weekly-row-check').prop('checked', checked);
                refreshBulkButton();
            });

            $('#weeklyTableBody').on('click', '.btn-pay-row', async function () {
                const id = parseInt($(this).data('id'), 10);
                const amount = parseFloat($(this).data('amount'));
                try {
                    await openPayModalFor([id], amount);
                } catch (error) {
                    Toast.error('Payment Failed', error.message || 'Unable to open payment modal.');
                }
            });

            $('#weeklyTableBody').on('click', '.btn-reject-row', async function () {
                const id = parseInt($(this).data('id'), 10);
                try {
                    await rejectRow(id);
                } catch (error) {
                    Toast.error('Reject Failed', error.message || 'Unable to reject payout row.');
                }
            });

            $('#btnBulkPay').on('click', async function () {
                const ids = selectedWeeklyIds();
                if (!ids.length) {
                    Toast.warning('No Selection', 'Please select at least one pending row.');
                    return;
                }
                const amount = weeklyRows
                    .filter(r => ids.includes(parseInt(r.id, 10)))
                    .reduce((sum, r) => sum + parseFloat(r.total_amount || 0), 0);
                try {
                    await openPayModalFor(ids, amount);
                } catch (error) {
                    Toast.error('Payment Failed', error.message || 'Unable to open payment modal. Please check your selection and try again.');
                }
            });

            $('#btnAddCustomPayment').on('click', async function () {
                try {
                    await createManualPayout();
                } catch (error) {
                    Toast.error('Save Failed', error.message || 'Unable to create manual payout.');
                }
            });
        });
    </script>
</body>

</html>