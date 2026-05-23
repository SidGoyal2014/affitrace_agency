<?php
$pathPrefix = '.';
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
            <div class="page-header">
                <h1>Dashboard</h1>
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
                    <div class="card-modern shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">Plan Usage: Clicks Consumed</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small font-weight-bold" id="clicks_status_text">Loading...</span>
                                <span class="small font-weight-bold" id="clicks_percentage_text">0%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-info" role="progressbar" id="clicks_progress_bar"
                                    style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div id="clicks_warning_alert" class="alert alert-danger mt-3 d-none">
                                <i class="fas fa-exclamation-triangle mr-2"></i> <span id="clicks_warning_msg"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
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
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Payouts</div>
                                <div class="stat-value" id="total_payouts">...</div>
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
            </div>

            <!-- Secondary Stats -->
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
                            <span class="badge badge-primary" id="request-count">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0">
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

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Chart Plugins -->
    <script src="assets/vendor/chart.js/Chart.min.js"></script>
    <script src="assets/js/demo/chart-area-demo.js"></script>
    <script src="assets/js/demo/chart-pie-demo.js"></script>

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

                $('#total_offers').text(data.total_offers);
                $('#total_revenue').text(formatDecimal(data.total_revenue));
                $('#total_payouts').text(formatDecimal(data.total_payouts));
                $('#total_affiliates').text(data.total_no_of_affiliates);

                $('#avg_rev_offer').text(formatDecimal(data.average_revenue_per_offer));
                $('#avg_rev_aff').text(formatDecimal(data.average_revenue_per_affiliate));
                $('#avg_payout_offer').text(formatDecimal(data.average_payout_per_offer));
                $('#avg_payout_offer').text(formatDecimal(data.average_payout_per_offer));
                $('#avg_payout_aff').text(formatDecimal(data.average_payout_per_affiliate));

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
                    tbody.append('<tr><td class="text-center py-4 text-muted">No pending requests</td></tr>');
                }

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
    </script>
</body>

</html>