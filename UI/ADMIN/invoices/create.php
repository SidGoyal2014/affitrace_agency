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
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Invoice Details</h6>
                        </div>
                        <div class="card-body">
                            <form id="createInvoiceForm">
                                <!-- Advertiser -->
                                <div class="form-group">
                                    <label for="advertiser_id"><strong class="text-danger">*</strong> Advertiser</label>
                                    <select class="form-control" id="advertiser_id" name="advertiser_id" required>
                                        <option value="">-- Loading Advertisers --</option>
                                    </select>
                                </div>

                                <!-- Title / Description -->
                                <div class="form-group">
                                    <label for="title">Title / Description</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="e.g. January 2025 Invoice">
                                </div>

                                <!-- Amount -->
                                <div class="form-group">
                                    <label for="total_amount"><strong class="text-danger">*</strong> Total Amount
                                        ($)</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="total_amount"
                                        name="total_amount" required placeholder="0.00">
                                </div>

                                <!-- Dates Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="invoice_date">Invoice Date</label>
                                            <input type="date" class="form-control" id="invoice_date"
                                                name="invoice_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="due_date">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="submitBtn">
                                    <i class="fas fa-plus-circle mr-2"></i> Create Invoice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Advertiser:</strong> <span id="previewAdvertiser">-</span></p>
                            <p><strong>Description:</strong> <span id="previewTitle">-</span></p>
                            <hr>
                            <h3 class="text-primary font-weight-bold">$<span id="previewAmount">0.00</span></h3>
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

            // Set default invoice date to today
            const today = new Date().toISOString().split('T')[0];
            $('#invoice_date').val(today);

            // Load Advertisers
            loadAdvertisers();

            // Live Preview
            $('#advertiser_id').change(function () {
                $('#previewAdvertiser').text($(this).find('option:selected').text() || '-');
            });
            $('#title').on('input', function () {
                $('#previewTitle').text($(this).val() || '-');
            });
            $('#total_amount').on('input', function () {
                const val = parseFloat($(this).val()) || 0;
                $('#previewAmount').text(val.toFixed(2));
            });

            // Form Submit
            $('#createInvoiceForm').submit(async function (e) {
                e.preventDefault();

                const payload = {
                    advertiser_id: $('#advertiser_id').val(),
                    title: $('#title').val(),
                    total_amount: parseFloat($('#total_amount').val()),
                    invoice_date: $('#invoice_date').val() || null,
                    due_date: $('#due_date').val() || null
                };

                if (!payload.advertiser_id || !payload.total_amount) {
                    alert('Please fill in required fields.');
                    return;
                }

                try {
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

                    const response = await API.fetchWithAuth('admin/invoices', {
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
                    alert("Error: " + error.message);
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-plus-circle mr-2"></i> Create Invoice');
                }
            });

            async function loadAdvertisers() {
                try {
                    const data = await API.fetchWithAuth('admin/advertisers/list');
                    const select = $('#advertiser_id');
                    select.empty().append('<option value="">-- Select Advertiser --</option>');

                    const list = data.data ? data.data : data;
                    if (Array.isArray(list)) {
                        list.forEach(adv => {
                            // uid is the user id from the API response
                            const userId = adv.uid || adv.id;
                            select.append(`<option value="${userId}">${adv.company || 'N/A'} (${adv.name || 'ID: ' + userId})</option>`);
                        });
                    }
                } catch (err) {
                    console.error("Load Advertisers Error:", err);
                    $('#advertiser_id').html('<option value="">Error loading advertisers</option>');
                }
            }
        });
    </script>
</body>