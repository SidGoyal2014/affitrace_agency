<?php
$pathPrefix = '..';
$activePage = 'invoices';
$pageTitle = 'My Invoices';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h1>My Invoices</h1>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending
                                        Payment</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-pending">$0.00</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-paid">$0.00</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Invoices
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-total">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DataTable -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Invoice History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTableBody">
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

    <script>
        $(document).ready(async function () {
            try {
                const data = await API.fetchWithAuth('advertiser/invoices');
                const invoices = data.invoices || [];

                // Calculate stats
                let pending = 0, paid = 0;
                invoices.forEach(inv => {
                    const amt = parseFloat(inv.total_amount) || 0;
                    if (inv.status === 'PAID') paid += amt;
                    else if (inv.status === 'SENT') pending += amt;
                });

                $('#stat-pending').text('$' + pending.toFixed(2));
                $('#stat-paid').text('$' + paid.toFixed(2));
                $('#stat-total').text(invoices.length);

                // Build table rows
                let html = '';
                if (invoices.length > 0) {
                    invoices.forEach(inv => {
                        let badgeClass = 'secondary';
                        if (inv.status === 'PAID') badgeClass = 'success';
                        else if (inv.status === 'SENT') badgeClass = 'warning';

                        let actionBtn = '';
                        if (inv.status === 'SENT' || inv.status === 'PAID') {
                            actionBtn = `<button onclick="downloadPdf(${inv.id})" class="btn btn-info btn-sm"><i class="fas fa-download"></i> PDF</button>`;
                        } else {
                            actionBtn = `<span class="text-muted">-</span>`;
                        }

                        html += `<tr>
                            <td>${inv.invoice_number}</td>
                            <td>${inv.title || '-'}</td>
                            <td>${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString() : '-'}</td>
                            <td>${inv.due_date ? new Date(inv.due_date).toLocaleDateString() : '-'}</td>
                            <td>$${parseFloat(inv.total_amount || 0).toFixed(2)}</td>
                            <td><span class="badge badge-${badgeClass}">${inv.status}</span></td>
                            <td>${actionBtn}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>';
                }

                $('#invoicesTableBody').html(html);

                // Only initialize DataTable if we have actual rows (not just a message row)
                if (invoices.length > 0) {
                    $('#dataTable').DataTable({
                        order: [[2, 'desc']]
                    });
                }

            } catch (error) {
                console.error('Invoices load failed:', error);
                $('#invoicesTableBody').html('<tr><td colspan="7" class="text-center text-danger">Failed to load invoices: ' + error.message + '</td></tr>');
            }
        });

        async function downloadPdf(id) {
            try {
                const response = await API.fetchWithAuth(`advertiser/invoices/${id}/download`);

                if (response.success && response.url) {
                    window.open(response.url, '_blank');
                } else {
                    throw new Error(response.message || "Failed to get download URL");
                }
            } catch (err) {
                alert("Download Error: " + err.message);
            }
        }
    </script>
</body>

</html>