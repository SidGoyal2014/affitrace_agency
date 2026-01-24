<?php
$pathPrefix = '..';
$activePage = 'invoices';
$pageTitle = 'Invoices';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Invoices</h1>
                <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Create New Invoice
                </a>
            </div>

            <!-- Stats API Error Alert -->
            <div id="api-error" class="alert alert-danger d-none"></div>

            <!-- Dashboard Stats -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Payments Received</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-received">...</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Payment Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-pending">...</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Invoices Not Sent (Drafts)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-drafts">...</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Filters Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse"
                        data-target="#filtersCollapse">
                        <i class="fas fa-filter"></i> Toggle
                    </button>
                </div>
                <div class="collapse show" id="filtersCollapse">
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row">
                                <!-- Advertiser -->
                                <div class="col-md-3 mb-3">
                                    <label for="filter_advertiser">Advertiser</label>
                                    <select class="form-control" id="filter_advertiser">
                                        <option value="">All Advertisers</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter_status">Status</label>
                                    <select class="form-control" id="filter_status">
                                        <option value="">All</option>
                                        <option value="DRAFT">Draft</option>
                                        <option value="SENT">Sent</option>
                                        <option value="PAID">Paid</option>
                                    </select>
                                </div>

                                <!-- Amount Range -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter_amount_min">Min Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="filter_amount_min"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="filter_amount_max">Max Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="filter_amount_max"
                                        placeholder="999999">
                                </div>

                                <!-- Due Date Range -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter_due_from">Due From</label>
                                    <input type="date" class="form-control" id="filter_due_from">
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label for="filter_due_to">Due To</label>
                                    <input type="date" class="form-control" id="filter_due_to">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i> Apply Filters
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Invoices</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice #</th>
                                    <th>Advertiser</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            // Load Stats & Advertisers
            fetchStats();
            loadAdvertisers();

            // Get filter parameters
            function getFilterParams() {
                return {
                    advertiser_id: $('#filter_advertiser').val() || '',
                    status: $('#filter_status').val() || '',
                    amount_min: $('#filter_amount_min').val() || '',
                    amount_max: $('#filter_amount_max').val() || '',
                    due_date_from: $('#filter_due_from').val() || '',
                    due_date_to: $('#filter_due_to').val() || ''
                };
            }

            // Initialize DataTable
            const table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: API_BASE_URL + '/admin/invoices',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'X-Agency-ID': localStorage.getItem('agency_id'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: function (d) {
                        // Merge filter params with DataTables params
                        return $.extend({}, d, getFilterParams());
                    },
                    error: function (xhr, error, code) {
                        if (xhr.status === 401) {
                            API.logout();
                        } else {
                            console.error("DataTable Error:", error);
                        }
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'invoice_number', name: 'invoice_number' },
                    { data: 'advertiser.company', name: 'advertiser.company', defaultContent: 'N/A' },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        render: function (data) { return '$' + parseFloat(data).toFixed(2); }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data) {
                            let color = 'secondary';
                            if (data === 'PAID') color = 'success';
                            if (data === 'SENT') color = 'warning';
                            return `<span class="badge badge-${color}">${data}</span>`;
                        }
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date',
                        render: function (data) { return data ? new Date(data).toLocaleDateString() : ''; }
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                        render: function (data) { return data ? new Date(data).toLocaleDateString() : 'N/A'; }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            let btns = `<a href="detail.php?id=${row.id}" class="btn btn-sm btn-primary shadow-sm" title="View"><i class="fas fa-eye"></i></a> `;

                            if (row.status === 'SENT' || row.status === 'PAID') {
                                btns += `<button onclick="downloadPdf(${row.id})" class="btn btn-sm btn-info shadow-sm" title="Download PDF"><i class="fas fa-download"></i></button>`;
                            }
                            return btns;
                        }
                    }
                ],
                order: [[0, 'desc']]
            });

            // Filter form submit
            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Clear filters
            $('#clearFilters').on('click', function () {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });

            // Load advertisers for filter dropdown
            async function loadAdvertisers() {
                try {
                    const data = await API.fetchWithAuth('admin/advertisers/list');
                    const select = $('#filter_advertiser');

                    const list = data.data ? data.data : data;
                    if (Array.isArray(list)) {
                        list.forEach(adv => {
                            const userId = adv.uid || adv.id;
                            select.append(`<option value="${userId}">${adv.company || 'N/A'} (${adv.name || 'ID: ' + userId})</option>`);
                        });
                    }
                } catch (err) {
                    console.error("Load Advertisers Error:", err);
                }
            }
        });

        async function fetchStats() {
            try {
                // We need an endpoint for this. 
                // Assuming we will update backend to support GET /admin/invoices/stats
                const data = await API.fetchWithAuth('admin/invoices/stats');

                $('#stat-received').text('$' + parseFloat(data.received_amount || 0).toFixed(2));
                $('#stat-pending').text('$' + parseFloat(data.pending_amount || 0).toFixed(2));
                $('#stat-drafts').text(data.draft_count || 0);
            } catch (error) {
                console.error("Stats Error:", error);
                // Fail silently or show dashes
                $('#stat-received').text('-');
                $('#stat-pending').text('-');
                $('#stat-drafts').text('-');
            }
        }

        async function downloadPdf(id) {
            // Initiate download via API logic
            // The prompt says: Browser redirects to signed Cloudflare R2 URL
            // API calls: GET /api/admin/invoices/{id}/download
            // Since it's a redirect, we can just set window.location
            const token = localStorage.getItem('access_token');
            const agencyId = localStorage.getItem('agency_id');
            // We can't easily add headers to standard window.location. 
            // But the backend `download` method does a redirect.
            // If the route is protected by `auth:sanctum`, we need the token in the URL or cookie.
            // Since it's an API route, it expects the Bearer token header. 
            // Browsers don't send Bearer tokens on simple link clicks/window.location.

            // TRICKY: How to download a file from a protected API route that redirects?
            // Option 1: Signed URL approach for the API route itself? No, the user logic is "Clicking calls API -> API 302 Redirect -> R2"
            // If the API requires Auth header, we must use fetch -> get the redirect URL? 
            // Fetch follows redirects automatically usually.
            // If we use fetch, we get the PDF content or the R2 302? 
            // If the backend returns `redirect()->away($url)`, the fetch will follow it and try to download the content.

            // Better approach for secured download:
            // 1. Fetch a "get-download-url" endpoint that returns JSON { url: "..." }
            // 2. Then window.open(url)

            // The prompt says: "Browser redirects to signed Cloudflare R2 URL".
            // It also says: "Clicking ... calls GET .../download".

            // If I click a link `<a href="/api/admin/invoices/1/download?token=...">` logic would work if I passed token in query param.
            // But standard Bearer auth is via header.

            // Let's implement a small helper in `InvoiceService` / `Controller` on backend to existing `download` method?
            // Wait, the current `download` method does `redirect()->away($url)`.
            // If I `fetch` it, the browser will follow the redirect to R2 and download the binary data into JS memory (Blob).
            // Then I can create a blob URL and click it.

            try {
                // Get the signed URL from the API
                const response = await API.fetchWithAuth(`admin/invoices/${id}/download`);

                if (response.success && response.url) {
                    // Open the signed URL directly in a new tab - avoids CORS issues
                    window.open(response.url, '_blank');
                } else {
                    throw new Error(response.message || "Failed to get download URL");
                }
            } catch (err) {
                Toast.error('Download Error', err.message);
            }
        }
    </script>
</body>