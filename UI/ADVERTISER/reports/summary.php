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
        $(document).ready(function () {
            // Sample data
            const data = [
                { date: '2023-10-01', impressions: 10000, clicks: 500, conversions: 20, cost: '$100.00' },
                { date: '2023-10-02', impressions: 12000, clicks: 600, conversions: 25, cost: '$125.00' }
            ];

            let html = '';
            data.forEach(row => {
                html += `<tr>
                    <td>${row.date}</td>
                    <td>${row.impressions}</td>
                    <td>${row.clicks}</td>
                    <td>${row.conversions}</td>
                    <td>${row.cost}</td>
                </tr>`;
            });
            $('#summaryTableBody').html(html);
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>