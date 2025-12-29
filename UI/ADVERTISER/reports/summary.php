<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Summary Report - Advertiser';
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

            <!-- Filters Card -->
            <div class="card-modern mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Filters</h6>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" id="from" name="from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" id="to" name="to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Offer</label>
                                    <select class="form-control" id="offer_id" name="offer_id">
                                        <option value="">All Offers</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                        placeholder="e.g., US, IN">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>OS</label>
                                    <input type="text" class="form-control" id="os" name="os"
                                        placeholder="e.g., Windows, Android">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Browser</label>
                                    <input type="text" class="form-control" id="browser" name="browser"
                                        placeholder="e.g., Chrome, Firefox">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Conversion Status</label>
                                    <select class="form-control" id="conversion_status" name="conversion_status">
                                        <option value="">All</option>
                                        <option value="converted">Conversions Only</option>
                                        <option value="pending">Clicks Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                                        <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
        let dataTableInstance = null;

        async function loadOffers() {
            try {
                const data = await API.fetchWithAuth('advertiser/offers/my');
                const select = $('#offer_id');
                if (data.offers && data.offers.length > 0) {
                    data.offers.forEach(offer => {
                        select.append(`<option value="${offer.id}">${offer.offer_name}</option>`);
                    });
                }
            } catch (error) {
                console.error('Failed to load offers:', error);
            }
        }

        async function loadReport(filters = {}) {
            try {
                // Build query string from filters
                const queryParams = new URLSearchParams();
                Object.keys(filters).forEach(key => {
                    if (filters[key]) {
                        queryParams.append(key, filters[key]);
                    }
                });

                const queryString = queryParams.toString();
                const endpoint = 'advertiser/reports/summary' + (queryString ? '?' + queryString : '');

                const data = await API.fetchWithAuth(endpoint);

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
                } else {
                    html = '<tr><td colspan="5" class="text-center">No data found for the selected filters</td></tr>';
                }

                $('#summaryTableBody').html(html);

                // Destroy existing DataTable if it exists
                if (dataTableInstance) {
                    dataTableInstance.destroy();
                }

                // Initialize DataTable
                dataTableInstance = $('#dataTable').DataTable({
                    order: [[0, 'desc']]
                });
            } catch (error) {
                console.error('Summary load failed:', error);
                $('#summaryTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load report: ' + error.message + '</td></tr>');
            }
        }

        $(document).ready(async function () {
            // Load offers for filter dropdown
            await loadOffers();

            // Load initial report
            await loadReport();

            // Handle filter form submission
            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                const filters = {
                    from: $('#from').val(),
                    to: $('#to').val(),
                    offer_id: $('#offer_id').val(),
                    country: $('#country').val(),
                    os: $('#os').val(),
                    browser: $('#browser').val(),
                    conversion_status: $('#conversion_status').val()
                };
                loadReport(filters);
            });

            // Handle reset button
            $('#resetBtn').on('click', function () {
                $('#filterForm')[0].reset();
                loadReport();
            });
        });
    </script>
</body>

</html>