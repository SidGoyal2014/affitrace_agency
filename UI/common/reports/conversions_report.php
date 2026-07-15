<?php
include $pathPrefix . '/includes/head.php';
?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

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
                            <div class="form-group col-md-3">
                                <label for="offer_id">Offer</label>
                                <select class="form-control" id="offer_id" name="offer_id">
                                    <option value="">All</option>
                                    <!-- options will be populated via JS -->
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="affiliate_id">Affiliate</label>
                                <select class="form-control" id="affiliate_id" name="affiliate_id">
                                    <option value="">All</option>
                                    <!-- options will be populated via JS -->
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="advertiser_id">Advertiser</label>
                                <select class="form-control" id="advertiser_id" name="advertiser_id">
                                    <option value="">All</option>
                                    <!-- options will be populated via JS -->
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
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script>
        let dt = null;
        $(document).ready(function () {
            const date = new Date();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            const formatDate = (d) => d.toISOString().split('T')[0];

            const urlParams = new URLSearchParams(window.location.search);
            // Initialize filter dropdowns
            loadDropdowns();
            if (urlParams.get('offer_id')) $('#offer_id').val(urlParams.get('offer_id'));
            if (urlParams.get('affiliate_id')) $('#affiliate_id').val(urlParams.get('affiliate_id'));
            if (urlParams.get('advertiser_id')) $('#advertiser_id').val(urlParams.get('advertiser_id'));
            if (urlParams.get('status')) $('#status').val(urlParams.get('status'));

            if (!$('#from').val()) $('#from').val(formatDate(firstDay));
            if (!$('#to').val()) $('#to').val(formatDate(lastDay));

            const tableColumns = [
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
                        return '<span class="text-success font-weight-bold">' + row.currency + ' ' + parseFloat(row.revenue || 0).toFixed(2) + '</span>';
                    }
                },
                {
                    data: 'payout', title: 'Payout',
                    render: function (data, type, row) {
                        if (type !== 'display') return parseFloat(row.payout || 0);
                        return '<span class="text-primary font-weight-bold">' + row.currency + ' ' + parseFloat(row.payout || 0).toFixed(2) + '</span>';
                    }
                },
                { data: 'country', title: 'Country', defaultContent: '-' },
                { data: 'device', title: 'Device', defaultContent: '-' },

                // Additional columns (visible: false by default)
                { data: 'aff_click_id', title: 'Affiliate Click ID', visible: false, defaultContent: '-' },
                { data: 'click_id', title: 'Click ID', visible: false, defaultContent: '-' },
                { data: 'transaction_id', title: 'Transaction ID', visible: false, defaultContent: '-' },
                { data: 'session_id', title: 'Session ID', visible: false, defaultContent: '-' },
                { data: 'rejection_reason', title: 'Rejection Reason', visible: false, defaultContent: '-' },
                { data: 'status_changed_by', title: 'Status Changed By', visible: false, defaultContent: '-' },
                { data: 'status_changed_at', title: 'Status Changed At', visible: false, defaultContent: '-' },
                { 
                    data: 'is_flagged', title: 'Flagged', visible: false, 
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { data: 'flag_reason', title: 'Flag Reason', visible: false, defaultContent: '-' },
                { data: 'click_time', title: 'Click Time', visible: false, defaultContent: '-' },
                { data: 'click_to_conv_time', title: 'Click-to-Conv Time', visible: false, defaultContent: '-' },
                { data: 'click_to_conv_display', title: 'Click-to-Conv Duration', visible: false, defaultContent: '-' },
                { data: 'postback_fired_at', title: 'Postback Fired At', visible: false, defaultContent: '-' },
                { data: 'postback_received_at', title: 'Postback Received At', visible: false, defaultContent: '-' },
                { data: 'landing_page_id', title: 'Landing Page ID', visible: false, defaultContent: '-' },
                { data: 'landing_page_url', title: 'Landing Page URL', visible: false, defaultContent: '-' },
                { data: 'affiliate_manager_id', title: 'Affiliate Manager ID', visible: false, defaultContent: '-' },
                { data: 'click_ip', title: 'Click IP', visible: false, defaultContent: '-' },
                { data: 'conversion_ip', title: 'Conversion IP', visible: false, defaultContent: '-' },
                { 
                    data: 'ip_match', title: 'IP Match', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { data: 'tracking_url', title: 'Tracking URL', visible: false, defaultContent: '-' },
                { data: 'redirect_url', title: 'Redirect URL', visible: false, defaultContent: '-' },
                { data: 'tracking_domain', title: 'Tracking Domain', visible: false, defaultContent: '-' },
                { data: 'region', title: 'Region', visible: false, defaultContent: '-' },
                { data: 'city', title: 'City', visible: false, defaultContent: '-' },
                { data: 'carrier', title: 'Carrier', visible: false, defaultContent: '-' },
                { data: 'connection_type', title: 'Connection Type', visible: false, defaultContent: '-' },
                { data: 'device_type', title: 'Device Type', visible: false, defaultContent: '-' },
                { data: 'device_id_raw', title: 'Raw Device ID', visible: false, defaultContent: '-' },
                { data: 'os_version', title: 'OS Version', visible: false, defaultContent: '-' },
                { data: 'browser_version', title: 'Browser Version', visible: false, defaultContent: '-' },
                { data: 'screen_resolution', title: 'Screen Resolution', visible: false, defaultContent: '-' },
                { data: 'fraud_score', title: 'Fraud Score', visible: false, defaultContent: '0' },
                { data: 'fraud_reason', title: 'Fraud Reason', visible: false, defaultContent: '-' },
                { 
                    data: 'is_bot', title: 'Is Bot', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { 
                    data: 'is_vpn', title: 'Is VPN', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { 
                    data: 'is_datacenter', title: 'Is Datacenter', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { 
                    data: 'is_duplicate_ip', title: 'Is Duplicate IP', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { 
                    data: 'is_duplicate_device', title: 'Is Duplicate Device', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { 
                    data: 'geo_mismatch', title: 'Geo Mismatch', visible: false,
                    render: function(data) { return data ? 'Yes' : 'No'; }
                },
                { data: 'ctit_flag', title: 'CTIT Flag', visible: false, defaultContent: '-' },
                { data: 'attribution_type', title: 'Attribution Type', visible: false, defaultContent: '-' },
                { data: 'impression_time', title: 'Impression Time', visible: false, defaultContent: '-' },
                { data: 'view_to_click_time', title: 'View-to-Click Time', visible: false, defaultContent: '-' },
                { data: 'attribution_window', title: 'Attribution Window', visible: false, defaultContent: '-' },
                { data: 'postback_status', title: 'Postback Status', visible: false, defaultContent: '-' },
                { data: 'postback_http_code', title: 'Postback HTTP Code', visible: false, defaultContent: '-' },
                { data: 'postback_url_fired', title: 'Postback URL Fired', visible: false, defaultContent: '-' },
                { data: 'postback_attempts', title: 'Postback Attempts', visible: false, defaultContent: '0' },
                { data: 'adv_postback_url', title: 'Adv Postback URL', visible: false, defaultContent: '-' },

                // Sub parameters 1-10
                { data: 'aff_sub1', title: 'Aff Sub 1', visible: false, defaultContent: '-' },
                { data: 'aff_sub2', title: 'Aff Sub 2', visible: false, defaultContent: '-' },
                { data: 'aff_sub3', title: 'Aff Sub 3', visible: false, defaultContent: '-' },
                { data: 'aff_sub4', title: 'Aff Sub 4', visible: false, defaultContent: '-' },
                { data: 'aff_sub5', title: 'Aff Sub 5', visible: false, defaultContent: '-' },
                { data: 'aff_sub6', title: 'Aff Sub 6', visible: false, defaultContent: '-' },
                { data: 'aff_sub7', title: 'Aff Sub 7', visible: false, defaultContent: '-' },
                { data: 'aff_sub8', title: 'Aff Sub 8', visible: false, defaultContent: '-' },
                { data: 'aff_sub9', title: 'Aff Sub 9', visible: false, defaultContent: '-' },
                { data: 'aff_sub10', title: 'Aff Sub 10', visible: false, defaultContent: '-' },

                { data: 'adv_sub1', title: 'Adv Sub 1', visible: false, defaultContent: '-' },
                { data: 'adv_sub2', title: 'Adv Sub 2', visible: false, defaultContent: '-' },
                { data: 'adv_sub3', title: 'Adv Sub 3', visible: false, defaultContent: '-' },
                { data: 'adv_sub4', title: 'Adv Sub 4', visible: false, defaultContent: '-' },
                { data: 'adv_sub5', title: 'Adv Sub 5', visible: false, defaultContent: '-' },
                { data: 'adv_sub6', title: 'Adv Sub 6', visible: false, defaultContent: '-' },
                { data: 'adv_sub7', title: 'Adv Sub 7', visible: false, defaultContent: '-' },
                { data: 'adv_sub8', title: 'Adv Sub 8', visible: false, defaultContent: '-' },
                { data: 'adv_sub9', title: 'Adv Sub 9', visible: false, defaultContent: '-' },
                { data: 'adv_sub10', title: 'Adv Sub 10', visible: false, defaultContent: '-' }
            ];

            // Sync HTML thead with the JS columns config to support column toggles dynamically
            const theadRow = $('#reportTable thead tr');
            theadRow.empty();
            tableColumns.forEach(col => {
                theadRow.append(`<th>${col.title || ''}</th>`);
            });

            dt = $('#reportTable').DataTable({
                processing: true,
                language: {
                    emptyTable: 'No conversions found.',
                    zeroRecords: 'No conversions found.',
                    processing: '<div class="d-flex justify-content-center py-3"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>'
                },
                dom: "<'row'<'col-sm-12 col-md-6'lB><'col-sm-12 col-md-6'f>>rtip",
                buttons: [
                    {
                        extend: 'colvis',
                        className: 'btn btn-outline-success btn-sm',
                        text: '<i class="fas fa-columns mr-1"></i> Columns'
                    }
                ],
                columns: tableColumns,
                order: [[0, 'desc']],
                pageLength: 25
            });

            loadReport();

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                loadReport();
            });

        });

        async function loadDropdowns() {
            // Load all dropdown data in a single request

            try {
                const resp = await API.fetchWithAuth('admin/reports/conversions-dropdown-data');
                //if (resp.success) {
                //const { offers, affiliates, advertisers } = resp.data;
                const offers = resp.offers;
                const affiliates = resp.affiliates;
                const advertisers = resp.advertisers;
                const offerSelect = $('#offer_id');
                offers.forEach(o => offerSelect.append(`<option value="${o.id}">${o.offer_name}</option>`));
                const affSelect = $('#affiliate_id');
                affiliates.forEach(a => affSelect.append(`<option value="${a.id}">${a.name}</option>`));
                const advSelect = $('#advertiser_id');
                advertisers.forEach(a => advSelect.append(`<option value="${a.id}">${a.name}</option>`));
                //}
            } catch (e) {
                console.error('Dropdown load error', e);
            }
        }

        async function showConversionDetail(id) {
            try {
                const response = await API.fetchWithAuth(`admin/reports/conversion-detail/${id}`);
                if (response.success) {
                    // Build HTML details
                    let html = '<div class="detail-container">';
                    let rowContent = '';
                    let pairCount = 0;
                    for (const [key, value] of Object.entries(response.data)) {
                        if (value != null && value != '' && !['id', 'offer_id', 'affiliate_id', 'advertiser_id', 'device_id', 'created_at', 'updated_at'].includes(key)) {
                            rowContent += `<dt class="col-sm-2 text-capitalize">${key.replace('_', ' ')}</dt><dd class="col-sm-4">${value}</dd>`;
                            pairCount++;
                            if (pairCount === 2) {
                                html += `<div class="detail-row row">${rowContent}</div>`;
                                rowContent = '';
                                pairCount = 0;
                            }
                        }
                    }
                    if (rowContent) {
                        html += `<div class="detail-row row">${rowContent}</div>`;
                    }
                    html += '</div>';
                    // Ensure modal exists
                    if (!$('#conversionDetailModal').length) {
                        const modalHtml = `<style>.detail-row:nth-child(odd){background:#f9f9f9;}.detail-row:nth-child(even){background:#ffffff;}</style>
<div class="modal fade" id="conversionDetailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Conversion Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="detailModalBody"></div>
    </div>
  </div>
</div>`;
                        $('body').append(modalHtml);
                    }
                    $('#detailModalBody').html(html);
                    $('#conversionDetailModal').modal('show');
                } else {
                    Toast.error('Error', response.message || 'Failed to load detail');
                }
            } catch (e) {
                console.error(e);
                Toast.error('Error', 'Unable to fetch conversion detail');
            }
        }

        function getStatusBadge(status) {
            const map = {
                'pending': '<span class="badge badge-warning">Pending</span>',
                'confirmed': '<span class="badge badge-success">Confirmed</span>',
                'declined': '<span class="badge badge-danger">Declined</span>',
            };
            return map[status] || `<span class="badge badge-secondary">${status || 'N/A'}</span>`;
        }

        function getActionButtons(row) {
            let actions = `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-info btn-sm" onclick="showConversionDetail(${row.id || row.conv_id})" title="Details">
                        <i class="fas fa-eye"></i>
                    </button>`;

            if (row.status === 'pending') {
                actions += `
                    <button class="btn btn-success btn-sm" onclick="updateConversionStatus(${row.conv_id || row.id}, 'confirmed')" title="Confirm">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="updateConversionStatus(${row.conv_id || row.id}, 'declined')" title="Decline">
                        <i class="fas fa-times"></i>
                    </button>`;
            }
            actions += `</div>`;
            return actions;
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