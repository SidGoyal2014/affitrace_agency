<?php
include $pathPrefix . '/includes/head.php';
?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/browsers.js"></script>
<script src="../../common/js/os.js"></script>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>Clicks Report</h1>
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
                                <label for="offer_id">Devices</label>
                                <select class="form-control" id="device" name="device">
                                    <option value="">All Devices</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="browser">Browser</label>
                                <select class="form-control" id="browser" name="browser">
                                    <option value="">Select Browser</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="country">Country</label>
                                <select class="form-control" id="country" name="country">
                                    <option value="">Select Country</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="os">Operating Systems</label>
                                <select class="form-control" id="os" name="os">
                                    <option value="">Select Operating System</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="from">From Date</label>
                                <input type="date" class="form-control" id="from" name="from">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="to">To Date</label>
                                <input type="date" class="form-control" id="to" name="to">
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
                                    <th>Actions</th>
                                    <th>Click Time</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Advertiser</th>
                                    <th>Country</th>
                                    <th>IP</th>
                                    <th>Device</th>
                                    <th>OS</th>
                                    <th>Browser</th>
                                    <th>User Agent</th>
                                    <th>Brand</th>
                                    <th>Currency</th>
                                    <th>Payout</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody"></tbody>
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

        let dtInstance = null;

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

            const tableColumns = [
                { data: 'tid', title: 'Click ID' },
                {
                    data: null, title: 'Actions',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<button class="btn btn-info btn-sm view-details" data-id="${row.tid}">View Details</button>`;
                    }
                },
                {
                    data: null, title: 'Click Time',
                    render: function (data, type, row) {
                        const d = row.link_clicked_at || '-';
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
                    data: 'aff_name', title: 'Affiliate',
                    render: function (data, type, row) {
                        if (type !== 'display') return (row.aff_id + ' ' + (row.aff_name || ''));
                        return '<a href="../affiliates/edit.php?id=' + row.aff_id + '" title="' + (row.aff_name || '') + '">' + truncate(row.aff_name, 15) + '</a>';
                    }
                },
                {
                    data: 'advert_name', title: 'Advertiser',
                    render: function (data, type, row) {
                        if (type !== 'display') return (row.adv_id + ' ' + (row.advert_name || ''));
                        return '<a href="../advertisers/edit.php?id=' + row.adv_id + '" title="' + (row.advert_name || '') + '">' + truncate(row.advert_name || 'N/A', 15) + '</a>';
                    }
                },
                { data: 'ot_country', title: 'Country', defaultContent: '-' },
                { data: 'ip', title: 'IP', defaultContent: '-' },
                { data: 'device_name', title: 'Device', defaultContent: '-' },
                { data: 'ot_os', title: 'OS', defaultContent: '-' },
                { data: 'ot_browser', title: 'Browser', defaultContent: '-' },
                { data: 'user_agent', title: 'User Agent', defaultContent: '-' },
                { data: 'brand', title: 'Brand', defaultContent: '-' },
                { data: 'currency', title: 'Currency', defaultContent: '-' },
                { data: 'payout', title: 'Payout', defaultContent: '0.00' },
                { data: 'revenue', title: 'Revenue', defaultContent: '0.00' },

                // Additional columns (visible: false)
                { data: 'session_id', title: 'Session ID', visible: false, defaultContent: '-' },
                { data: 'browser_version', title: 'Browser Version', visible: false, defaultContent: '-' },
                { data: 'screen_resolution', title: 'Screen Resolution', visible: false, defaultContent: '-' },
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
                { data: 'carrier', title: 'Carrier', visible: false, defaultContent: '-' },
                { data: 'region', title: 'Region', visible: false, defaultContent: '-' },
                { data: 'city', title: 'City', visible: false, defaultContent: '-' },

                // Sub parameters 1-10
                { data: 'sub1', title: 'Sub 1', visible: false, defaultContent: '-' },
                { data: 'sub2', title: 'Sub 2', visible: false, defaultContent: '-' },
                { data: 'sub3', title: 'Sub 3', visible: false, defaultContent: '-' },
                { data: 'sub4', title: 'Sub 4', visible: false, defaultContent: '-' },
                { data: 'sub5', title: 'Sub 5', visible: false, defaultContent: '-' },
                { data: 'sub6', title: 'Sub 6', visible: false, defaultContent: '-' },
                { data: 'sub7', title: 'Sub 7', visible: false, defaultContent: '-' },
                { data: 'sub8', title: 'Sub 8', visible: false, defaultContent: '-' },
                { data: 'sub9', title: 'Sub 9', visible: false, defaultContent: '-' },
                { data: 'sub10', title: 'Sub 10', visible: false, defaultContent: '-' }
            ];

            // Sync HTML thead with the JS columns config to support column toggles dynamically
            const theadRow = $('#reportTable thead tr');
            theadRow.empty();
            tableColumns.forEach(col => {
                theadRow.append(`<th>${col.title || ''}</th>`);
            });

            dtInstance = $('#reportTable').DataTable({
                processing: true,
                language: {
                    emptyTable: 'No clicks found.',
                    zeroRecords: 'No clicks found.',
                    processing: '<div class="d-flex justify-content-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                },
                dom: "<'row'<'col-sm-12 col-md-6'lB><'col-sm-12 col-md-6'f>>rtip",
                buttons: [
                    {
                        extend: 'colvis',
                        className: 'btn btn-outline-primary btn-sm',
                        text: '<i class="fas fa-columns mr-1"></i> Columns'
                    }
                ],
                columns: tableColumns,
                order: [[0, 'desc']],
                pageLength: 25
            });


            // Populate Device dropdown
            API.fetchWithAuth('<?php echo $user_type; ?>/reports/devices').then(function (res) {
                const sel = $('#device');
                ((res && Array.isArray(res.data)) ? res.data : []).forEach(function (d) {
                    sel.append('<option value="' + d.id + '">' + d.device_name + '</option>');
                });
            }).catch(function (err) {
                console.warn('Could not load devices:', err);
            });


            try {
                const countrySelect = $('select[name="country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });
            } catch (e) { console.error("Failed to load countries"); }

            try {
                const browserSelect = $('select[name="browser"]');
                Object.entries(BROWSERS).forEach(([code, name]) => {
                    browserSelect.append(new Option(name, name));
                });
            } catch (e) { console.error("Failed to load browsers"); }

            try {
                const osSelect = $('select[name="os"]');
                Object.entries(OPERATING_SYSTEMS).forEach(([code, name]) => {
                    osSelect.append(new Option(name, name));
                });
            } catch (e) { console.error("Failed to load operating systems"); }

            loadReport();

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                loadReport();
            });


        });

        async function loadReport(page) {
            try {
                const formData = new FormData(document.getElementById('filterForm'));
                const formObj = Object.fromEntries(formData.entries());


                const response = await API.fetchWithAuth('<?php echo $user_type; ?>/reports/clicks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formObj)
                });
                if (!response) {
                    return;
                }
                //console.log(response);
                renderTable(response);

            } catch (error) {
                console.error("Report Error:", error);
                dtInstance.clear().draw();
                Toast.error('Report Error', 'Failed to load data. Please try again.');
            }
        }


        function renderTable(response) {
            const rows = (response && Array.isArray(response.data)) ? response.data : [];
            dtInstance.clear();
            if (rows && rows.length > 0) {
                dtInstance.rows.add(rows);
            }
            dtInstance.draw();
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

                const response = await API.fetchWithAuth('<?php echo $user_type; ?>/reports/export', {
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
    // ---------- New Code ----------
async function fetchClickDetails(id) {
    try {
        const response = await API.fetchWithAuth('<?php echo $user_type; ?>/reports/click-detail/' + id);
        if (response.success && response.data) {
            populateDetailsModal(response.data);
            $('#detailsModal').modal('show');
        } else {
            Toast.error('Error', response.message || 'Failed to load details');
        }
    } catch (err) {
        console.error('Click details fetch error:', err);
        Toast.error('Error', 'Unable to retrieve click details');
    }
}

function populateDetailsModal(record) {
    const tbody = $('#detailsTable');
    tbody.empty();
    const fields = {
        'Click ID': record.tid,
        'Click Time': record.link_clicked_at,
        'Offer': record.offer_name,
        'Affiliate': record.aff_name,
        'Advertiser': record.advert_name,
        'Country': record.ot_country,
        'IP': record.ip,
        'Device': record.device_name,
        'OS': record.ot_os,
        'Browser': record.ot_browser,
        'User Agent': record.user_agent,
        'Brand': record.brand,
        'Currency': record.currency,
        'Payout': record.payout,
        'Revenue': record.revenue
    };
    for (const [label, value] of Object.entries(fields)) {
        tbody.append(`<tr><th>${label}</th><td>${value ?? '-'}</td></tr>`);
    }
}

// Event delegation for dynamically generated buttons
$(document).on('click', '.view-details', function () {
    const id = $(this).data('id');
    if (id) {
        fetchClickDetails(id);
    }
});
</script>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Click Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-borderless" id="detailsTable">
                        <!-- Rows will be populated via JavaScript -->
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>