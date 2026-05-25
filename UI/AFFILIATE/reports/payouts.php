<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Payouts Report - Affiliate';
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
                <h1>Payouts Report</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Payouts (Conversions)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Offer Name</th>
                                    <th>Event</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Content -->
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

    <!-- Page level custom scripts -->
    <script>
        $(document).ready(async function () {

            try {
                const response = await API.fetchWithAuth('affiliate/reports/payouts');
                const payoutsData = response.data || [];

                const data = payoutsData.map(row => {
                    return [
                        row.date_updated,
                        row.offer_name,
                        row.event_name || 'Default',
                        '$' + parseFloat(row.price).toFixed(2) + ' ' + (row.ot_currency || 'USD')
                    ];
                });

                $('#dataTable').DataTable({
                    data: data,
                    order: [[0, "desc"]]
                });

            } catch (error) {
                console.error('Failed to load payouts report:', error);
                $('#dataTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load report.</td></tr>');
            }
        });
    </script>
</body>

</html>