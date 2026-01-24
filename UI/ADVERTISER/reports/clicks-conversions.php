<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Click & Conversion Report - Advertiser';
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
                <h1>Click & Conversion Report</h1>
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
                                    <input type="text" class="form-control" id="country" name="country" placeholder="e.g., US, IN">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>OS</label>
                                    <input type="text" class="form-control" id="os" name="os" placeholder="e.g., Windows, Android">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Browser</label>
                                    <input type="text" class="form-control" id="browser" name="browser" placeholder="e.g., Chrome, Firefox">
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
                    <h6 class="m-0 font-weight-bold">Click & Conversion Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Click ID</th>
                                    <th>Country</th>
                                    <th>OS</th>
                                    <th>Browser</th>
                                    <th>Device</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="pagination" class="mt-3"></div>
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

        async function loadReport(filters = {}, page = 1) {
            try {
                // Build query string from filters
                const queryParams = new URLSearchParams();
                Object.keys(filters).forEach(key => {
                    if (filters[key]) {
                        queryParams.append(key, filters[key]);
                    }
                });
                queryParams.append('page', page);

                const queryString = queryParams.toString();
                const endpoint = 'advertiser/reports/clicks-conversions' + (queryString ? '?' + queryString : '');
                
                const response = await API.fetchWithAuth(endpoint);

                // Destroy existing DataTable if it exists
                if (dataTableInstance) {
                    dataTableInstance.destroy();
                    dataTableInstance = null;
                }

                let html = '';
                if (response.data && response.data.data && response.data.data.length > 0) {
                    response.data.data.forEach(row => {
                        const isConversion = row.is_conversion == 1 || row.aff_click_id != '';
                        const statusBadge = isConversion 
                            ? '<span class="badge badge-success">Conversion</span>' 
                            : '<span class="badge badge-info">Click</span>';
                        
                        html += `<tr>
                            <td>${new Date(row.link_clicked_at).toLocaleString()}</td>
                            <td>${row.offer_name || 'N/A'}</td>
                            <td>${row.aff_name || 'N/A'}</td>
                            <td><small>${row.tid || 'N/A'}</small></td>
                            <td>${row.ot_country || 'N/A'}</td>
                            <td>${row.ot_os || 'N/A'}</td>
                            <td>${row.ot_browser || 'N/A'}</td>
                            <td>${row.device_name || 'N/A'}</td>
                            <td>${statusBadge}</td>
                        </tr>`;
                    });
                    
                    $('#reportTableBody').html(html);
                    
                    // Initialize DataTable only when we have data
                    dataTableInstance = $('#dataTable').DataTable({
                        paging: false,
                        searching: false,
                        info: false,
                        order: [[0, 'desc']]
                    });
                    
                    // Build pagination
                    buildPagination(response.data);
                } else {
                    html = '<tr><td colspan="9" class="text-center">No data found for the selected filters</td></tr>';
                    $('#reportTableBody').html(html);
                    $('#pagination').html('');
                }
            } catch (error) {
                console.error('Report load failed:', error);
                
                // Destroy DataTable if error occurs
                if (dataTableInstance) {
                    dataTableInstance.destroy();
                    dataTableInstance = null;
                }
                
                $('#reportTableBody').html('<tr><td colspan="9" class="text-center text-danger">Failed to load report: ' + error.message + '</td></tr>');
                $('#pagination').html('');
            }
        }

        function buildPagination(paginationData) {
            if (!paginationData || !paginationData.last_page) return;
            
            let html = '<nav><ul class="pagination">';
            
            // Previous button
            if (paginationData.current_page > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page - 1}">Previous</a></li>`;
            }
            
            // Page numbers
            for (let i = 1; i <= paginationData.last_page; i++) {
                const active = i === paginationData.current_page ? 'active' : '';
                html += `<li class="page-item ${active}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
            
            // Next button
            if (paginationData.current_page < paginationData.last_page) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page + 1}">Next</a></li>`;
            }
            
            html += '</ul></nav>';
            $('#pagination').html(html);
            
            // Attach click handlers
            $('#pagination a').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                const filters = {
                    from: $('#from').val(),
                    to: $('#to').val(),
                    offer_id: $('#offer_id').val(),
                    country: $('#country').val(),
                    os: $('#os').val(),
                    browser: $('#browser').val(),
                    conversion_status: $('#conversion_status').val()
                };
                loadReport(filters, page);
            });
        }

        $(document).ready(async function () {
            // Load offers for filter dropdown
            await loadOffers();
            
            // Load initial report
            await loadReport();

            // Handle filter form submission
            $('#filterForm').on('submit', function(e) {
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
                loadReport(filters, 1);
            });

            // Handle reset button
            $('#resetBtn').on('click', function() {
                $('#filterForm')[0].reset();
                loadReport({}, 1);
            });
        });
    </script>
</body>

</html>