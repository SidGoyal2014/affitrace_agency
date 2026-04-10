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
                <a href="#" class="btn btn-sm btn-info shadow-sm d-none" id="editLink">
                    <i class="fas fa-edit mr-1"></i> Edit Invoice
                </a>
            </div>

            <div class="row" id="mainInvoiceContent" style="display:none;">
                <!-- Left Column: Details -->
                <div class="col-xl-8 col-md-7 mb-4">

                    <!-- From / To -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Invoice From</h6>
                                </div>
                                <div class="card-body">
                                    <h5 id="detailFromCompany" class="font-weight-bold">-</h5>
                                    <p class="mb-1 text-muted" id="detailFromAddress"></p>
                                    <p class="mb-1" id="detailFromEmail"></p>
                                    <p class="mb-0 text-muted small" id="detailFromVat"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Invoice To</h6>
                                </div>
                                <div class="card-body">
                                    <h5 id="detailToCompany" class="font-weight-bold">-</h5>
                                    <span class="badge badge-light mb-2" id="detailCompanyType"></span>
                                    <p class="mb-1 text-muted" id="detailToAddress"></p>
                                    <p class="mb-1" id="detailToEmail"></p>
                                    <p class="mb-0 text-muted small" id="detailToVat"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates & Offer -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Invoice Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Invoice Date</small>
                                    <strong id="invoiceDate">-</strong>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Due Date</small>
                                    <strong id="dueDate">-</strong>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Offer</small>
                                    <strong id="detailOffer">-</strong>
                                </div>
                            </div>
                            <div id="paymentInfo" class="mt-3 d-none">
                                <div class="alert alert-success text-center px-2 py-1 mb-0">
                                    <small><i class="fas fa-check-circle"></i> Paid on <span id="paidAt"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Line Items</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-center" style="width:100px;">Qty</th>
                                            <th class="text-right" style="width:120px;">Price</th>
                                            <th class="text-right" style="width:120px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailLineItems">
                                        <tr><td class="text-center text-muted" colspan="4">Loading...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Subtotal:</span>
                                        <strong id="detailSubtotal">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Tax (<span id="detailTaxPct">0</span>%):</span>
                                        <strong id="detailTaxAmt">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Discount:</span>
                                        <strong id="detailDiscount">-$0.00</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <h4 class="font-weight-bold text-primary">Net Total:</h4>
                                        <h4 class="font-weight-bold text-primary" id="detailNetTotal">$0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card shadow mb-4" id="paymentMethodCard" style="display:none;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Method</h6>
                        </div>
                        <div class="card-body">
                            <span class="badge badge-info" style="font-size:1rem;" id="detailPaymentMethod"></span>
                        </div>
                    </div>

                    <!-- Notes & Terms -->
                    <div class="row">
                        <div class="col-md-6" id="notesCard" style="display:none;">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" id="detailNotes"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="termsCard" style="display:none;">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Terms &amp; Conditions</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" id="detailTerms"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="col-xl-4 col-md-5 mb-4">
                    <div class="card shadow mb-4" style="position:sticky; top:80px;">
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



    <!-- Preview PDF Modal -->
    <div class="modal fade" id="previewPdfModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-secondary"><i class="fas fa-file-pdf mr-2"></i>Invoice Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div id="previewLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading preview...</p>
                    </div>
                    <iframe id="pdfPreviewFrame" style="width:100%; height:75vh; border:none; display:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Via Email Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-left-primary">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Send Invoice Via Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This will email the invoice PDF to:</p>
                    <div class="alert alert-light border text-center">
                        <i class="fas fa-envelope mr-2 text-primary"></i>
                        <strong id="emailRecipient">Loading...</strong>
                    </div>
                    <p class="small text-muted mb-0">The invoice PDF will be sent as an attachment.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmEmailBtn">
                        <i class="fas fa-envelope mr-2"></i> Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Save as Draft Modal -->
    <div class="modal fade" id="revertDraftModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-left-secondary">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-secondary">Revert to Draft</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This will revert the invoice back to <span class="badge badge-secondary">DRAFT</span> status.</p>
                    <ul class="mb-3">
                        <li>The invoice will be unlocked for editing</li>
                        <li>The generated PDF will be preserved</li>
                    </ul>
                    <p class="font-weight-bold mb-0">Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-secondary" id="confirmRevertBtn">
                        <i class="fas fa-undo mr-2"></i> Save as Draft
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
            if (!PermissionGuard.check('invoices', 'can_view')) return;
            const canEditInvoices = PermissionGuard.has('invoices', 'can_edit');

            const urlParams = new URLSearchParams(window.location.search);
            const invoiceId = urlParams.get('id');

            if (!invoiceId) {
                Toast.error('Error', 'Invalid Invoice ID');
                window.location.href = 'index.php';
                return;
            }

            let recipientEmail = '';
            let currentNetTotal = 0;

            loadInvoice(invoiceId);

            // Send Handler
            $('#confirmSendBtn').click(async function () {
                if (!canEditInvoices) {
                    Toast.error('Access Denied', 'You do not have permission to send invoices.');
                    return;
                }
                try {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}/send`, { method: 'POST' });

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

            // Pay Handler (delegated — #btnMarkPaid rendered dynamically by renderActions)
            $(document).on('click', '#btnMarkPaid', function () {
                if (!canEditInvoices) {
                    Toast.error('Access Denied', 'You do not have permission to mark invoices as paid.');
                    return;
                }
                MarkPaidPlugin.open({
                    endpoint: `employee/invoices/${invoiceId}/pay`,
                    amount: currentNetTotal,
                    paymentFrom: 'Advertiser',
                    paymentTo: 'Agency',
                    onSuccess: function () { location.reload(); }
                });
            });

            // Delete Handler
            $(document).on('click', '#btnDelete', async function () {
                if (!confirm("Are you sure you want to delete this DRAFT invoice?")) return;

                try {
                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}`, { method: 'DELETE' });
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
                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}/download`);

                    if (response.success && response.url) {
                        window.open(response.url, '_blank');
                    } else {
                        throw new Error(response.message || "Failed to get download URL");
                    }
                } catch (err) {
                    Toast.error('Download Error', err.message);
                }
            };

            // Preview PDF Handler
            window.previewPdf = async function () {
                try {
                    $('#previewLoading').show();
                    $('#pdfPreviewFrame').hide();
                    $('#previewPdfModal').modal('show');

                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}/download`);

                    if (response.success && response.url) {
                        $('#pdfPreviewFrame').attr('src', response.url);
                        $('#pdfPreviewFrame').on('load', function () {
                            $('#previewLoading').hide();
                            $(this).show();
                        });
                    } else {
                        throw new Error(response.message || "Failed to get preview URL");
                    }
                } catch (err) {
                    Toast.error('Preview Error', err.message);
                    $('#previewPdfModal').modal('hide');
                }
            };

            // Clear iframe on modal close
            $('#previewPdfModal').on('hidden.bs.modal', function () {
                $('#pdfPreviewFrame').attr('src', '').hide();
                $('#previewLoading').show();
            });

            // Send Via Email Handler
            $('#confirmEmailBtn').click(async function () {
                try {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}/email`, { method: 'POST' });

                    if (response.success) {
                        $('#sendEmailModal').modal('hide');
                        Toast.success('Email Sent', response.message || 'Invoice emailed successfully.');
                        btn.prop('disabled', false).html('<i class="fas fa-envelope mr-2"></i> Send Email');
                    } else {
                        throw new Error(response.message || 'Error sending email');
                    }
                } catch (err) {
                    Toast.error('Email Failed', err.message);
                    $('#confirmEmailBtn').prop('disabled', false).html('<i class="fas fa-envelope mr-2"></i> Send Email');
                }
            });

            // Revert to Draft Handler
            $('#confirmRevertBtn').click(async function () {
                if (!canEditInvoices) {
                    Toast.error('Access Denied', 'You do not have permission to modify invoices.');
                    return;
                }
                try {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                    const response = await API.fetchWithAuth(`employee/invoices/${invoiceId}/revert-draft`, { method: 'POST' });

                    if (response.success) {
                        $('#revertDraftModal').modal('hide');
                        location.reload();
                    } else {
                        throw new Error(response.message || 'Error reverting to draft');
                    }
                } catch (err) {
                    Toast.error('Revert Failed', err.message);
                    $('#confirmRevertBtn').prop('disabled', false).html('<i class="fas fa-undo mr-2"></i> Save as Draft');
                }
            });

            async function loadInvoice(id) {
                try {
                    const inv = await API.fetchWithAuth(`employee/invoices/${id}`);

                    // Header
                    $('#invoiceNumber').text(inv.invoice_number);
                    renderStatus(inv.status);

                    // Edit link for drafts
                    if (inv.status === 'DRAFT') {
                        $('#editLink').attr('href', `edit.php?id=${id}`).removeClass('d-none');
                    }

                    // From
                    $('#detailFromCompany').text(inv.from_company || 'N/A');
                    $('#detailFromAddress').text(inv.from_address || '');
                    if (inv.from_email) $('#detailFromEmail').html('<i class="fas fa-envelope mr-1 small"></i>' + inv.from_email);
                    if (inv.from_vat_id) $('#detailFromVat').text('VAT: ' + inv.from_vat_id);

                    // To
                    const toCompany = inv.company_name || (inv.advertiser ? inv.advertiser.company : 'N/A');
                    $('#detailToCompany').text(toCompany);
                    if (inv.company_type) {
                        const typeLabel = inv.company_type.charAt(0).toUpperCase() + inv.company_type.slice(1);
                        $('#detailCompanyType').text(typeLabel);
                    }
                    $('#detailToAddress').text(inv.to_address || '');
                    if (inv.to_email) $('#detailToEmail').html('<i class="fas fa-envelope mr-1 small"></i>' + inv.to_email);
                    if (inv.to_vat_id) $('#detailToVat').text('VAT: ' + inv.to_vat_id);

                    // Dates
                    $('#invoiceDate').text(inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString() : 'N/A');
                    $('#dueDate').text(inv.due_date ? new Date(inv.due_date).toLocaleDateString() : 'N/A');

                    // Offer
                    $('#detailOffer').text(inv.offer_id ? 'Offer #' + inv.offer_id : 'None');

                    if (inv.paid_at) {
                        $('#paymentInfo').removeClass('d-none');
                        $('#paidAt').text(new Date(inv.paid_at).toLocaleDateString());
                    }

                    // Line Items
                    const lineBody = $('#detailLineItems');
                    lineBody.empty();
                    if (inv.line_items && inv.line_items.length > 0) {
                        inv.line_items.forEach(item => {
                            lineBody.append(`<tr>
                                <td>${item.description || ''}</td>
                                <td class="text-center">${parseFloat(item.quantity).toFixed(2)}</td>
                                <td class="text-right">$${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-right">$${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>`);
                        });
                    } else {
                        lineBody.append('<tr><td class="text-center text-muted" colspan="4">No line items</td></tr>');
                    }

                    // Totals
                    $('#detailSubtotal').text('$' + parseFloat(inv.subtotal_amount || 0).toFixed(2));
                    $('#detailTaxPct').text(parseFloat(inv.tax_percent || 0).toFixed(2));
                    $('#detailTaxAmt').text('$' + parseFloat(inv.tax_amount || 0).toFixed(2));
                    $('#detailDiscount').text('-$' + parseFloat(inv.discount_amount || 0).toFixed(2));
                    const netTotal = parseFloat(inv.net_total || inv.total_amount || 0).toFixed(2);
                    $('#detailNetTotal').text('$' + netTotal);
                    currentNetTotal = netTotal;

                    // Payment Method
                    if (inv.payment_method) {
                        $('#detailPaymentMethod').text(inv.payment_method);
                        $('#paymentMethodCard').show();
                    }

                    // Notes & Terms
                    if (inv.notes) {
                        $('#detailNotes').text(inv.notes);
                        $('#notesCard').show();
                    }
                    if (inv.terms) {
                        $('#detailTerms').text(inv.terms);
                        $('#termsCard').show();
                    }

                    // Recipient email for email modal
                    recipientEmail = inv.to_email || (inv.advertiser ? inv.advertiser.email : '');
                    $('#emailRecipient').text(recipientEmail || 'No email on file');

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

                const viewOnlyHelp = 'You have view-only access. Editable actions are hidden.';

                if (status === 'DRAFT') {
                    if (canEditInvoices) {
                        help.text('Invoice is in Draft mode. You can send or delete it.');

                        container.append(`
                            <button class="btn btn-warning btn-block mb-3" data-toggle="modal" data-target="#sendInvoiceModal">
                                <i class="fas fa-paper-plane mr-2"></i> Send Invoice
                            </button>
                        `);

                        container.append(`
                            <a href="edit.php?id=${invoiceId}" class="btn btn-info btn-block mb-3">
                                <i class="fas fa-edit mr-2"></i> Edit Invoice
                            </a>
                        `);

                        container.append(`
                            <button class="btn btn-danger btn-block" id="btnDelete">
                                <i class="fas fa-trash-alt mr-2"></i> Delete Invoice
                            </button>
                        `);
                    } else {
                        help.text(`Invoice is in Draft mode. ${viewOnlyHelp}`);
                    }
                }
                else if (status === 'SENT') {
                    if (canEditInvoices) {
                        help.text('Invoice has been sent. You can preview, download, email, revert to draft, or mark as paid.');
                    } else {
                        help.text(`Invoice has been sent. ${viewOnlyHelp}`);
                    }

                    container.append(`
                        <button class="btn btn-secondary btn-block mb-3" onclick="previewPdf()">
                            <i class="fas fa-eye mr-2"></i> Preview PDF
                        </button>
                    `);

                    container.append(`
                        <button class="btn btn-info btn-block mb-3" onclick="downloadPdf()">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    `);

                    if (canEditInvoices) {
                        container.append(`
                            <button class="btn btn-primary btn-block mb-3" data-toggle="modal" data-target="#sendEmailModal">
                                <i class="fas fa-envelope mr-2"></i> Send Via Email
                            </button>
                        `);

                        container.append(`
                            <button class="btn btn-outline-secondary btn-block mb-3" data-toggle="modal" data-target="#revertDraftModal">
                                <i class="fas fa-undo mr-2"></i> Save as Draft
                            </button>
                        `);

                        container.append(`
                            <button class="btn btn-success btn-block" id="btnMarkPaid">
                                <i class="fas fa-check-circle mr-2"></i> Mark as Paid
                            </button>
                        `);
                    }
                }
                else if (status === 'PAID') {
                    help.text('Invoice is fully paid. You can preview, download, or email the invoice.');

                    container.append(`
                        <button class="btn btn-secondary btn-block mb-3" onclick="previewPdf()">
                            <i class="fas fa-eye mr-2"></i> Preview PDF
                        </button>
                    `);

                    container.append(`
                        <button class="btn btn-info btn-block mb-3" onclick="downloadPdf()">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    `);

                    container.append(`
                        <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#sendEmailModal">
                            <i class="fas fa-envelope mr-2"></i> Send Via Email
                        </button>
                    `);
                }
            }
        });
    </script>
</body>