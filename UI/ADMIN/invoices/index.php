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

            // Load Stats
            // Since we don't have a dedicated stats endpoint yet, we might rely on the main dashboard one or assume we need to fix the backend.
            // For now, attempting to fetch from a new 'admin/invoices/stats' endpoint we should probably create, 
            // OR reuse logic. But simplest is to fetch index via API which might return stats if we modify controller.
            // Let's implement fetchStats function.
            fetchStats();

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
                        'X-Requested-With': 'XMLHttpRequest' // Force AJAX for DataTables 
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
                // Fetch with Auth. response will normally be the file content because fetch follows 302.
                // However, R2 might return 200 OK with PDF body.
                const response = await fetch(`${API_BASE_URL}/admin/invoices/${id}/download`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
                        'X-Agency-ID': localStorage.getItem('agency_id'),
                        'Accept': 'application/json' // Actually we want binary/blob?
                    }
                });

                if (response.ok) {
                    // Try to detect if it was a JSON error or the actual file
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const json = await response.json();
                        throw new Error(json.message || "Download failed");
                    }

                    // It is likely the PDF blob now (after redirect)
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `invoice_${id}.pdf`; // Fallback name
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                } else {
                    const json = await response.json();
                    alert("Download Failed: " + (json.message || "Unknown Error"));
                }
            } catch (err) {
                alert("Download Error: " + err.message);
            }
        }
    </script>
</body>