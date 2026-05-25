<?php
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>Conversions Report</h1>
            </div>

            <!-- Filters -->
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
                            <div class="form-group col-md-2">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-search mr-2"></i> Apply Filters
                                </button>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" onclick="exportReport()"
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
                    <h6 class="m-0 font-weight-bold text-success">Conversions Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="reportTable" width="100%"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Conversion ID</th>
                                    <th>Status</th>
                                    <th>Actions</th>                                    
                                    <th>Conversion Time</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Advertiser</th>
                                    <th>Event</th>
                                    <th>Revenue</th>
                                    <th>Payout</th>
                                    <th>Country</th>
                                    <th>Device</th>

                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let dt = null;
        $(document).ready(function () {
            const date = new Date();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            const formatDate = (d) => d.toISOString().split('T')[0];

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('offer_id')) $('#offer_id').val(urlParams.get('offer_id'));
            if (urlParams.get('affiliate_id')) $('#affiliate_id').val(urlParams.get('affiliate_id'));
            if (urlParams.get('advertiser_id')) $('#advertiser_id').val(urlParams.get('advertiser_id'));
            if (urlParams.get('status')) $('#status').val(urlParams.get('status'));

            if (!$('#from').val()) $('#from').val(formatDate(firstDay));
            if (!$('#to').val()) $('#to').val(formatDate(lastDay));

            dt = $('#reportTable').DataTable({
                processing: true,
                language: {
                    emptyTable: 'No conversions found.',
                    zeroRecords: 'No conversions found.',
                    processing: '<div class="d-flex justify-content-center py-3"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>'
                },
                columns: [
                    { data: 'tid', title: 'Conversion ID' },
                    {
                        data: 'status', title: 'Status',
                        render: function (data, type, row) {
                            if (type !== 'display') return (row.status || '');
                            return getStatusBadge(row.status);
                        }
                    },
                    {
                        data: null, title: 'Actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return getActionButtons(row);
                        }
                    },
                    {
                        data: null, title: 'Conversion Time',
                        render: function (data, type, row) {
                            const d = row.conversion_time || row.created_at || '-';
                            return type === 'display' ? '<small>' + d + '</small>' : d;
                        }
                    },
                    {
                        data: 'offer_name', title: 'Offer',
                        render: function (data, type, row) {
                            if (type !== 'display') return (row.offer_id + ' ' + (row.offer_name || ''));
                            return '<a href="../offers/edit.php?id=' + row.offer_id + '" title="' + (row.offer_name || '') + '">' + truncate(row.offer_name, 20) + '</a>';
                        }
                    },
                    {
                        data: 'affiliate_name', title: 'Affiliate',
                        render: function (data, type, row) {
                            if (type !== 'display') return (row.affiliate_id + ' ' + (row.affiliate_name || ''));
                            return '<a href="../affiliates/edit.php?id=' + row.affiliate_id + '" title="' + (row.affiliate_name || '') + '">' + truncate(row.affiliate_name, 15) + '</a>';
                        }
                    },
                    {
                        data: 'advertiser_name', title: 'Advertiser',
                        render: function (data, type, row) {
                            if (type !== 'display') return (row.advertiser_id + ' ' + (row.advertiser_name || ''));
                            return '<a href="../advertisers/edit.php?id=' + row.advertiser_id + '" title="' + (row.advertiser_name || '') + '">' + truncate(row.advertiser_name || 'N/A', 15) + '</a>';
                        }
                    },
                    {
                        data: 'event', title: 'Event',
                        render: function (data, type, row) {
                            if (type !== 'display') return (row.event || 'Conversion');
                            return '<span class="badge badge-info">' + (row.event || 'Conversion') + '</span>';
                        }
                    },
                    {
                        data: 'revenue', title: 'Revenue',
                        render: function (data, type, row) {
                            if (type !== 'display') return parseFloat(row.revenue || 0);
                            return '<span class="text-success font-weight-bold">$' + parseFloat(row.revenue || 0).toFixed(2) + '</span>';
                        }
                    },
                    {
                        data: 'payout', title: 'Payout',
                        render: function (data, type, row) {
                            if (type !== 'display') return parseFloat(row.payout || 0);
                            return '<span class="text-primary font-weight-bold">$' + parseFloat(row.payout || 0).toFixed(2) + '</span>';
                        }
                    },
                    { data: 'country', title: 'Country', defaultContent: '-' },
                    { data: 'device',  title: 'Device',  defaultContent: '-' }
                ],
                order: [[0, 'desc']],
                pageLength: 25
            });

            loadReport();

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                loadReport();
            });

        });

        function getStatusBadge(status) {
            const map = {
                'pending':   '<span class="badge badge-warning">Pending</span>',
                'confirmed': '<span class="badge badge-success">Confirmed</span>',
                'declined':  '<span class="badge badge-danger">Declined</span>',
            };
            return map[status] || `<span class="badge badge-secondary">${status || 'N/A'}</span>`;
        }

        function getActionButtons(row) {
            if (row.status !== 'pending') {
                return '<span class="text-muted">—</span>';
            }
            return `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-success btn-sm" onclick="updateConversionStatus(${row.conv_id || row.id}, 'confirmed')" title="Confirm">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="updateConversionStatus(${row.conv_id || row.id}, 'declined')" title="Decline">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }

        async function updateConversionStatus(id, status) {
            const actionLabel = status.charAt(0).toUpperCase() + status.slice(1);
            if (!confirm(`Are you sure you want to mark this conversion as "${actionLabel}"?`)) {
                return;
            }

            try {
                const response = await API.fetchWithAuth('admin/reports/conversion/update-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, status: status })
                });

                if (response.success) {
                    Toast.success('Success', response.message);
                    loadReport();
                } else {
                    Toast.error('Error', response.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Status update error:', error);
                Toast.error('Error', 'Failed to update conversion status. Please try again.');
            }
        }

        async function loadReport() {
            try {
                const formData = new FormData(document.getElementById('filterForm'));
                const formObj = Object.fromEntries(formData.entries());

                const response = await API.fetchWithAuth('admin/reports/conversions', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formObj)
                });

                renderTable(response);
            } catch (error) {
                console.error('Report Error:', error);
                dt.clear().draw();
                Toast.error('Error', 'Failed to load data. Please try again.');
            }
        }

        function renderTable(response) {
            const rows = response.data;
            dt.clear();
            if (!rows || rows.length === 0) {
                dt.draw();
                return;
            }
            dt.rows.add(rows).draw();
        }

        function truncate(str, n) {
            return (str && str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
        }

        /*function updatePagination(meta) {
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
        }*/

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
                        report_type: 'conversions',
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