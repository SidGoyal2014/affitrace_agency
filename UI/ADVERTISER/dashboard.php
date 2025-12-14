<?php
$pathPrefix = '.';
$activePage = 'dashboard';
$pageTitle = 'Advertiser Dashboard';
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

                <!-- Total Budget -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Budget</div>
                                <div class="stat-value" id="total_budget">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversions -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Conversions</div>
                                <div class="stat-value" id="conversions">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Invoices -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Pending Invoices</div>
                                <div class="stat-value" id="pending_invoices">...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {
            try {
                // TODO: Implement advertiser dashboard API call
                // const data = await API.fetchWithAuth('advertiser/dashboard');

                // Placeholder data for now
                $('#total_offers').text('12');
                $('#total_budget').text('$50,000');
                $('#conversions').text('1,250');
                $('#pending_invoices').text('2');

            } catch (error) {
                console.error('Dashboard load failed:', error);
            }
        });
    </script>
</body>

</html>