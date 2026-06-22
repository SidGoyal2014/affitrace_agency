<?php
$rolePrefix = isset($api_link_to_edit_advertiser) ? explode('/', $api_link_to_edit_advertiser)[0] : 'admin';
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/timezones.js"></script>
<script src="../../common/js/currencies.js"></script>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="page-header mb-4">
                <h1>Edit Advertiser Profile</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>

            <div class="card card-modern">
                <!-- Tab Headers -->
                <div class="card-header p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="advertiserTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                                <i class="fas fa-user-edit mr-2 text-primary"></i>Edit Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                                <i class="fas fa-info-circle mr-2 text-success"></i>Overview & Notes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="offers-tab" data-toggle="tab" href="#offers" role="tab">
                                <i class="fas fa-tags mr-2 text-warning"></i>Offers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="stats-tab" data-toggle="tab" href="#stats" role="tab">
                                <i class="fas fa-chart-line mr-2 text-info"></i>Spend & Conversion Stats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="billing-tab" data-toggle="tab" href="#billing" role="tab">
                                <i class="fas fa-file-invoice-dollar mr-2 text-secondary"></i>Billing & Invoices
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div id="alert-container"></div>
                    <!-- Single Form Wrapping Config Tabs -->
                    <form class="user" id="advertiserForm" method="post" action="#">
                        <input type="hidden" name="user_type" value="advertiser">
                        
                        <div class="tab-content" id="advertiserTabsContent">
                            
                            <!-- Tab 1: Edit Profile -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Basic Information</h4>
                                
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label><strong>Name:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label><strong>Email:</strong> <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label><strong>Phone:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone No" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label><strong>Assigned Manager:</strong></label>
                                        <select name="employee_id" class="form-control" id="employee_id">
                                            <option value="">(No Manager)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label><strong>Password:</strong> <small class="text-muted">(Leave blank to keep current)</small></label>
                                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Enter Password">
                                    </div>
                                    <div class="col-lg-6">
                                        <label><strong>Confirm Password:</strong></label>
                                        <input type="password" name="password_confirmation" class="form-control" id="inputConfirmPassword" placeholder="Confirm Password">
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Company & Contact</h4>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label><strong>Company Name:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" name="company" id="company" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Instant Messenger:</strong></label>
                                        <input type="text" name="instant_messenger_name" id="instant_messenger_name" class="form-control" placeholder="Skype, Telegram">
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>IM Username/Value:</strong></label>
                                        <input type="text" name="instant_messenger_value" id="instant_messenger_value" class="form-control">
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Address & Region</h4>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Address:</strong></label>
                                        <input type="text" name="address" id="address" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>City:</strong></label>
                                        <input type="text" name="city" id="city" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label><strong>State:</strong></label>
                                        <input type="text" name="state" id="state" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label><strong>Zip Code:</strong></label>
                                        <input type="text" name="zipcode" id="zipcode" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label><strong>Country:</strong></label>
                                        <select name="country" id="country" class="form-control select2-country">
                                            <option value="">(Select Country)</option>
                                        </select>
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Billing & Financials</h4>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Currency:</strong></label>
                                        <select name="currency" id="currency" class="form-control">
                                            <option value="">Select Currency</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>Time Zone:</strong></label>
                                        <select name="time_zone" id="time_zone" class="form-control">
                                            <option value="">Select Timezone</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Profile Overview -->
                            <div class="tab-pane fade" id="overview" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Profile Overview & Extra Details</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><strong>Tags:</strong></label>
                                            <input type="text" name="tags" id="tags" class="form-control" placeholder="comma-separated tags">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Postback IP Whitelist:</strong></label>
                                            <input type="text" name="postback_ip_whitelist" id="postback_ip_whitelist" class="form-control" placeholder="comma-separated IPs">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Tax Details:</strong></label>
                                            <input type="text" name="tax_details" id="tax_details" class="form-control" placeholder="GST, VAT, etc.">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Status:</strong></label>
                                            <div class="d-flex align-items-center mt-2">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="statusActiveTab" name="status" class="custom-control-input" value="active">
                                                    <label class="custom-control-label font-weight-bold text-success" for="statusActiveTab">Active</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="statusPendingTab" name="status" class="custom-control-input" value="pending">
                                                    <label class="custom-control-label font-weight-bold text-warning" for="statusPendingTab">Pending</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="statusRejectedTab" name="status" class="custom-control-input" value="rejected">
                                                    <label class="custom-control-label font-weight-bold text-danger" for="statusRejectedTab">Blocked</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="statusSuspendedTab" name="status" class="custom-control-input" value="suspended">
                                                    <label class="custom-control-label font-weight-bold text-secondary" for="statusSuspendedTab">Suspended</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Status Reason:</strong></label>
                                            <input type="text" name="status_reason" class="form-control" readonly id="overview_status_reason" placeholder="No reason specified">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><strong>Affiliate Block list:</strong></label>
                                            <input type="text" name="affiliate_block" id="affiliate_block" class="form-control" placeholder="comma-separated affiliate IDs">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Affiliate Whitelist:</strong></label>
                                            <input type="text" name="affiliate_whitelist" id="affiliate_whitelist" class="form-control" placeholder="comma-separated affiliate IDs">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Additional Info:</strong></label>
                                            <textarea name="additional_info" id="additional_info" class="form-control" rows="4" placeholder="Additional advertiser info..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="form-group">
                                            <label><strong>Private Note (Internal Admin Use Only):</strong></label>
                                            <textarea name="private_note" id="private_note" class="form-control" rows="4" placeholder="Add comments/notes about this advertiser..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Offers -->
                            <div class="tab-pane fade" id="offers" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Advertiser's Offers</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="assignedOffersTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Offer Name</th>
                                                <th>Ad Model (Pricing)</th>
                                                <th>Aff Model (Pricing)</th>
                                                <th>Visibility</th>
                                                <th>Status</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignedOffersBody">
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No offers found for this advertiser</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 4: Performance Stats -->
                            <div class="tab-pane fade" id="stats" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Advertiser Performance Stats</h4>
                                <div class="row" id="statsGrid">
                                    <div class="col-md-3 mb-4">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clicks</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_clicks">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <div class="card border-left-success shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved Conversions</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_conv_approved">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <div class="card border-left-warning shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Conversions</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_conv_pending">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <div class="card border-left-danger shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected Conversions</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_conv_rejected">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-4 mb-4">
                                        <div class="card border-left-info shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Spend (Rev)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_spend">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card border-left-success shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Payout</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_payout">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Margin (Profit)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_profit">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-left-secondary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Conversion Rate (CR%)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_cr">0.00%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-left-dark shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Earnings Per Click (EPC/CPC)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_epc">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 5: Invoices -->
                            <div class="tab-pane fade" id="billing" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Billing & Invoices</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="invoicesTable">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Title</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoicesBody">
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No invoices found for this advertiser</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- Update Button Row -->
                        <div class="row mt-4 card-footer bg-white border-top-0 d-flex justify-content-end" id="btnRow">
                            <button type="submit" id="submitBtn" class="btn btn-primary px-5 py-2 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        const rolePrefix = '<?php echo $rolePrefix; ?>';
        let advertiserId = null;

        $(document).ready(async function () {
            // Check auth
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            // Navbar User Info
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('#userDropdown span').text(user.name);
            }

            // Get ID
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            advertiserId = id;

            if (!id) {
                alert('No ID provided');
                window.location.href = 'list.php';
                return;
            }

            // 1. Populate standard static options
            const countrySelect = $('select[name="country"]');
            Object.entries(COUNTRIES).forEach(([code, name]) => {
                countrySelect.append(new Option(name, code));
            });

            const timezoneSelect = $('select[name="time_zone"]');
            Object.entries(TIMEZONE_LIST).forEach(([code, name]) => {
                timezoneSelect.append(new Option(name, code));
            });

            const currencySelect = $('select[name="currency"]');
            Object.entries(CURRENCIES).forEach(([code, name]) => {
                currencySelect.append(new Option(name, code));
            });

            // Load managers list dynamically
            try {
                const empResponse = await API.fetchWithAuth(`${rolePrefix}/employees/list`);
                const managers = empResponse.data;
                const employeeSelect = $('#employee_id');
                managers.forEach(m => {
                    employeeSelect.append(new Option(m.name, m.id));
                });
            } catch (err) {
                console.error("Failed to load managers", err);
            }

            // 2. Fetch Advertiser Details
            try {
                const response = await API.fetchWithAuth(`<?php echo $api_link_to_edit_advertiser; ?>/${id}`);
                const data = response.user;
                const dataAdud = response.adud;

                if (data) {
                    // Form fields basic binding
                    $('input[name="name"]').val(data.name || "");
                    $('input[name="phone"]').val(data.phone || "");                    
                    $('input[name="email"]').val(data.email || "");
                    $('input[name="company"]').val(data.company || "");
                    $('input[name="instant_messenger_name"]').val(data.instant_messenger_name || "");
                    $('input[name="instant_messenger_value"]').val(data.instant_messenger_value || "");
                    $('input[name="address"]').val(data.address || "");
                    $('input[name="city"]').val(data.city || "");
                    $('input[name="state"]').val(data.state || "");
                    countrySelect.val(data.country || "");
                    $('input[name="zipcode"]').val(data.zipcode || "");
                    $('#employee_id').val(data.employee_id || "");

                    // Form fields AUD binding
                    timezoneSelect.val(dataAdud.time_zone || "");
                    currencySelect.val(dataAdud.currency || "");
                    $('input[name="postback_ip_whitelist"]').val(dataAdud.postback_ip_whitelist || "");
                    $('input[name="tax_details"]').val(dataAdud.tax_details || "");
                    $('input[name="affiliate_block"]').val(dataAdud.affiliate_block || "");
                    $('input[name="affiliate_whitelist"]').val(dataAdud.affiliate_whitelist || "");
                    $('textarea[name="additional_info"]').val(dataAdud.additional_info || "");
                    $('textarea[name="private_note"]').val(dataAdud.private_note || "");
                    $('input[name="tags"]').val(dataAdud.tags || "");
                    $('#overview_status_reason').val(dataAdud.status_reason || "No reason specified");

                    // Set Status in Overview Radio
                    if (data.status) {
                        const statusVal = (typeof data.status === 'object') ? data.status.value : data.status;
                        $(`input[name="status"][value="${statusVal}"]`).prop('checked', true);
                    }
                }
            } catch (error) {
                console.error('Load Error:', error);
                $('#alert-container').html('<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> Failed to load advertiser data.</div>');
            }

            // Tab change lazy loading
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr("href");
                if (target === "#offers") {
                    loadOffers();
                    $('#btnRow').hide();
                } else if (target === "#stats") {
                    loadStats();
                    $('#btnRow').hide();
                } else if (target === "#billing") {
                    loadInvoices();
                    $('#btnRow').hide();
                } else {
                    $('#btnRow').show();
                }
            });

            // Handle Submit
            $('#advertiserForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();

                const password = $('#inputPassword').val();
                const confirm = $('#inputConfirmPassword').val();

                if (password && password !== confirm) {
                    $('#alert-container').html('<div class="alert alert-danger alert-modern">Passwords do not match.</div>');
                    return;
                }

                const btn = $('#submitBtn');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

                const payload = {
                    uid: advertiserId,
                    name: $('input[name="name"]').val(),
                    email: $('input[name="email"]').val(),
                    phone: $('input[name="phone"]').val(),
                    employee_id: $('#employee_id').val(),
                    company: $('input[name="company"]').val(),
                    instant_messenger_name: $('input[name="instant_messenger_name"]').val(),
                    instant_messenger_value: $('input[name="instant_messenger_value"]').val(),
                    address: $('input[name="address"]').val(),
                    city: $('input[name="city"]').val(),
                    state: $('input[name="state"]').val(),
                    zipcode: $('input[name="zipcode"]').val(),
                    country: countrySelect.val(),
                    currency: currencySelect.val(),
                    time_zone: timezoneSelect.val(),
                    postback_ip_whitelist: $('input[name="postback_ip_whitelist"]').val(),
                    tax_details: $('input[name="tax_details"]').val(),
                    status: $('input[name="status"]:checked').val(),
                    tags: $('input[name="tags"]').val(),
                    affiliate_block: $('input[name="affiliate_block"]').val(),
                    affiliate_whitelist: $('input[name="affiliate_whitelist"]').val(),
                    additional_info: $('textarea[name="additional_info"]').val(),
                    private_note: $('textarea[name="private_note"]').val()
                };

                if (password) {
                    payload.password = password;
                    payload.password_confirmation = confirm;
                }

                try {
                    const response = await API.fetchWithAuth(`<?php echo $api_link_to_update_advertiser; ?>`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success alert-modern"><i class="fas fa-check-circle mr-2"></i> Advertiser updated successfully! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        let errorMsg = response.error;
                        if (typeof response.error === 'object') {
                            errorMsg = Object.values(response.error).flat().join('<br>');
                        }
                        throw new Error(errorMsg || 'Failed to update advertiser.');
                    }
                } catch (e) {
                    $('#alert-container').html(`<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> ${e.message}</div>`);
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        async function loadOffers() {
            const body = $('#assignedOffersBody');
            body.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Loading offers...</td></tr>');
            
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/advertisers/${advertiserId}/offers`);
                body.empty();
                
                if (response.success && response.offers.length > 0) {
                    response.offers.forEach(o => {
                        body.append(`
                            <tr>
                                <td>${o.id}</td>
                                <td><strong>${o.offer_name}</strong></td>
                                <td>${o.ad_model.toUpperCase()} (${o.ad_pricing})</td>
                                <td>${o.aff_model.toUpperCase()} (${o.aff_pricing})</td>
                                <td><span class="badge badge-light border text-secondary px-2 py-1">${o.offer_visibility}</span></td>
                                <td><span class="badge badge-${o.offer_status === 'active' ? 'success' : 'secondary'} px-2 py-1">${o.offer_status}</span></td>
                                <td>
                                    <a href="../offers/edit.php?id=${o.id}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    body.html('<tr><td colspan="7" class="text-center py-4 text-muted">No offers found for this advertiser</td></tr>');
                }
            } catch (e) {
                console.error(e);
                body.html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load offers.</td></tr>');
            }
        }

        async function loadStats() {
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/advertisers/${advertiserId}/stats`);
                if (response.success && response.stats) {
                    const s = response.stats;
                    $('#stat_clicks').text(s.total_clicks);
                    $('#stat_conv_approved').text(s.approved_conversions);
                    $('#stat_conv_pending').text(s.pending_conversions);
                    $('#stat_conv_rejected').text(s.rejected_conversions);
                    $('#stat_spend').text('$' + parseFloat(s.total_spend || 0).toFixed(2));
                    $('#stat_payout').text('$' + parseFloat(s.total_payout || 0).toFixed(2));
                    $('#stat_profit').text('$' + parseFloat(s.total_profit || 0).toFixed(2));
                    $('#stat_cr').text(s.cr.toFixed(2) + '%');
                    $('#stat_epc').text('$' + parseFloat(s.epc || 0).toFixed(2));
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function loadInvoices() {
            const body = $('#invoicesBody');
            body.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Loading invoices...</td></tr>');
            
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/advertisers/${advertiserId}/invoices`);
                body.empty();
                
                if (response.success && response.invoices.length > 0) {
                    response.invoices.forEach(i => {
                        let color = '';
                        if (i.status === 'PAID') color = 'success';
                        if (i.status === 'SENT') color = 'warning';
                        if (i.status === 'DRAFT') color = 'secondary';
                        
                        body.append(`
                            <tr>
                                <td><strong>${i.invoice_number}</strong></td>
                                <td>${i.title}</td>
                                <td>${i.invoice_date ? new Date(i.invoice_date).toLocaleDateString() : '-'}</td>
                                <td>${i.due_date ? new Date(i.due_date).toLocaleDateString() : '-'}</td>
                                <td><strong>$${parseFloat(i.total_amount).toFixed(2)}</strong></td>
                                <td><span class="badge badge-${color} px-2 py-1">${i.status}</span></td>
                                <td>
                                    <a href="../invoices/detail.php?id=${i.id}" class="btn btn-sm btn-outline-primary mr-1" title="View"><i class="fas fa-eye"></i></a>
                                    ${(i.status === 'SENT' || i.status === 'PAID') ? `
                                        <button type="button" onclick="downloadPdf(${i.id})" class="btn btn-sm btn-outline-success" title="Download PDF"><i class="fas fa-download"></i></button>
                                    ` : ''}
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    body.html('<tr><td colspan="7" class="text-center py-4 text-muted">No invoices found for this advertiser</td></tr>');
                }
            } catch (e) {
                console.error(e);
                body.html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load invoices.</td></tr>');
            }
        }

        async function downloadPdf(id) {
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/invoices/${id}/download`);
                if (response.success && response.url) {
                    window.open(response.url, '_blank');
                } else {
                    throw new Error(response.message || "Failed to get download URL");
                }
            } catch (err) {
                alert('Download Error: ' + err.message);
            }
        }
    </script>
</body>

</html>