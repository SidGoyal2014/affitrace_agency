<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Clicks Report';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Clicks Report</h1>
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
                            <div class="form-group col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-2"></i> Apply Filters
                                </button>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-success btn-block" onclick="exportReport()"
                                    title="Export to CSV">
                                    <i class="fas fa-file-export"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Clicks Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="reportTable" width="100%"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Click ID</th>
                                    <th>Click Time</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Advertiser</th>
                                    <th>Country</th>
                                    <th>IP</th>
                                    <th>Device</th>
                                    <th>OS</th>
                                    <th>Browser</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">Loading...</td>
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
            const date = new Date();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            const formatDate = (d) => d.toISOString().split('T')[0];

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('offer_id')) $('#offer_id').val(urlParams.get('offer_id'));
            if (urlParams.get('affiliate_id')) $('#affiliate_id').val(urlParams.get('affiliate_id'));
            if (urlParams.get('advertiser_id')) $('#advertiser_id').val(urlParams.get('advertiser_id'));

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
            tbody.html('<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

            try {
                const formData = new FormData(document.getElementById('filterForm'));
                const formObj = Object.fromEntries(formData.entries());
                formObj.page = page;

                const response = await API.fetchWithAuth('admin/reports/clicks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formObj)
                });

                renderTable(response);

            } catch (error) {
                console.error("Report Error:", error);
                tbody.html('<tr><td colspan="10" class="text-center text-danger">Failed to load data. Please try again.</td></tr>');
            }
        }

        function renderTable(response) {
            const tbody = $('#reportTableBody');
            tbody.empty();

            const result = response.data;
            const rows = result.data;

            if (rows.length === 0) {
                tbody.html('<tr><td colspan="10" class="text-center text-muted">No records found.</td></tr>');
                updatePagination(result);
                return;
            }

            rows.forEach(row => {
                const dateStr = new Date(row.link_clicked_at).toLocaleString();

                const tr = `
                    <tr>
                        <td>${row.tid}</td>
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
                        <td>${row.ot_country || '-'}</td>
                        <td>${row.ip || '-'}</td>
                        <td>${row.device_name || '-'}</td>
                        <td>${row.ot_os || '-'}</td>
                        <td>${row.ot_browser || '-'}</td>
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

            const prevDisabled = meta.current_page === 1 ? 'disabled' : '';
            pagination.append(`
                <li class="page-item ${prevDisabled}">
                    <a class="page-link" href="#" onclick="loadReport(${meta.current_page - 1}); return false;">Previous</a>
                </li>
            `);

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

            const nextDisabled = meta.current_page === meta.last_page ? 'disabled' : '';
            pagination.append(`
                <li class="page-item ${nextDisabled}">
                    <a class="page-link" href="#" onclick="loadReport(${meta.current_page + 1}); return false;">Next</a>
                </li>
            `);
        }

        async function exportReport() {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                const formData = new FormData(document.getElementById('filterForm'));
                const filters = {
                    date_from: formData.get('from'),
                    date_to: formData.get('to'),
                    offer_id: formData.get('offer_id'),
                    affiliate_id: formData.get('affiliate_id')
                };

                const response = await API.fetchWithAuth('admin/reports/export', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        report_type: 'clicks',
                        format: 'csv',
                        filters: filters
                    })
                });

                if (response.success) {
                    Toast.success('Export Queued', 'Go to Reports > My Exports to download your file when ready. Export ID: ' + response.data.export_id);
                } else {
                    Toast.error('Export Failed', response.message || 'Unknown error');
                }

            } catch (error) {
                console.error('Export error:', error);
                Toast.error('Export Failed', 'Failed to start export. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    </script>
</body>

</html>