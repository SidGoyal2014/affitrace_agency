<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Reports - Advertiser';
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
                <h1>Summary Report</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Summary Stats</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>Conversions</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody id="summaryTableBody">
                                <!-- Loaded via JS -->
                            </tbody>
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

    <script>
        $(document).ready(async function () {
            try {
                const data = await API.fetchWithAuth('advertiser/reports/summary');

                let html = '';
                if (data.stats && data.stats.length > 0) {
                    data.stats.forEach(row => {
                        html += `<tr>
                            <td>${row.date}</td>
                            <td>${Number(row.impressions || 0).toLocaleString()}</td>
                            <td>${Number(row.clicks || 0).toLocaleString()}</td>
                            <td>${Number(row.conversions || 0).toLocaleString()}</td>
                            <td>$${Number(row.cost || 0).toFixed(2)}</td>
                        </tr>`;
                    });
                }
                $('#summaryTableBody').html(html);
                $('#dataTable').DataTable();
            } catch (error) {
                console.error('Summary load failed:', error);
                $('#summaryTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load report: ' + error.message + '</td></tr>');
            }
        });
    </script>
</body>

</html>