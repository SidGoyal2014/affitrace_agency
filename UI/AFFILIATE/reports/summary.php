<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Performance Report - Affiliate';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1>Performance Summary</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Performance Summary (By Offer)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Offer ID</th>
                                    <th>Offer Name</th>
                                    <th>Total Clicks</th>
                                    <th>Total Conversions</th>
                                    <th>Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Content -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th id="total_clicks">0</th>
                                    <th id="total_conversions">0</th>
                                    <th id="avg_cr">0%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Page level plugins -->
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
        $(document).ready(async function () {

            try {
                const response = await API.fetchWithAuth('affiliate/reports/summary');
                const reportData = response.data || [];

                let totalClicks = 0;
                let totalConversions = 0;

                const data = reportData.map(row => {
                    totalClicks += parseInt(row.total_clicks);
                    totalConversions += parseInt(row.total_conversions);

                    const cr = row.total_clicks > 0 ? ((row.total_conversions / row.total_clicks) * 100).toFixed(2) + '%' : '0.00%';

                    return [
                        row.id,
                        row.offer_name,
                        row.total_clicks,
                        row.total_conversions,
                        cr
                    ];
                });

                $('#dataTable').DataTable({
                    data: data
                });

                $('#total_clicks').text(totalClicks);
                $('#total_conversions').text(totalConversions);

                const avgCr = totalClicks > 0 ? ((totalConversions / totalClicks) * 100).toFixed(2) + '%' : '0.00%';
                $('#avg_cr').text(avgCr);

            } catch (error) {
                console.error('Failed to load summary report:', error);
                $('#dataTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load report.</td></tr>');
            }
        });
    </script>
</body>

</html>