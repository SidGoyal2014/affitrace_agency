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
                const data = await API.fetchWithAuth('advertiser/dashboard');

                // Update Stats
                $('#total_offers').text(data.total_offers || 0);
                $('#total_budget').text('$' + Number(data.total_budget || 0).toLocaleString());
                $('#conversions').text(Number(data.conversions || 0).toLocaleString());
                $('#pending_invoices').text(data.pending_invoices || 0);

            } catch (error) {
                console.error('Dashboard load failed:', error);
                $('#total_offers').text('Error');
                $('#total_budget').text('Error');
                $('#conversions').text('Error');
                $('#pending_invoices').text('Error');
            }
        });
    </script>
</body>

</html>