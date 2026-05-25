<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Click & Conversion Report - Affiliate';
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
                <h1>Click & Conversion Report</h1>
            </div>

            <!-- Filters -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Filter Report</h6>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>From Date</label>
                                <input type="date" class="form-control" name="from" id="from_date">
                            </div>
                            <div class="form-group col-md-3">
                                <label>To Date</label>
                                <input type="date" class="form-control" name="to" id="to_date">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Offer ID</label>
                                <input type="text" class="form-control" name="offer_id" placeholder="Optional">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Status</label>
                                <select class="form-control" name="conversion_status">
                                    <option value="">All</option>
                                    <option value="converted">Converted</option>
                                    <option value="pending">Not-converted</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Load Report</button>
                    </form>
                </div>
            </div>

            <!-- DataTable -->
            <div class="card-modern mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Click & Conversion Logs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Click ID</th>
                                    <th>Date</th>
                                    <th>Offer</th>
                                    <th>Country</th>
                                    <th>Device</th>
                                    <th>Source</th>
                                    <th>Google AID</th>
                                    <th>Android ID</th>
                                    <th>IDFA</th>
                                    <th>Converted?</th>
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
        $(document).ready(function () {
            let table = $('#dataTable').DataTable();

            $('#filterForm').on('submit', async function (e) {
                e.preventDefault();
                loadReport();
            });

            async function loadReport() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                params.append('limit', 100);

                try {
                    const response = await API.fetchWithAuth('affiliate/reports/click-conversion-report?' + params.toString());

                    const reportData = (response.data && response.data.data) ? response.data.data : [];

                    const rows = reportData.map(row => {
                        return [
                            `<small>${row.tid}</small><br><small class='text-muted'>${row.aff_click_id || ''}</small>`,
                            row.link_clicked_at,
                            `${row.offer_name} <small>(${row.offer_id})</small>`,
                            row.ot_country,
                            `${row.device_name || row.ot_os} - ${row.ot_browser}`,
                            row.source || '-',
                            row.google_aid || '-',
                            row.android_id || '-',
                            row.iosidfa || '-',
                            (row.is_conversion == 1) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>'
                        ];
                    });

                    table.clear().rows.add(rows).draw();

                } catch (error) {
                    console.error('Failed to load report:', error);
                    Toast.error('Load Failed', 'Failed to load data. Please try again.');
                }
            }

            // Load initially with empty filters
            loadReport();
        });
    </script>
</body>

</html>