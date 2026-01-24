<?php
$pathPrefix = '..';
$activePage = 'invoices';
$pageTitle = 'Invoice Details';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="index.php" class="btn btn-secondary btn-sm shadow-sm mr-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="h3 mb-0 text-gray-800">Invoice: <span id="invoiceNumber">Loading...</span></h1>
                    <span id="statusBadge" class="ml-3 badge badge-secondary" style="font-size: 1rem;">...</span>
                </div>
            </div>

            <div class="row" id="mainInvoiceContent" style="display:none;">
                <!-- Left Column: Details -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Invoice Details</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Advertiser:</strong> <span id="advertiserName">...</span></p>
                            <p class="mb-2"><strong>Title:</strong> <span id="invoiceTitle">...</span></p>
                            <p class="mb-2"><strong>Invoice Date:</strong> <span id="invoiceDate">...</span></p>
                            <p class="mb-2"><strong>Due Date:</strong> <span id="dueDate">...</span></p>
                            <hr>
                            <div class="d-flex justify-content-between mt-3 text-primary">
                                <h4 class="font-weight-bold">Total Amount:</h4>
                                <h4 class="font-weight-bold" id="totalAmount">...</h4>
                            </div>
                            <div id="paymentInfo" class="mt-3 d-none">
                                <div class="alert alert-success text-center px-2 py-1 mb-0">
                                    <small><i class="fas fa-check-circle"></i> Paid on <span id="paidAt"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3" id="actionHelpText">Loading allowed actions...</p>
                            <div id="actionButtons"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="pageLoading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Send Invoice Modal -->
    <div class="modal fade" id="sendInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-left-warning">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-warning">Confirm Send Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This action will:</p>
                    <ul class="mb-3">
                        <li>Generate and store the invoice PDF</li>
                        <li>Lock the invoice</li>
                        <li>Mark it as <span class="badge badge-warning">SENT</span></li>
                    </ul>
                    <p class="font-weight-bold mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmSendBtn">
                        <i class="fas fa-paper-plane mr-2"></i> Send Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-left-success">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-success">Confirm Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Confirm payment received?</p>
                    <p>This will mark the invoice as <span class="badge badge-success">PAID</span>.</p>
                    <p class="font-weight-bold mb-0">This action is final.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPayBtn">
                        <i class="fas fa-check-circle mr-2"></i> Mark as Paid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const urlParams = new URLSearchParams(window.location.search);
            const invoiceId = urlParams.get('id');

            if (!invoiceId) {
                Toast.error('Error', 'Invalid Invoice ID');
                window.location.href = 'index.php';
                return;
            }

            loadInvoice(invoiceId);

            // Send Handler
            $('#confirmSendBtn').click(async function () {
                try {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                    const response = await API.fetchWithAuth(`admin/invoices/${invoiceId}/send`, { method: 'POST' });

                    if (response.success) {
                        $('#sendInvoiceModal').modal('hide');
                        location.reload();
                    } else {
                        throw new Error(response.message || 'Error sending invoice');
                    }
                } catch (err) {
                    Toast.error('Send Failed', err.message);
                    $('#confirmSendBtn').prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> Send Invoice');
                }
            });

            // Pay Handler
            $('#confirmPayBtn').click(async function () {
                try {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                    const response = await API.fetchWithAuth(`admin/invoices/${invoiceId}/pay`, { method: 'POST' });

                    if (response.success) {
                        $('#markPaidModal').modal('hide');
                        location.reload();
                    } else {
                        throw new Error(response.message || 'Error marking paid');
                    }
                } catch (err) {
                    Toast.error('Payment Failed', err.message);
                    $('#confirmPayBtn').prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> Mark as Paid');
                }
            });

            // Delete Handler
            $(document).on('click', '#btnDelete', async function () {
                if (!confirm("Are you sure you want to delete this DRAFT invoice?")) return;

                try {
                    const response = await API.fetchWithAuth(`admin/invoices/${invoiceId}`, { method: 'DELETE' });
                    if (response.success) {
                        window.location.href = 'index.php';
                    }
                } catch (err) {
                    Toast.error('Delete Failed', err.message);
                }
            });

            // Download Handler
            window.downloadPdf = async function () {
                try {
                    const response = await API.fetchWithAuth(`admin/invoices/${invoiceId}/download`);

                    if (response.success && response.url) {
                        // Open the signed URL directly in a new tab - avoids CORS issues
                        window.open(response.url, '_blank');
                    } else {
                        throw new Error(response.message || "Failed to get download URL");
                    }
                } catch (err) {
                    Toast.error('Download Error', err.message);
                }
            };

            async function loadInvoice(id) {
                try {
                    const inv = await API.fetchWithAuth(`admin/invoices/${id}`);

                    // Header
                    $('#invoiceNumber').text(inv.invoice_number);
                    renderStatus(inv.status);

                    // Details
                    $('#advertiserName').text(inv.advertiser ? inv.advertiser.company : 'N/A');
                    $('#invoiceTitle').text(inv.title || 'N/A');
                    $('#invoiceDate').text(inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString() : 'N/A');
                    $('#dueDate').text(inv.due_date ? new Date(inv.due_date).toLocaleDateString() : 'N/A');
                    $('#totalAmount').text('$' + parseFloat(inv.total_amount).toFixed(2));

                    if (inv.paid_at) {
                        $('#paymentInfo').removeClass('d-none');
                        $('#paidAt').text(new Date(inv.paid_at).toLocaleDateString());
                    }

                    // Actions
                    renderActions(inv.status);

                    $('#pageLoading').hide();
                    $('#mainInvoiceContent').show();

                } catch (err) {
                    $('#pageLoading').html(`<div class="alert alert-danger">Error loading invoice: ${err.message}</div>`);
                }
            }

            function renderStatus(status) {
                const el = $('#statusBadge');
                el.text(status);
                el.removeClass('badge-secondary badge-success badge-warning');

                if (status === 'PAID') el.addClass('badge-success');
                else if (status === 'SENT') el.addClass('badge-warning');
                else el.addClass('badge-secondary');
            }

            function renderActions(status) {
                const container = $('#actionButtons');
                const help = $('#actionHelpText');
                container.empty();

                if (status === 'DRAFT') {
                    help.text('Invoice is in Draft mode. You can send or delete it.');

                    container.append(`
                        <button class="btn btn-warning btn-block mb-3" data-toggle="modal" data-target="#sendInvoiceModal">
                            <i class="fas fa-paper-plane mr-2"></i> Send Invoice
                        </button>
                    `);

                    container.append(`
                        <button class="btn btn-danger btn-block" id="btnDelete">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Invoice
                        </button>
                    `);
                }
                else if (status === 'SENT') {
                    help.text('Invoice has been sent. You can download PDF or mark as paid.');

                    container.append(`
                        <button class="btn btn-info btn-block mb-3" onclick="downloadPdf()">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    `);

                    container.append(`
                        <button class="btn btn-success btn-block" data-toggle="modal" data-target="#markPaidModal">
                            <i class="fas fa-check-circle mr-2"></i> Mark as Paid
                        </button>
                    `);
                }
                else if (status === 'PAID') {
                    help.text('Invoice is fully paid.');

                    container.append(`
                        <button class="btn btn-info btn-block" onclick="downloadPdf()">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    `);
                }
            }
        });
    </script>
</body>