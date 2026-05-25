<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Conversions Report';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>My Conversions</h1>
            </div>

            <div class="card mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
                    data-toggle="collapse" data-target="#filterBody" aria-expanded="true" style="cursor: pointer;">
                    <h6 class="m-0 font-weight-bold text-success">Filters</h6>
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
                                <label for="offer_id">Offer</label>
                                <select class="form-control" id="offer_id" name="offer_id">
                                    <option value="">All Offers</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="limit">Rows per page</label>
                                <select class="form-control" id="limit" name="limit">
                                    <option value="25">25</option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-search mr-2"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Conversions Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="reportTable" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Time</th>
                                    <th>Offer</th>
                                    <th>Event</th>
                                    <th>Payout</th>
                                    <th>Country</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-md-6 text-muted">
                            Showing <span id="startEntry">0</span> to <span id="endEntry">0</span> of <span
                                id="totalEntries">0</span> entries
                        </div>
                        <div class="col-md-6">
                            <nav>
                                <ul class="pagination justify-content-end mb-0" id="pagination"></ul>
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

            if (!$('#from').val()) $('#from').val(formatDate(firstDay));
            if (!$('#to').val()) $('#to').val(formatDate(lastDay));

            loadOffers();
            loadReport(1);

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                loadReport(1);
            });
        });

        async function loadOffers() {
            try {
                const response = await API.fetchWithAuth('affiliate/offers-approved');
                if (response.data) {
                    response.data.forEach(offer => {
                        $('#offer_id').append(`<option value="${offer.oid}">${offer.offer_name}</option>`);
                    });
                }
            } catch (error) {
                console.error('Failed to load offers:', error);
            }
        }

        async function loadReport(page) {
            currentPage = page;
            const tbody = $('#reportTableBody');
            tbody.html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>');

            try {
                const formData = new FormData(document.getElementById('filterForm'));
                const formObj = Object.fromEntries(formData.entries());
                formObj.page = page;

                const response = await API.fetchWithAuth('affiliate/reports/conversions', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formObj)
                });

                renderTable(response);
            } catch (error) {
                console.error("Report Error:", error);
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load data.</td></tr>');
            }
        }

        function renderTable(response) {
            const tbody = $('#reportTableBody');
            tbody.empty();

            const result = response.data;
            const rows = result.data;

            if (!rows || rows.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center text-muted">No conversions found.</td></tr>');
                updatePagination(result);
                return;
            }

            rows.forEach(row => {
                const dateStr = row.conversion_time || row.created_at || '-';
                const tr = `
                    <tr>
                        <td>${row.tid || row.id || '-'}</td>
                        <td><small>${dateStr}</small></td>
                        <td>${row.offer_id}: ${row.offer_name || ''}</td>
                        <td><span class="badge badge-info">${row.event || 'Conversion'}</span></td>
                        <td class="text-success font-weight-bold">$${parseFloat(row.payout || 0).toFixed(2)}</td>
                        <td>${row.country || '-'}</td>
                    </tr>
                `;
                tbody.append(tr);
            });

            updatePagination(result);
        }

        function updatePagination(meta) {
            $('#startEntry').text(meta.from || 0);
            $('#endEntry').text(meta.to || 0);
            $('#totalEntries').text(meta.total || 0);

            const pagination = $('#pagination');
            pagination.empty();

            if (!meta.last_page || meta.last_page <= 1) return;

            if (meta.current_page > 1) {
                pagination.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReport(${meta.current_page - 1}); return false;">Previous</a></li>`);
            }
            for (let i = Math.max(1, meta.current_page - 2); i <= Math.min(meta.last_page, meta.current_page + 2); i++) {
                const active = i === meta.current_page ? 'active' : '';
                pagination.append(`<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadReport(${i}); return false;">${i}</a></li>`);
            }
            if (meta.current_page < meta.last_page) {
                pagination.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReport(${meta.current_page + 1}); return false;">Next</a></li>`);
            }
        }
    </script>
</body>

</html>