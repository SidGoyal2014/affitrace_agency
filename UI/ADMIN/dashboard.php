<?php
$pathPrefix = '.';
$pathCommonPrefix = $pathPrefix . '/../common';
$activePage = 'dashboard';
$pageTitle = 'Dashboard';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <?php
                /*
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-download mr-2"></i> Generate Report
                </a>
                */
                ?>
            </div>

            <!-- Click Limit Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card-modern shadow-sm py-2 px-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <!-- Title & Progress Bar inline -->
                            <div class="d-flex align-items-center flex-grow-1 mr-md-4 mb-2 mb-md-0"
                                style="min-width: 280px;">
                                <h6 class="font-weight-bold text-primary mb-0 mr-3 text-nowrap">Plan Usage: Clicks
                                    Consumed</h6>
                                <div class="progress flex-grow-1" style="height: 12px; min-width: 150px;">
                                    <div class="progress-bar bg-info" role="progressbar" id="clicks_progress_bar"
                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <!-- Status Values inline -->
                            <div class="d-flex align-items-center">
                                <span class="small font-weight-bold text-muted mr-3"
                                    id="clicks_status_text">Loading...</span>
                                <span class="badge badge-info" id="clicks_percentage_text">0%</span>
                            </div>
                        </div>
                        <!-- Warning Alert below, if triggered -->
                        <div id="clicks_warning_alert" class="alert alert-danger py-1 px-3 mt-2 mb-0 small d-none">
                            <i class="fas fa-exclamation-triangle mr-2"></i> <span id="clicks_warning_msg"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <!-- Row 1: Setup & Margin -->
            <div class="row">
                <!-- Total Offers -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Offers</div>
                                <div class="stat-value" id="total_offers">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Advertisers -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Advertisers</div>
                                <div class="stat-value" id="total_advertisers">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Affiliates -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Affiliates</div>
                                <div class="stat-value" id="total_affiliates">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Margin -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Margin</div>
                                <div class="stat-value" id="total_margin">...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Volume & Finance -->
            <div class="row">
                <!-- Total Clicks -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-mouse-pointer"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Clicks</div>
                                <div class="stat-value" id="total_clicks">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Conversions -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Conversions</div>
                                <div class="stat-value" id="total_conversions">...</div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Total Revenue -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Revenue</div>
                                <div class="stat-value" id="total_revenue">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Payouts -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Payouts</div>
                                <div class="stat-value" id="total_payouts">...</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Row 3: Averages -->
            <div class="row">
                <!-- Avg Rev / Offer -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Avg Rev / Offer</div>
                                <div class="stat-value" id="avg_rev_offer">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Rev / Affiliate -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Avg Rev / Affiliate</div>
                                <div class="stat-value" id="avg_rev_aff">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Payout / Offer -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Avg Payout / Offer</div>
                                <div class="stat-value" id="avg_payout_offer">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Payout / Affiliate -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Avg Payout / Affiliate</div>
                                <div class="stat-value" id="avg_payout_aff">...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Requests Row -->
            <div class="row">
                <!-- Pending Requests -->
                <div class="col-xl-7 col-lg-7 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">Pending Offer Assignments</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0" id="requests_table">
                                    <thead>
                                        <tr>
                                            <th>Request Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requests_table_body">
                                        <tr>
                                            <td class="text-center py-4 text-muted">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Column -->
                <div class="col-xl-5 col-lg-5 mb-4">
                    <!-- Pie Chart -->
                    <div class="card-modern mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Revenue Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="myPieChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Area Chart -->
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Monthly Revenue</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue vs Payout Chart -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card-modern">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="m-0 font-weight-bold">Revenue vs Payout</h6>
                            <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
                                <!-- Period toggle buttons -->
                                <div class="btn-group btn-group-sm mr-3" id="revPayoutPeriodToggle">
                                    <button class="btn btn-outline-primary" data-period="daily">Daily</button>
                                    <button class="btn btn-outline-primary" data-period="weekly">Weekly</button>
                                    <button class="btn btn-primary" data-period="monthly">Monthly</button>
                                    <button class="btn btn-outline-primary" data-period="custom">Custom</button>
                                </div>
                                <!-- Custom date range controls (hidden by default) -->
                                <div id="customRangeControls" class="d-none d-flex align-items-center flex-wrap">
                                    <input type="date" id="rev_from" class="form-control form-control-sm mr-1"
                                        style="width:140px;">
                                    <span class="mx-1 text-muted small">to</span>
                                    <input type="date" id="rev_to" class="form-control form-control-sm mr-2"
                                        style="width:140px;">
                                    <button id="applyCustomRange" class="btn btn-sm btn-success">Apply</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="rev-chart-error" class="alert alert-danger d-none"></div>
                            <div class="chart-area">
                                <canvas id="revenuePayoutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Chart Plugins -->
    <script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/chart.js/Chart.min.js"></script>
    <script src="<?php echo $pathCommonPrefix; ?>/assets/js/demo/chart-area-demo.js"></script>
    <script src="<?php echo $pathCommonPrefix; ?>/assets/js/demo/chart-pie-demo.js"></script>
    <script src="<?php echo $pathCommonPrefix; ?>/assets/js/demo/chart-revenue-payout-demo.js"></script>

    <script>
        // Format numbers to 2 decimal places
        function formatDecimal(value) {
            if (value === null || value === undefined || value === '') return '0.00';
            return parseFloat(value).toFixed(2);
        }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            try {
                const data = await API.fetchWithAuth('admin/dashboard');
                const cur = data.default_currency || 'USD';

                $('#total_offers').text(data.total_offers);
                $('#total_advertisers').text(data.total_no_of_advertisers);
                $('#total_affiliates').text(data.total_no_of_affiliates);
                $('#total_margin').text(`${formatDecimal(data.total_margin)} ${cur}`);

                $('#total_clicks').text(data.overall_clicks);
                $('#total_conversions').text(data.overall_conversions);
                $('#total_payouts').text(`${formatDecimal(data.total_payouts)} ${cur}`);
                $('#total_revenue').text(`${formatDecimal(data.total_revenue)} ${cur}`);

                $('#avg_rev_offer').text(`${formatDecimal(data.average_revenue_per_offer)} ${cur}`);
                $('#avg_rev_aff').text(`${formatDecimal(data.average_revenue_per_affiliate)} ${cur}`);
                $('#avg_payout_offer').text(`${formatDecimal(data.average_payout_per_offer)} ${cur}`);
                $('#avg_payout_aff').text(`${formatDecimal(data.average_payout_per_affiliate)} ${cur}`);

                // Click Limit Logic
                if (data.clicks_limit > 0) {
                    const consumed = parseInt(data.clicks_consumed) || 0;
                    const limit = parseInt(data.clicks_limit);
                    const percent = Math.min(100, Math.round((consumed / limit) * 100));

                    $('#clicks_status_text').text(`${consumed.toLocaleString()} / ${limit.toLocaleString()} Clicks`);
                    $('#clicks_percentage_text').text(`${percent}%`);
                    $('#clicks_progress_bar').css('width', `${percent}%`).attr('aria-valuenow', percent);

                    // Color coding
                    const progressBar = $('#clicks_progress_bar');
                    progressBar.removeClass('bg-info bg-warning bg-danger bg-success');

                    if (percent >= 90) {
                        progressBar.addClass('bg-danger');
                    } else if (percent >= 75) {
                        progressBar.addClass('bg-warning');
                    } else {
                        progressBar.addClass('bg-success'); // or bg-info
                    }

                    // Warning Message
                    if (data.click_message) {
                        $('#clicks_warning_alert').removeClass('d-none');
                        $('#clicks_warning_msg').text(data.click_message);
                    } else {
                        $('#clicks_warning_alert').addClass('d-none');
                    }
                } else {
                    $('#clicks_status_text').text('Unlimited Plan / No Limit Set');
                    $('#clicks_percentage_text').text('');
                    $('#clicks_progress_bar').css('width', '100%').addClass('bg-secondary');
                    $('#clicks_warning_alert').addClass('d-none');
                }

                const tbody = $('#requests_table_body');
                tbody.empty();

                if (data.all_requests && data.all_requests.length > 0) {
                    $('#request-count').text(data.all_requests.length);
                    data.all_requests.forEach(req => {
                        const row = `
                            <tr>
                                <td>
                                    <div class="py-2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="text-primary">${req.offer_name}</strong>
                                            <span class="badge badge-success">${formatDecimal(req.offer_payout) || 'N/A'}</span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            By: <strong>${req.name}</strong> (ID: ${req.aid})
                                        </div>
                                        <div class="text-right">
                                            <button class="btn btn-sm btn-outline-danger mr-1" onclick="rejectRequest(${req.oaid})">Reject</button>
                                            <button class="btn btn-sm btn-primary" onclick="approveRequest(${req.oaid})">Approve</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    $('#request-count').text('0');
                    //tbody.append('<tr><td class="text-center py-4 text-muted">No pending requests</td></tr>');
                }

                $('#requests_table').DataTable({
                    pageLength: 5,
                    lengthMenu: [5, 10, 25, 50],
                    scrollY: '500px',
                    scrollCollapse: true,
                    language: {
                        emptyTable: 'No pending requests',
                        search: 'Search:',
                        info: 'Showing _START_ to _END_ of _TOTAL_ requests',
                        infoEmpty: 'No pending requests'
                    }
                });

                if (window.initAreaChart) {
                    let monthlyRevenue = [];
                    try { monthlyRevenue = JSON.parse(data.arrMonthlyRevenue); } catch (e) { }
                    window.initAreaChart(monthlyRevenue);
                }

                if (window.initPieChart) {
                    window.initPieChart(data.labelCategories, data.strRevenue);
                }

            } catch (error) {
                console.error("Dashboard Load Error:", error);
            }
        });

        async function rejectRequest(id) {
            if (!confirm("Reject this request?")) {
                return;
            }

            try {
                const response = await API.fetchWithAuth(`admin/reject-assign-request/${id}`);
                Toast.success('Request Rejected', response.message || 'Offer request rejected successfully');
                window.location.reload();
            } catch (error) {
                Toast.error('Action Failed', error.message || 'Could not reject request');
            }
        }

        async function approveRequest(oaid) {
            if (!confirm("Approve this request?")) {
                return;
            }

            try {
                const response = await API.fetchWithAuth(`admin/approve-assign-request/${oaid}`);
                Toast.success('Request Approved', response.message || 'Offer assigned successfully');
                window.location.reload();
            } catch (error) {
                Toast.error('Action Failed', error.message || 'Could not approve request');
            }
        }

        // ── Revenue vs Payout Chart ──────────────────────────────────────────

        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        async function loadRevenuePayoutChart(period, from = null, to = null) {
            let url = `admin/dashboard/revenue-payout-chart?period=${period}`;
            if (from && to) {
                url += `&from=${from}&to=${to}`;
            }
            try {
                const data = await API.fetchWithAuth(url);
                if (data.status === 0) {
                    $('#rev-chart-error').removeClass('d-none').text(data.error || 'Failed to load chart data.');
                    return;
                }
                $('#rev-chart-error').addClass('d-none');
                if (window.initRevenuePayoutChart) {
                    window.initRevenuePayoutChart(data.labels, data.revenue, data.payout);
                }
            } catch (e) {
                $('#rev-chart-error').removeClass('d-none').text('Failed to load Revenue vs Payout chart.');
                console.error('Revenue Payout Chart Error:', e);
            }
        }

        // Period toggle buttons
        $('#revPayoutPeriodToggle button').on('click', function () {
            const period = $(this).data('period');
            $('#revPayoutPeriodToggle button').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            if (period === 'custom') {
                const today = new Date();
                const firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                $('#rev_from').val(formatDate(firstOfMonth));
                $('#rev_to').val(formatDate(today));
                $('#customRangeControls').removeClass('d-none');
            } else {
                $('#customRangeControls').addClass('d-none');
                loadRevenuePayoutChart(period);
            }
        });

        // Apply custom date range
        $('#applyCustomRange').on('click', function () {
            const from = $('#rev_from').val();
            const to = $('#rev_to').val();
            if (!from || !to) {
                $('#rev-chart-error').removeClass('d-none').text('Please select both From and To dates.');
                return;
            }
            if (from > to) {
                $('#rev-chart-error').removeClass('d-none').text('From date must be before or equal to To date.');
                return;
            }
            $('#rev-chart-error').addClass('d-none');
            loadRevenuePayoutChart('custom', from, to);
        });

        // Load default (monthly) on page ready — called after DOM is ready
        loadRevenuePayoutChart('monthly');
    </script>
</body>

</html>