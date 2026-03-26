<?php
$pathPrefix = '..';
$activePage = 'invoices';
$pageTitle = 'Create Invoice';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create New Invoice</h1>
                <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                <!-- ====== LEFT COLUMN: Form ====== -->
                <div class="col-lg-8">
                    <form id="createInvoiceForm">

                        <!-- 1. Dates Row -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Invoice Dates</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="invoice_date">Invoice Date</label>
                                            <input type="date" class="form-control" id="invoice_date" name="invoice_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="due_date">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Invoice From & Invoice To (side by side) -->
                        <div class="row">
                            <!-- Invoice From -->
                            <div class="col-md-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Invoice From</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="from_company">Company Name</label>
                                            <input type="text" class="form-control" id="from_company" name="from_company" placeholder="Your Company">
                                        </div>
                                        <div class="form-group">
                                            <label for="from_address">Address</label>
                                            <textarea class="form-control" id="from_address" name="from_address" rows="2" placeholder="Street, City, Country"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="from_email">Email</label>
                                            <input type="email" class="form-control" id="from_email" name="from_email" placeholder="email@company.com">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="from_vat_id">VAT ID</label>
                                            <input type="text" class="form-control" id="from_vat_id" name="from_vat_id" placeholder="VAT123456">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice To -->
                            <div class="col-md-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Invoice To</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Tabs -->
                                        <ul class="nav nav-tabs mb-3" id="invoiceToTabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-affiliate" data-toggle="tab" href="#pane-affiliate" role="tab">Affiliates</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" id="tab-advertiser" data-toggle="tab" href="#pane-advertiser" role="tab">Advertisers</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-custom" data-toggle="tab" href="#pane-custom" role="tab">Custom</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="invoiceToTabContent">
                                            <!-- Affiliate Tab -->
                                            <div class="tab-pane fade" id="pane-affiliate" role="tabpanel">
                                                <div class="form-group">
                                                    <label>Affiliate Company</label>
                                                    <select class="form-control" id="to_affiliate_id">
                                                        <option value="">-- Select Affiliate --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Advertiser Tab -->
                                            <div class="tab-pane fade show active" id="pane-advertiser" role="tabpanel">
                                                <div class="form-group">
                                                    <label>Advertiser Company</label>
                                                    <select class="form-control" id="to_advertiser_id">
                                                        <option value="">-- Select Advertiser --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Custom Tab -->
                                            <div class="tab-pane fade" id="pane-custom" role="tabpanel">
                                                <div class="form-group">
                                                    <label>Company Name</label>
                                                    <input type="text" class="form-control" id="to_custom_company" placeholder="Enter company name">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="company_type" name="company_type" value="advertiser">
                                        <input type="hidden" id="company_name" name="company_name" value="">
                                        <input type="hidden" id="advertiser_id" name="advertiser_id" value="">

                                        <div class="form-group">
                                            <label for="to_address">Address</label>
                                            <textarea class="form-control" id="to_address" name="to_address" rows="2" placeholder="Street, City, Country"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="to_email">Email</label>
                                            <input type="email" class="form-control" id="to_email" name="to_email" placeholder="email@company.com">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="to_vat_id">VAT ID</label>
                                            <input type="text" class="form-control" id="to_vat_id" name="to_vat_id" placeholder="VAT123456">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Offer (optional) -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Offer <small class="text-muted">(Optional)</small></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <label for="offer_id">Select Offer</label>
                                    <select class="form-control" id="offer_id" name="offer_id">
                                        <option value="">-- None --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Line Items -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Line Items</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="addLineItem">
                                    <i class="fas fa-plus mr-1"></i> Add Item
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" id="lineItemsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:50%">Description</th>
                                                <th style="width:15%">Qty</th>
                                                <th style="width:15%">Price ($)</th>
                                                <th style="width:15%">Subtotal</th>
                                                <th style="width:5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="lineItemsBody">
                                            <!-- Dynamic rows -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Totals, Tax, Discount -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Totals</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <strong>$<span id="calcSubtotal">0.00</span></strong>
                                        </div>
                                        <div class="form-group mb-2">
                                            <div class="d-flex align-items-center">
                                                <label class="mb-0 mr-2" for="tax_percent" style="white-space:nowrap;">Tax (%):</label>
                                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" id="tax_percent" name="tax_percent" value="0" style="max-width:100px;">
                                                <span class="ml-2">= $<span id="calcTax">0.00</span></span>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <div class="d-flex align-items-center">
                                                <label class="mb-0 mr-2" for="discount_amount" style="white-space:nowrap;">Discount ($):</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="discount_amount" name="discount_amount" value="0" style="max-width:100px;">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <h5 class="font-weight-bold text-primary">Net Total:</h5>
                                            <h5 class="font-weight-bold text-primary">$<span id="calcNetTotal">0.00</span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Payment Method -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Payment Method</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_crypto" value="Crypto">
                                    <label class="form-check-label" for="pay_crypto"><i class="fab fa-bitcoin mr-1"></i>Crypto</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="Bank Transfer">
                                    <label class="form-check-label" for="pay_bank"><i class="fas fa-university mr-1"></i>Bank Transfer</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_paypal" value="PayPal">
                                    <label class="form-check-label" for="pay_paypal"><i class="fab fa-paypal mr-1"></i>PayPal</label>
                                </div>
                            </div>
                        </div>

                        <!-- 7. Notes & Terms -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Notes &amp; Terms</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="notes">Notes <small class="text-muted">(Optional)</small></label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="terms">Terms &amp; Conditions <small class="text-muted">(Optional)</small></label>
                                            <textarea class="form-control" id="terms" name="terms" rows="3" placeholder="Payment terms..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm mb-4" id="submitBtn">
                            <i class="fas fa-plus-circle mr-2"></i> Create Invoice
                        </button>

                    </form>
                </div>

                <!-- ====== RIGHT COLUMN: Preview ====== -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4 border-left-primary" style="position:sticky; top:80px;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-eye mr-2"></i>Live Preview</h6>
                        </div>
                        <div class="card-body" style="font-size:0.85rem;">
                            <!-- From -->
                            <div class="mb-3">
                                <div class="text-uppercase text-muted small font-weight-bold">From</div>
                                <strong id="prevFromCompany">-</strong>
                                <div class="text-muted small" id="prevFromAddress"></div>
                                <div class="text-muted small" id="prevFromEmail"></div>
                                <div class="text-muted small" id="prevFromVat"></div>
                            </div>
                            <!-- To -->
                            <div class="mb-3">
                                <div class="text-uppercase text-muted small font-weight-bold">To</div>
                                <strong id="prevToCompany">-</strong>
                                <div class="text-muted small" id="prevToAddress"></div>
                                <div class="text-muted small" id="prevToEmail"></div>
                                <div class="text-muted small" id="prevToVat"></div>
                            </div>
                            <hr>
                            <!-- Dates -->
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Invoice Date:</small>
                                <small id="prevInvDate">-</small>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-muted">Due Date:</small>
                                <small id="prevDueDate">-</small>
                            </div>
                            <!-- Line Items -->
                            <div class="text-uppercase text-muted small font-weight-bold mb-1">Items</div>
                            <table class="table table-sm table-borderless mb-2" style="font-size:0.8rem;">
                                <tbody id="prevLineItems">
                                    <tr><td class="text-muted text-center" colspan="3">No items yet</td></tr>
                                </tbody>
                            </table>
                            <hr>
                            <!-- Totals -->
                            <div class="d-flex justify-content-between mb-1">
                                <span>Subtotal:</span>
                                <span>$<span id="prevSubtotal">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tax:</span>
                                <span>$<span id="prevTax">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Discount:</span>
                                <span>-$<span id="prevDiscount">0.00</span></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5 class="font-weight-bold text-primary mb-0">Net Total:</h5>
                                <h5 class="font-weight-bold text-primary mb-0">$<span id="prevNetTotal">0.00</span></h5>
                            </div>
                            <!-- Payment -->
                            <div class="mt-3" id="prevPaymentWrap" style="display:none;">
                                <small class="text-muted">Payment:</small>
                                <span class="badge badge-info" id="prevPayment"></span>
                            </div>
                        </div>
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
            if (!PermissionGuard.check('invoices', 'can_create')) return;

            // Set default invoice date
            const today = new Date().toISOString().split('T')[0];
            $('#invoice_date').val(today);

            // Load dropdowns
            loadAdvertisers();
            loadAffiliates();
            loadOffers();

            // Add first line item row
            addLineItemRow();

            // ===== Tab switching =====
            $('#invoiceToTabs a').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#pane-affiliate') {
                    $('#company_type').val('affiliate');
                    updateToCompanyName();
                } else if (target === '#pane-advertiser') {
                    $('#company_type').val('advertiser');
                    updateToCompanyName();
                } else {
                    $('#company_type').val('custom');
                    updateToCompanyName();
                }
            });

            // Company name sync from dropdowns / input
            $('#to_advertiser_id').change(function () {
                const text = $(this).find('option:selected').text();
                const val = $(this).val();
                $('#company_name').val(val ? text : '');
                $('#advertiser_id').val(val);
                updatePreview();
            });
            $('#to_affiliate_id').change(function () {
                const text = $(this).find('option:selected').text();
                $('#company_name').val($(this).val() ? text : '');
                $('#advertiser_id').val('');
                updatePreview();
            });
            $('#to_custom_company').on('input', function () {
                $('#company_name').val($(this).val());
                $('#advertiser_id').val('');
                updatePreview();
            });

            function updateToCompanyName() {
                const type = $('#company_type').val();
                if (type === 'advertiser') {
                    const sel = $('#to_advertiser_id');
                    $('#company_name').val(sel.val() ? sel.find('option:selected').text() : '');
                    $('#advertiser_id').val(sel.val());
                } else if (type === 'affiliate') {
                    const sel = $('#to_affiliate_id');
                    $('#company_name').val(sel.val() ? sel.find('option:selected').text() : '');
                    $('#advertiser_id').val('');
                } else {
                    $('#company_name').val($('#to_custom_company').val());
                    $('#advertiser_id').val('');
                }
                updatePreview();
            }

            // ===== Line Items =====
            $('#addLineItem').click(function () {
                addLineItemRow();
            });

            function addLineItemRow() {
                const idx = $('#lineItemsBody tr').length;
                const row = `
                    <tr class="line-item-row">
                        <td><input type="text" class="form-control form-control-sm li-desc" placeholder="Item description"></td>
                        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm li-qty" value="1"></td>
                        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm li-price" value="0"></td>
                        <td class="text-right align-middle font-weight-bold li-subtotal">$0.00</td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-line-item" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#lineItemsBody').append(row);
            }

            // Calculate row subtotal on qty/price change
            $(document).on('input', '.li-qty, .li-price', function () {
                const row = $(this).closest('tr');
                const qty = parseFloat(row.find('.li-qty').val()) || 0;
                const price = parseFloat(row.find('.li-price').val()) || 0;
                row.find('.li-subtotal').text('$' + (qty * price).toFixed(2));
                recalcTotals();
            });

            // Remove line item
            $(document).on('click', '.remove-line-item', function () {
                $(this).closest('tr').remove();
                recalcTotals();
            });

            // Tax / Discount change
            $('#tax_percent, #discount_amount').on('input', function () {
                recalcTotals();
            });

            function recalcTotals() {
                let subtotal = 0;
                $('#lineItemsBody tr').each(function () {
                    const qty = parseFloat($(this).find('.li-qty').val()) || 0;
                    const price = parseFloat($(this).find('.li-price').val()) || 0;
                    subtotal += qty * price;
                });
                const taxPct = parseFloat($('#tax_percent').val()) || 0;
                const taxAmt = subtotal * (taxPct / 100);
                const discount = parseFloat($('#discount_amount').val()) || 0;
                const netTotal = subtotal + taxAmt - discount;

                $('#calcSubtotal').text(subtotal.toFixed(2));
                $('#calcTax').text(taxAmt.toFixed(2));
                $('#calcNetTotal').text(netTotal.toFixed(2));

                updatePreview();
            }

            // ===== Live Preview =====
            // From fields
            $('#from_company, #from_address, #from_email, #from_vat_id').on('input', updatePreview);
            // To fields
            $('#to_address, #to_email, #to_vat_id').on('input', updatePreview);
            // Dates
            $('#invoice_date, #due_date').on('change', updatePreview);
            // Line item description
            $(document).on('input', '.li-desc', updatePreview);
            // Payment method
            $('input[name="payment_method"]').on('change', updatePreview);

            function updatePreview() {
                // From
                $('#prevFromCompany').text($('#from_company').val() || '-');
                $('#prevFromAddress').text($('#from_address').val());
                $('#prevFromEmail').text($('#from_email').val());
                const fromVat = $('#from_vat_id').val();
                $('#prevFromVat').text(fromVat ? 'VAT: ' + fromVat : '');

                // To
                $('#prevToCompany').text($('#company_name').val() || '-');
                $('#prevToAddress').text($('#to_address').val());
                $('#prevToEmail').text($('#to_email').val());
                const toVat = $('#to_vat_id').val();
                $('#prevToVat').text(toVat ? 'VAT: ' + toVat : '');

                // Dates
                $('#prevInvDate').text($('#invoice_date').val() || '-');
                $('#prevDueDate').text($('#due_date').val() || '-');

                // Line items
                const tbody = $('#prevLineItems');
                tbody.empty();
                let hasItems = false;
                $('#lineItemsBody tr').each(function () {
                    const desc = $(this).find('.li-desc').val() || '';
                    const qty = parseFloat($(this).find('.li-qty').val()) || 0;
                    const price = parseFloat($(this).find('.li-price').val()) || 0;
                    if (desc || qty || price) {
                        hasItems = true;
                        tbody.append(`<tr>
                            <td>${desc}</td>
                            <td class="text-center">${qty}</td>
                            <td class="text-right">$${(qty * price).toFixed(2)}</td>
                        </tr>`);
                    }
                });
                if (!hasItems) {
                    tbody.append('<tr><td class="text-muted text-center" colspan="3">No items yet</td></tr>');
                }

                // Totals
                $('#prevSubtotal').text($('#calcSubtotal').text());
                $('#prevTax').text($('#calcTax').text());
                $('#prevDiscount').text((parseFloat($('#discount_amount').val()) || 0).toFixed(2));
                $('#prevNetTotal').text($('#calcNetTotal').text());

                // Payment
                const pm = $('input[name="payment_method"]:checked').val();
                if (pm) {
                    $('#prevPaymentWrap').show();
                    $('#prevPayment').text(pm);
                } else {
                    $('#prevPaymentWrap').hide();
                }
            }

            // ===== Form Submit =====
            $('#createInvoiceForm').submit(async function (e) {
                e.preventDefault();

                const companyName = $('#company_name').val();
                if (!companyName) {
                    Toast.warning('Required', 'Please select or enter a company in the "Invoice To" section.');
                    return;
                }

                // Gather line items
                const lineItems = [];
                $('#lineItemsBody tr').each(function () {
                    lineItems.push({
                        description: $(this).find('.li-desc').val() || '',
                        quantity: parseFloat($(this).find('.li-qty').val()) || 0,
                        price: parseFloat($(this).find('.li-price').val()) || 0,
                    });
                });

                const payload = {
                    // From
                    from_company: $('#from_company').val() || null,
                    from_address: $('#from_address').val() || null,
                    from_email: $('#from_email').val() || null,
                    from_vat_id: $('#from_vat_id').val() || null,
                    // To
                    company_type: $('#company_type').val(),
                    company_name: companyName,
                    advertiser_id: $('#advertiser_id').val() || null,
                    to_address: $('#to_address').val() || null,
                    to_email: $('#to_email').val() || null,
                    to_vat_id: $('#to_vat_id').val() || null,
                    // Offer
                    offer_id: $('#offer_id').val() || null,
                    // Dates
                    invoice_date: $('#invoice_date').val() || null,
                    due_date: $('#due_date').val() || null,
                    // Line items
                    line_items: lineItems,
                    // Tax/discount
                    tax_percent: parseFloat($('#tax_percent').val()) || 0,
                    discount_amount: parseFloat($('#discount_amount').val()) || 0,
                    // Payment
                    payment_method: $('input[name="payment_method"]:checked').val() || null,
                    // Notes
                    notes: $('#notes').val() || null,
                    terms: $('#terms').val() || null,
                };

                try {
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

                    const response = await API.fetchWithAuth('employee/invoices', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (response.success) {
                        window.location.href = `detail.php?id=${response.invoice_id}`;
                    } else {
                        throw new Error(response.message || "Failed to create invoice");
                    }
                } catch (error) {
                    console.error(error);
                    Toast.error('Error', error.message);
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-plus-circle mr-2"></i> Create Invoice');
                }
            });

            // ===== Load Dropdowns =====
            async function loadAdvertisers() {
                try {
                    const data = await API.fetchWithAuth('employee/advertisers/list');
                    const select = $('#to_advertiser_id');
                    select.empty().append('<option value="">-- Select Advertiser --</option>');
                    const list = data.data ? data.data : data;
                    if (Array.isArray(list)) {
                        list.forEach(adv => {
                            const userId = adv.uid || adv.id;
                            select.append(`<option value="${userId}">${adv.company || 'N/A'}</option>`);
                        });
                    }
                } catch (err) {
                    console.error("Load Advertisers Error:", err);
                }
            }

            async function loadAffiliates() {
                try {
                    const data = await API.fetchWithAuth('employee/affiliates/list');
                    const select = $('#to_affiliate_id');
                    select.empty().append('<option value="">-- Select Affiliate --</option>');
                    const list = data.data ? data.data : data;
                    if (Array.isArray(list)) {
                        list.forEach(aff => {
                            const userId = aff.uid || aff.id;
                            select.append(`<option value="${userId}">${aff.company || 'N/A'}</option>`);
                        });
                    }
                } catch (err) {
                    console.error("Load Affiliates Error:", err);
                }
            }

            async function loadOffers() {
                try {
                    const data = await API.fetchWithAuth('employee/offer/approved');
                    const select = $('#offer_id');
                    const list = data.data ? data.data : data;
                    if (Array.isArray(list)) {
                        list.forEach(offer => {
                            select.append(`<option value="${offer.id}">${offer.title || offer.name || 'Offer #' + offer.id}</option>`);
                        });
                    }
                } catch (err) {
                    console.error("Load Offers Error:", err);
                }
            }
        });
    </script>
</body>