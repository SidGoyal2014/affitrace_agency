<?php
$pathPrefix = '.';
$pathCommonPrefix = $pathPrefix . '/../common';
$activePage = 'dashboard';
$pageTitle = 'Employee Dashboard';
include $pathPrefix . '/includes/head.php';
?>

<style>
    /* Premium Modern Dashboard Styles */
    :root {
        --theme-indigo-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        --theme-emerald-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        --theme-amber-gradient: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        --theme-rose-gradient: linear-gradient(135deg, #be123c 0%, #e11d48 100%);
        --theme-blue-gradient: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
        --theme-violet-gradient: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%);
        --theme-dark-indigo: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
    }

    .welcome-card-premium {
        background: var(--theme-dark-indigo);
        border: none;
        border-radius: var(--radius-lg);
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .welcome-card-premium::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(99, 102, 241, 0.15);
        border-radius: 50%;
        top: -100px;
        right: -100px;
        z-index: 1;
        pointer-events: none;
    }

    .welcome-card-premium .card-body {
        position: relative;
        z-index: 2;
    }

    .card-modern {
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        background: var(--bg-card);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .stat-card-gradient {
        border: none;
        color: #ffffff;
    }

    .stat-card-gradient .stat-label {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .stat-card-gradient .stat-value {
        color: #ffffff !important;
    }

    .g-purple {
        background: var(--theme-indigo-gradient);
    }

    .g-green {
        background: var(--theme-emerald-gradient);
    }

    .g-orange {
        background: var(--theme-amber-gradient);
    }

    .g-red {
        background: var(--theme-rose-gradient);
    }

    .g-blue {
        background: var(--theme-blue-gradient);
    }

    .g-violet {
        background: var(--theme-violet-gradient);
    }

    .badge-role {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-radius: var(--radius-full);
        padding: 0.35rem 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-role-am {
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .badge-role-adv {
        background: rgba(244, 114, 182, 0.15);
        color: #f472b6;
        border: 1px solid rgba(244, 114, 182, 0.3);
    }

    .badge-role-fin {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-role-obs {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* Task Item styles */
    .task-item {
        border-left: 4px solid var(--border-default);
        transition: all 0.2s ease;
    }

    .task-item:hover {
        background: var(--bg-body);
        border-left-color: var(--primary-main);
    }

    .task-item.pending {
        border-left-color: var(--warning-main);
    }

    .task-item.high {
        border-left-color: var(--error-main);
    }

    .task-item.success {
        border-left-color: var(--success-main);
    }

    /* Observer Locked Overlay */
    .observer-locked-card {
        position: relative;
    }

    .observer-locked-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        text-align: center;
        padding: 1.5rem;
    }

    .observer-locked-overlay i {
        font-size: 2rem;
        color: var(--error-main);
        margin-bottom: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.7;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Skeleton Loader */
    .skeleton-loader {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: var(--radius-md);
        display: inline-block;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }
</style>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Welcome Header Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="welcome-card-premium card">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <span id="role-badge-container"></span>
                                    <h2 class="h1 mt-2 mb-3" style="color:#fff;">Welcome back, <span
                                            class="user-name">Employee</span>!</h2>
                                    <p class="lead mb-0 text-white-50">Here is your account overview, pending tasks, and
                                        performance updates for today.</p>
                                </div>
                                <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                                    <div class="text-white-50 small mb-1"><i class="far fa-calendar-alt mr-1"></i>
                                        Current Date</div>
                                    <h5 class="mb-0 text-white font-weight-bold" id="current-date-display">...</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Usage: Clicks Consumed (if set) -->
            <div id="plan-usage-row" class="row mb-4 d-none">
                <div class="col-12">
                    <div class="card-modern py-3 px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
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
                            <div class="d-flex align-items-center">
                                <span class="small font-weight-bold text-muted mr-3"
                                    id="clicks_status_text">Loading...</span>
                                <span class="badge badge-info" id="clicks_percentage_text">0%</span>
                            </div>
                        </div>
                        <div id="clicks_warning_alert" class="alert alert-danger py-1 px-3 mt-2 mb-0 small d-none">
                            <i class="fas fa-exclamation-triangle mr-2"></i> <span id="clicks_warning_msg"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Scoped Stats Grid -->
            <div class="row" id="stats-grid-container">
                <!-- Skeletons will load here -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern p-4" style="height: 110px;">
                        <div class="skeleton-loader w-70 h-20 mb-2"></div>
                        <div class="skeleton-loader w-50 h-30"></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern p-4" style="height: 110px;">
                        <div class="skeleton-loader w-70 h-20 mb-2"></div>
                        <div class="skeleton-loader w-50 h-30"></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern p-4" style="height: 110px;">
                        <div class="skeleton-loader w-70 h-20 mb-2"></div>
                        <div class="skeleton-loader w-50 h-30"></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern p-4" style="height: 110px;">
                        <div class="skeleton-loader w-70 h-20 mb-2"></div>
                        <div class="skeleton-loader w-50 h-30"></div>
                    </div>
                </div>
            </div>

            <!-- Middle Row: Today's Metrics & Tasks -->
            <div class="row">
                <!-- Left: Action Items & Pending Tasks -->
                <div class="col-xl-7 col-lg-7 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks mr-2"></i>My Pending
                                Action Items</h6>
                            <span class="badge badge-warning" id="task-badge-count">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="pending-tasks-list">
                                <div class="p-4 text-center text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                                    Loading action items...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Top Performing Lists -->
                <div class="col-xl-5 col-lg-5 mb-4">
                    <!-- Top Offers -->
                    <div class="card-modern mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fire mr-2"></i>Top Managed
                                Offers</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0">
                                    <thead>
                                        <tr>
                                            <th>Offer Name</th>
                                            <th class="text-right">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-offers-list">
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                                                Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Top Affiliates / Advertisers (Conditional) -->
                    <div class="card-modern" id="top-partners-card">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary" id="top-partners-title"><i
                                    class="fas fa-award mr-2"></i>Top Partners</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0">
                                    <thead>
                                        <tr>
                                            <th id="partner-col-name">Name</th>
                                            <th class="text-right" id="partner-col-value">Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-partners-list">
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                                                Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue vs Payout Scoped Chart -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card-modern observer-locked-card">
                        <!-- Locked Screen for Observer -->
                        <div id="observer-chart-overlay" class="observer-locked-overlay d-none">
                            <i class="fas fa-lock"></i>
                            <h5>Financial Chart Unavailable</h5>
                            <p class="text-muted small mb-0">Your profile (Observer) is restricted from viewing
                                financial trends.</p>
                        </div>

                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line mr-2"></i>Revenue
                                vs Payout Performance</h6>
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
                                        style="width:140px; height: 31px; padding: 0.25rem 0.5rem;">
                                    <span class="mx-1 text-muted small">to</span>
                                    <input type="date" id="rev_to" class="form-control form-control-sm mr-2"
                                        style="width:140px; height: 31px; padding: 0.25rem 0.5rem;">
                                    <button id="applyCustomRange" class="btn btn-sm btn-success"
                                        style="padding: 0.25rem 0.5rem;">Apply</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="rev-chart-error" class="alert alert-danger d-none"></div>
                            <div class="chart-area" style="height: 350px; position: relative;">
                                <canvas id="revenuePayoutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Reject/Approve Offer Assignment Modals if needed, but we can do inline actions -->

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Load ChartJS -->
    <script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/chart.js/Chart.min.js"></script>

    <script>
        // Formatting Helpers
        function formatDecimal(value) {
            if (value === null || value === undefined || value === '') return '0.00';
            return parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        $(document).ready(async function () {
            // Guard Login
            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            // Display current date in welcome header
            const todayOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#current-date-display').text(new Date().toLocaleDateString('en-US', todayOptions));

            // Load User Profile details
            const user = JSON.parse(localStorage.getItem('user'));
            //console.log(user);
            let roleName = 'employee';
            if (user) {
                $('.user-name').text(user.name);
                if (user.role && user.role.name) {
                    roleName = user.role.name.toLowerCase();
                }
            }

            // Render Role Badge
            let badgeHtml = '';
            if (roleName.includes('affiliate manager')) {
                badgeHtml = '<span class="badge-role badge-role-am"><i class="fas fa-user-shield"></i> Affiliate Manager</span>';
            } else if (roleName.includes('advertiser manager')) {
                badgeHtml = '<span class="badge-role badge-role-adv"><i class="fas fa-briefcase"></i> Advertiser Manager</span>';
            } else if (roleName.includes('finance')) {
                badgeHtml = '<span class="badge-role badge-role-fin"><i class="fas fa-wallet"></i> Finance Manager</span>';
            } else if (roleName.includes('observer')) {
                badgeHtml = '<span class="badge-role badge-role-obs"><i class="fas fa-eye"></i> Observer Mode</span>';
            } else {
                badgeHtml = `<span class="badge-role badge-role-am"><i class="fas fa-user-tie"></i> ${user.designation ? user.designation.name : 'Employee'}</span>`;
            }
            $('#role-badge-container').html(badgeHtml);

            // Fetch Main Scoped Dashboard aggregate data
            let dashboardData = {};
            let currency = 'USD';
            try {
                dashboardData = await API.fetchWithAuth('admin/dashboard');
                currency = dashboardData.default_currency || 'USD';
            } catch (err) {
                console.error('Failed to fetch dashboard aggregate stats', err);
            }

            // Build Stats Cards based on role designation
            const statsContainer = $('#stats-grid-container');
            statsContainer.empty();

            const isAM = roleName.includes('affiliate manager');
            const isAdvM = roleName.includes('advertiser manager');
            const isObserver = roleName.includes('observer');

            let cardData = [];

            if (isObserver) {
                cardData = [
                    { label: 'Managed Affiliates', value: dashboardData.total_no_of_affiliates || 0, icon: 'fa-users', color: 'g-purple' },
                    { label: 'Managed Advertisers', value: dashboardData.total_no_of_advertisers || 0, icon: 'fa-briefcase', color: 'g-blue' },
                    { label: 'Total Offers', value: dashboardData.total_offers || 0, icon: 'fa-bullhorn', color: 'g-violet' },
                    { label: 'Total Margin', value: '<i class="fas fa-lock mr-1"></i> Locked', icon: 'fa-percentage', color: 'g-orange' },
                    { label: 'Total Revenue', value: '<i class="fas fa-lock mr-1"></i> Locked', icon: 'fa-money-bill-wave', color: 'g-green' },
                    { label: 'Total Payouts', value: '<i class="fas fa-lock mr-1"></i> Locked', icon: 'fa-hand-holding-usd', color: 'g-red' }
                ];
                // Display observer chart overlay
                $('#observer-chart-overlay').removeClass('d-none');
            } else if (isAM) {
                cardData = [
                    { label: 'Managed Affiliates', value: dashboardData.total_no_of_affiliates || 0, icon: 'fa-users', color: 'g-purple' },
                    { label: 'Total Clicks', value: dashboardData.overall_clicks || 0, icon: 'fa-mouse-pointer', color: 'g-blue' },
                    { label: 'Conversions', value: dashboardData.overall_conversions || 0, icon: 'fa-check-double', color: 'g-violet' },
                    { label: 'Total Margin', value: `${formatDecimal(dashboardData.total_margin)} ${currency}`, icon: 'fa-chart-pie', color: 'g-orange' },
                    { label: 'Total Revenue', value: `${formatDecimal(dashboardData.total_revenue)} ${currency}`, icon: 'fa-money-bill-wave', color: 'g-green' },
                    { label: 'Total Payouts', value: `${formatDecimal(dashboardData.total_payouts)} ${currency}`, icon: 'fa-wallet', color: 'g-red' }
                ];
            } else if (isAdvM) {
                cardData = [
                    { label: 'Managed Advertisers', value: dashboardData.total_no_of_advertisers || 0, icon: 'fa-briefcase', color: 'g-purple' },
                    { label: 'Total Offers', value: dashboardData.total_offers || 0, icon: 'fa-bullhorn', color: 'g-blue' },
                    { label: 'Clicks Scoped', value: dashboardData.overall_clicks || 0, icon: 'fa-mouse-pointer', color: 'g-violet' },
                    { label: 'Conversions', value: dashboardData.overall_conversions || 0, icon: 'fa-check-double', color: 'g-orange' },
                    { label: 'Total Revenue', value: `${formatDecimal(dashboardData.total_revenue)} ${currency}`, icon: 'fa-money-bill-wave', color: 'g-green' },
                    { label: 'Total Margin', value: `${formatDecimal(dashboardData.total_margin)} ${currency}`, icon: 'fa-chart-pie', color: 'g-red' }
                ];
            } else {
                // Fallback / standard
                cardData = [
                    { label: 'Total Offers', value: dashboardData.total_offers || 0, icon: 'fa-bullhorn', color: 'g-purple' },
                    { label: 'Clicks', value: dashboardData.overall_clicks || 0, icon: 'fa-mouse-pointer', color: 'g-blue' },
                    { label: 'Conversions', value: dashboardData.overall_conversions || 0, icon: 'fa-check-double', color: 'g-violet' },
                    { label: 'Total Revenue', value: `${formatDecimal(dashboardData.total_revenue)} ${currency}`, icon: 'fa-money-bill-wave', color: 'g-green' },
                    { label: 'Total Payouts', value: `${formatDecimal(dashboardData.total_payouts)} ${currency}`, icon: 'fa-wallet', color: 'g-red' },
                    { label: 'Total Margin', value: `${formatDecimal(dashboardData.total_margin)} ${currency}`, icon: 'fa-chart-pie', color: 'g-orange' }
                ];
            }

            cardData.forEach(card => {
                const html = `
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card-modern stat-card stat-card-gradient ${card.color}">
                            <div class="stat-icon" style="background: rgba(255,255,255,0.18); color: #fff;">
                                <i class="fas ${card.icon}"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">${card.label}</div>
                                <div class="stat-value font-weight-bold">${card.value}</div>
                            </div>
                        </div>
                    </div>
                `;
                statsContainer.append(html);
            });

            // Display Click limits plan status if enabled & not observer
            if (!isObserver && dashboardData.clicks_limit > 0) {
                $('#plan-usage-row').removeClass('d-none');
                const consumed = parseInt(dashboardData.clicks_consumed) || 0;
                const limit = parseInt(dashboardData.clicks_limit);
                const percent = Math.min(100, Math.round((consumed / limit) * 100));

                $('#clicks_status_text').text(`${consumed.toLocaleString()} / ${limit.toLocaleString()} Clicks`);
                $('#clicks_percentage_text').text(`${percent}%`);
                $('#clicks_progress_bar').css('width', `${percent}%`).attr('aria-valuenow', percent);

                const progressBar = $('#clicks_progress_bar');
                progressBar.removeClass('bg-info bg-warning bg-danger bg-success');
                if (percent >= 90) progressBar.addClass('bg-danger');
                else if (percent >= 75) progressBar.addClass('bg-warning');
                else progressBar.addClass('bg-success');

                if (dashboardData.click_message) {
                    $('#clicks_warning_alert').removeClass('d-none');
                    $('#clicks_warning_msg').text(dashboardData.click_message);
                }
            }

            // Draw Top Performing Lists
            // 1. Top Offers
            const topOffersTbody = $('#top-offers-list');
            topOffersTbody.empty();
            if (dashboardData.topEarningOffers && dashboardData.topEarningOffers.data && dashboardData.topEarningOffers.data.length > 0) {
                dashboardData.topEarningOffers.data.forEach(item => {
                    const revStr = isObserver ? 'Locked' : `${formatDecimal(item.revenue)} ${currency}`;
                    topOffersTbody.append(`
                        <tr>
                            <td>
                                <span class="font-weight-bold text-gray-800">${item.offer_name}</span>
                                <div class="small text-muted">ID: ${item.offer_id} | Status: <span class="badge badge-sm badge-secondary">${item.offer_status}</span></div>
                            </td>
                            <td class="text-right font-weight-bold text-success">${revStr}</td>
                        </tr>
                    `);
                });
            } else {
                topOffersTbody.append('<tr><td colspan="2" class="text-center py-3 text-muted">No offer performance data</td></tr>');
            }

            // 2. Top Partners (Affiliates for AM, Advertisers for AdvM)
            const topPartnersTbody = $('#top-partners-list');
            topPartnersTbody.empty();
            if (isAdvM) {
                $('#top-partners-title').html('<i class="fas fa-handshake mr-2"></i>Top Assigned Advertisers');
                $('#partner-col-name').text('Advertiser Company');
                $('#partner-col-value').text('Spend / Revenue');

                // For Advertiser Managers, let's fetch top advertisers list
                // If dashboardData.topEarningAffiliates exists, it might contain affiliates.
                // Let's check if there is partner data we can display
                if (dashboardData.topEarningAffiliates && dashboardData.topEarningAffiliates.data && dashboardData.topEarningAffiliates.data.length > 0) {
                    // Let's list the top clients
                    dashboardData.topEarningAffiliates.data.forEach(item => {
                        const valStr = isObserver ? 'Locked' : `${formatDecimal(item.payout)} ${currency}`;
                        topPartnersTbody.append(`
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-gray-800">${item.name}</span>
                                    <div class="small text-muted">Client ID: ${item.uid}</div>
                                </td>
                                <td class="text-right font-weight-bold text-primary">${valStr}</td>
                            </tr>
                        `);
                    });
                } else {
                    topPartnersTbody.append('<tr><td colspan="2" class="text-center py-3 text-muted">No advertiser performance data</td></tr>');
                }
            } else {
                // Affiliate Manager / Observer / Other
                $('#top-partners-title').html('<i class="fas fa-users mr-2"></i>Top Managed Affiliates');
                $('#partner-col-name').text('Affiliate Name');
                $('#partner-col-value').text('Payouts / Earnings');

                if (dashboardData.topEarningAffiliates && dashboardData.topEarningAffiliates.data && dashboardData.topEarningAffiliates.data.length > 0) {
                    dashboardData.topEarningAffiliates.data.forEach(item => {
                        const valStr = isObserver ? 'Locked' : `${formatDecimal(item.payout)} ${currency}`;
                        topPartnersTbody.append(`
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-gray-800">${item.name}</span>
                                    <div class="small text-muted">Affiliate ID: ${item.uid}</div>
                                </td>
                                <td class="text-right font-weight-bold text-primary">${valStr}</td>
                            </tr>
                        `);
                    });
                } else {
                    topPartnersTbody.append('<tr><td colspan="2" class="text-center py-3 text-muted">No affiliate performance data</td></tr>');
                }
            }

            // Pending Action Items List
            const tasksList = $('#pending-tasks-list');
            tasksList.empty();
            let taskCount = 0;

            // 1. Pending Offer Assignment Requests (from dashboardData.all_requests)
            if (dashboardData.all_requests && dashboardData.all_requests.length > 0) {
                dashboardData.all_requests.forEach(req => {
                    taskCount++;
                    const itemHtml = `
                        <div class="list-group-item task-item pending p-3 mb-2 card-modern">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <h6 class="mb-1 text-primary font-weight-bold"><i class="fas fa-plus-circle mr-1"></i>Offer Assignment Approval Required</h6>
                                <span class="badge badge-warning">Pending Approval</span>
                            </div>
                            <p class="mb-1 text-gray-800 small"><strong>${req.name}</strong> (ID: ${req.aid}) has requested access to offer: <strong>${req.offer_name}</strong> (ID: ${req.oid})</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="small text-muted">Category: ${req.category_name || 'N/A'}</span>
                                <div>
                                    <button class="btn btn-sm btn-outline-danger py-1 px-2 mr-1" onclick="rejectRequest(${req.oaid})">Reject</button>
                                    <button class="btn btn-sm btn-primary py-1 px-2" onclick="approveRequest(${req.oaid})">Approve</button>
                                </div>
                            </div>
                        </div>
                    `;
                    tasksList.append(itemHtml);
                });
            }

            // 2. Pending Payouts (for Affiliate Managers / Finance)
            if (isAM || roleName.includes('finance')) {
                try {
                    const payoutStats = await API.fetchWithAuth('admin/payouts/weekly-payouts/stats');
                    if (payoutStats && payoutStats.stats && payoutStats.stats.pending_count > 0) {
                        taskCount++;
                        const pendingAmt = isObserver ? 'Locked' : `${formatDecimal(payoutStats.stats.pending_amount)} ${currency}`;
                        tasksList.append(`
                            <div class="list-group-item task-item high p-3 mb-2 card-modern">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <h6 class="mb-1 text-danger font-weight-bold"><i class="fas fa-file-invoice-dollar mr-1"></i>Unpaid Weekly Payouts Queue</h6>
                                    <span class="badge badge-danger">Urgent</span>
                                </div>
                                <p class="mb-1 text-gray-800 small">There are <strong>${payoutStats.stats.pending_count}</strong> weekly payouts pending review & payment processing.</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="small font-weight-bold text-gray-700">Total Pending: ${pendingAmt}</span>
                                    <a class="btn btn-sm btn-outline-primary py-1 px-2" href="payouts/payouts.php">Process Payouts <i class="fas fa-chevron-right ml-1"></i></a>
                                </div>
                            </div>
                        `);
                    }
                } catch (e) {
                    console.log('No weekly payout stats returned or not authorized');
                }
            }

            // 3. Pending Invoices (for Advertiser Managers / Finance)
            if (isAdvM || roleName.includes('finance')) {
                try {
                    // Let's query list of invoices that are SENT/UNPAID
                    const invoiceData = await API.fetchWithAuth('admin/invoices?status=SENT');
                    if (invoiceData && invoiceData.data && invoiceData.data.length > 0) {
                        taskCount++;
                        let totalUnpaid = 0;
                        invoiceData.data.forEach(inv => { totalUnpaid += parseFloat(inv.total_amount) || 0; });
                        const unpaidAmt = isObserver ? 'Locked' : `${formatDecimal(totalUnpaid)} ${currency}`;
                        tasksList.append(`
                            <div class="list-group-item task-item pending p-3 mb-2 card-modern">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <h6 class="mb-1 text-warning font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i>Outstanding Client Invoices</h6>
                                    <span class="badge badge-warning">Awaiting Payment</span>
                                </div>
                                <p class="mb-1 text-gray-800 small">You have <strong>${invoiceData.data.length}</strong> sent invoices awaiting payment from your advertisers.</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="small font-weight-bold text-gray-700">Total Value: ${unpaidAmt}</span>
                                    <a class="btn btn-sm btn-outline-primary py-1 px-2" href="invoices/index.php">View Invoices <i class="fas fa-chevron-right ml-1"></i></a>
                                </div>
                            </div>
                        `);
                    }
                } catch (e) {
                    console.log('Could not fetch invoices queue');
                }
            }

            // If zero tasks
            if (taskCount === 0) {
                tasksList.append(`
                    <div class="py-5 text-center text-muted">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>All caught up!</h5>
                        <p class="small mb-0">No pending weekly payouts or offer requests are assigned to your profile right now.</p>
                    </div>
                `);
            }
            $('#task-badge-count').text(taskCount);


            // ── Revenue vs Payout Performance Chart ──
            let myChart = null;

            async function loadRevenuePayoutChart(period, from = null, to = null) {
                if (isObserver) return; // Charts are locked

                let url = `admin/dashboard/revenue-payout-chart?period=${period}`;
                if (from && to) {
                    url += `&from=${from}&to=${to}`;
                }

                try {
                    const chartResponse = await API.fetchWithAuth(url);
                    if (chartResponse.status === 0) {
                        $('#rev-chart-error').removeClass('d-none').text(chartResponse.error || 'Failed to load chart data.');
                        return;
                    }
                    $('#rev-chart-error').addClass('d-none');

                    const ctx = document.getElementById('revenuePayoutChart').getContext('2d');

                    if (myChart) {
                        myChart.destroy();
                    }

                    myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartResponse.labels || [],
                            datasets: [
                                {
                                    label: 'Revenue',
                                    data: chartResponse.revenue || [],
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99, 102, 241, 0.05)',
                                    borderWidth: 3,
                                    pointBackgroundColor: '#6366f1',
                                    pointHoverRadius: 6,
                                    fill: true,
                                    lineTension: 0.3
                                },
                                {
                                    label: isAM ? 'Payout (Cost)' : 'Payout / Cost',
                                    data: chartResponse.payout || [],
                                    borderColor: '#f472b6',
                                    backgroundColor: 'rgba(244, 114, 182, 0.05)',
                                    borderWidth: 3,
                                    pointBackgroundColor: '#f472b6',
                                    pointHoverRadius: 6,
                                    fill: true,
                                    lineTension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        callback: function (value) {
                                            return value.toLocaleString() + ' ' + currency;
                                        }
                                    }
                                }]
                            },
                            tooltips: {
                                callbacks: {
                                    label: function (tooltipItem, data) {
                                        let label = data.datasets[tooltipItem.datasetIndex].label || '';
                                        if (label) label += ': ';
                                        label += formatDecimal(tooltipItem.yLabel) + ' ' + currency;
                                        return label;
                                    }
                                }
                            }
                        }
                    });

                } catch (e) {
                    $('#rev-chart-error').removeClass('d-none').text('Failed to load Revenue vs Payout chart.');
                    console.error('Revenue Payout Chart Error:', e);
                }
            }

            // Period toggles
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

            // Apply custom range
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

            // Initial load of chart (monthly)
            loadRevenuePayoutChart('monthly');
        });

        // Offer Request Approval Actions
        async function rejectRequest(id) {
            if (!confirm("Are you sure you want to reject this offer assignment request?")) return;
            try {
                const response = await API.fetchWithAuth(`admin/reject-assign-request/${id}`);
                Toast.success('Request Rejected', response.message || 'Offer request rejected successfully');
                window.location.reload();
            } catch (error) {
                Toast.error('Action Failed', error.message || 'Could not reject request');
            }
        }

        async function approveRequest(oaid) {
            if (!confirm("Are you sure you want to approve this offer assignment request?")) return;
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