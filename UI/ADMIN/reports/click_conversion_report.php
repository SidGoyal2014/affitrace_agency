<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Click & Conversion Report';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Click & Conversion Report</h1>
            </div>

            <!-- Filters -->
            <div class="card mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
                    data-toggle="collapse" data-target="#filterBody" aria-expanded="true" style="cursor: pointer;">
                    <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                    <i class="fas fa-filter text-gray-400"></i>
                </div>
                <div class="card-body collapse show" id="filterBody">
                    <form id="filterForm">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="from">From Date</label>
                                <input type="date" class="form-control" id="from" name="from">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="to">To Date</label>
                                <input type="date" class="form-control" id="to" name="to">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="conversion_status">Conversion Status</label>
                                <select class="form-control" id="conversion_status" name="conversion_status">
                                    <option value="">All</option>
                                    <option value="converted">Converted</option>
                                    <option value="pending">Not Converted</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="limit">Rows per page</label>
                                <select class="form-control" id="limit" name="limit">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="offer_id">Offer ID</label>
                                <input type="text" class="form-control" id="offer_id" name="offer_id"
                                    placeholder="Offer ID">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="affiliate_id">Affiliate ID</label>
                                <input type="text" class="form-control" id="affiliate_id" name="affiliate_id"
                                    placeholder="Affiliate ID">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="advertiser_id">Advertiser ID</label>
                                <input type="text" class="form-control" id="advertiser_id" name="advertiser_id"
                                    placeholder="Advertiser ID">
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-2"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="reportTable" width="100%"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Click ID</th>
                                    <th>Status</th>
                                    <th>Click Time</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Advertiser</th>
                                    <th>Source</th>
                                    <th>Google AID</th>
                                    <th>Android ID</th>
                                    <th>IDFA</th>
                                    <th>Country</th>
                                    <th>Device</th>
                                    <th>OS</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <tr>
                                    <td colspan="13" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="row align-items-center mt-3">
                        <div class="col-md-6 text-muted">
                            Showing <span id="startEntry">0</span> to <span id="endEntry">0</span> of <span
                                id="totalEntries">0</span> entries
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-end mb-0" id="pagination">
                                    <!-- Populated by JS -->
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let currentPage = 1;

        $(document).ready(function () {
            // Set default dates (Current Month)
            const date = new Date();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            // Format YYYY-MM-DD
            const formatDate = (d) => d.toISOString().split('T')[0];

            // Only set if parameters not present (logic could be added to read URL params)
            if (!$('#from').val()) $('#from').val(formatDate(firstDay));
            if (!$('#to').val()) $('#to').val(formatDate(lastDay));

            loadReport(1);

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                loadReport(1);
            });

            $('#limit').on('change', function () {
                loadReport(1);
            });
        });

        async function loadReport(page) {
            currentPage = page;
            const tbody = $('#reportTableBody');

            // Show loading
            tbody.html('<tr><td colspan="13" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

            try {
                // Construct query params
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);
                params.append('page', page);

                const response = await API.fetchWithAuth(`admin/reports/click-conversion-report?${params.toString()}`, {
                    method: 'POST' // API is POST, but usually reports are GET. The route is POST in my implementation plan. 
                    // Wait, I defined it as POST in api.php.
                    // "Route::post('reports/click-conversion-report'..."
                    // Yet I am passing params in URL? 
                    // FetchWithAuth by default might be JSON body. 
                    // If I use POST, I should send body, not URL params for clarity, but API usually reads both in Laravel Request.
                    // Let's stick to sending data in body for POST.
                });

                // Ah wait, `fetchWithAuth` wrapper usually handles method/body.
                // If I prefer standard POST with JSON body:
                const formObj = Object.fromEntries(formData.entries());
                formObj.page = page;

                // If fetchWithAuth signature is (url, options) or (url, body, method)?
                // Checking demo/chart-area-demo.js or previous files...
                // Previous usage: API.fetchWithAuth('admin/reports/all-offers');
                // Let's assume standard fetch wrapper.
                // I will strictly use the fetchWithAuth with POST and body.

                const postResponse = await API.fetchWithAuth('admin/reports/click-conversion-report', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formObj)
                });

                renderTable(postResponse);

            } catch (error) {
                console.error("Report Error:", error);
                tbody.html('<tr><td colspan="13" class="text-center text-danger">Failed to load data. Please try again.</td></tr>');
            }
        }

        function renderTable(response) {
            const tbody = $('#reportTableBody');
            tbody.empty();

            const result = response.data; // The Laravel paginator object (current_page, data, etc)
            const rows = result.data;

            if (rows.length === 0) {
                tbody.html('<tr><td colspan="13" class="text-center text-muted">No records found.</td></tr>');
                updatePagination(result);
                return;
            }

            rows.forEach(row => {
                const isConv = row.is_conversion == 1;
                const statusBadge = isConv
                    ? '<span class="badge badge-success">Converted</span>'
                    : '<span class="badge badge-secondary">Pending</span>';

                // Format Date
                const dateStr = new Date(row.link_clicked_at).toLocaleString();

                const tr = `
                    <tr>
                        <td>${row.tid}</td>
                        <td>${statusBadge}</td>
                        <td><small>${dateStr}</small></td>
                        <td>
                            <a href="../offers/edit.php?id=${row.offer_id}" title="${row.offer_name || ''}">
                                ${row.offer_id}: ${truncate(row.offer_name, 20)}
                            </a>
                        </td>
                        <td>
                            <a href="../affiliates/edit.php?id=${row.aff_id}" title="${row.aff_name || ''}">
                                ${row.aff_id}: ${truncate(row.aff_name, 15)}
                            </a>
                        </td>
                        <td>
                            <a href="../advertisers/edit.php?id=${row.adv_id}" title="${row.advert_name || ''}">
                                ${row.adv_id}: ${truncate(row.advert_name || 'N/A', 15)}
                            </a>
                        </td>
                        <td>${row.source || '-'}</td>
                        <td>${row.google_aid || '-'}</td>
                        <td>${row.android_id || '-'}</td>
                        <td>${row.iosidfa || '-'}</td>
                        <td>${row.ot_country || '-'}</td>
                        <td>${row.device_name || '-'}</td>
                        <td>${row.ot_os || '-'}</td>
                    </tr>
                `;
                tbody.append(tr);
            });

            updatePagination(result);
        }

        function truncate(str, n) {
            return (str && str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
        }

        function updatePagination(meta) {
            $('#startEntry').text(meta.from || 0);
            $('#endEntry').text(meta.to || 0);
            $('#totalEntries').text(meta.total || 0);

            const pagination = $('#pagination');
            pagination.empty();

            if (meta.last_page <= 1) return;

            // Prev
            const prevDisabled = meta.current_page === 1 ? 'disabled' : '';
            pagination.append(`
                <li class="page-item ${prevDisabled}">
                    <a class="page-link" href="#" onclick="loadReport(${meta.current_page - 1}); return false;">Previous</a>
                </li>
            `);

            // Simple Logic: Show all pages if < 10, otherwise ellipsis logic (simplified for now: show current +/- 2)
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                    const active = i === meta.current_page ? 'active' : '';
                    pagination.append(`
                        <li class="page-item ${active}">
                            <a class="page-link" href="#" onclick="loadReport(${i}); return false;">${i}</a>
                        </li>
                    `);
                } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                    pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }

            // Next
            const nextDisabled = meta.current_page === meta.last_page ? 'disabled' : '';
            pagination.append(`
                <li class="page-item ${nextDisabled}">
                    <a class="page-link" href="#" onclick="loadReport(${meta.current_page + 1}); return false;">Next</a>
                </li>
            `);
        }
    </script>
</body>

</html>