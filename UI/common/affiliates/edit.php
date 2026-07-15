<?php
$rolePrefix = isset($api_link_to_edit_affiliate) ? explode('/', $api_link_to_edit_affiliate)[0] : 'admin';
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/timezones.js"></script>
<script src="../../common/js/currencies.js"></script>
<script src="../../common/js/pmt_methods.js"></script>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="page-header mb-4">
                <h1>Edit Affiliate Profile</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>

            <div class="card card-modern">
                <!-- Tab Headers -->
                <div class="card-header p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="affiliateTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                                <i class="fas fa-user-edit mr-2 text-primary"></i>Edit Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                                <i class="fas fa-info-circle mr-2 text-success"></i>Overview & Docs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="stats-tab" data-toggle="tab" href="#stats" role="tab">
                                <i class="fas fa-chart-line mr-2 text-info"></i>Performance Stats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="offers-tab" data-toggle="tab" href="#offers" role="tab">
                                <i class="fas fa-tags mr-2 text-warning"></i>Assigned Offers & Overrides
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="postback-tab" data-toggle="tab" href="#postback" role="tab">
                                <i class="fas fa-link mr-2 text-purple"></i>Postback Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="payouts-tab" data-toggle="tab" href="#payouts" role="tab">
                                <i class="fas fa-history mr-2 text-secondary"></i>Payout History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="referrals-tab" data-toggle="tab" href="#referrals" role="tab">
                                <i class="fas fa-sitemap mr-2 text-danger"></i>Referral Tree
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- Single Form Wrapping Config Tabs -->
                    <form class="user" id="affiliateForm" method="post" action="#">
                        <input type="hidden" name="user_type" value="affiliate">
                        
                        <div class="tab-content" id="affiliateTabsContent">
                            
                            <!-- Tab 1: Edit Profile -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Basic Information</h4>
                                
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label><strong>Name:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label><strong>Email:</strong> <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label><strong>Phone:</strong></label>
                                        <input type="text" name="phone" class="form-control" placeholder="Enter Phone No">
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
                                    <div class="col-sm-6">
                                        <label><strong>Company Name:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" name="company" class="form-control" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>Job Title:</strong></label>
                                        <input type="text" name="job_title" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Instant Messenger:</strong></label>
                                        <input type="text" name="instant_messenger_name" class="form-control" placeholder="Skype, Telegram">
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>IM Username/Value:</strong></label>
                                        <input type="text" name="instant_messenger_value" class="form-control">
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Address & Region</h4>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Address:</strong></label>
                                        <input type="text" name="address" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>City:</strong></label>
                                        <input type="text" name="city" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label><strong>State:</strong></label>
                                        <input type="text" name="state" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label><strong>Zip Code:</strong></label>
                                        <input type="text" name="zipcode" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label><strong>Country:</strong></label>
                                        <select name="country" class="form-control select2-country">
                                            <option value="">(Select Country)</option>
                                        </select>
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Billing & Financials</h4>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Currency:</strong></label>
                                        <select name="currency" class="form-control">
                                            <option value="">Select Currency</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>Additional Tracking URL Tokens:</strong></label>
                                        <input type="text" name="additional_tracking_url_tokens" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Time Zone:</strong></label>
                                        <select name="time_zone" class="form-control">
                                            <option value="">Select Timezone</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>Payment Method:</strong></label>
                                        <select name="payment_method" class="form-control">
                                            <option value="">Select Payment Method</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label><strong>Payment Details:</strong></label>
                                        <textarea name="payment_details" rows="5" class="form-control" placeholder="Bank details, wire instructions, wallet address..."></textarea>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><strong>Billing Terms:</strong></label>
                                        <textarea name="billing_terms" rows="5" class="form-control" placeholder="Net 15, Net 30, Weekly..."></textarea>
                                    </div>
                                </div>

                                <h4 class="mt-4 mb-4 text-gray-800 font-weight-bold">Tax Details</h4>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Tax Name (GST/VAT)</label>
                                        <input type="text" name="add_tax_basic_name" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Tax Type (IGST/VAT)</label>
                                        <input type="text" name="add_tax_type" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Tax Number/ID</label>
                                        <input type="text" name="add_tax_number" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Tax % (e.g. 18.00)</label>
                                        <input type="text" name="add_tax" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Profile Overview -->
                            <div class="tab-pane fade" id="overview" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Profile Overview & Extra Details</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><strong>Website URL:</strong></label>
                                            <input type="url" name="website" class="form-control" placeholder="https://example.com">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Traffic Source Description:</strong></label>
                                            <input type="text" name="traffic_source" class="form-control" placeholder="e.g. FB Ads, SEO Blog, Native Ads">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Tags:</strong></label>
                                            <input type="text" name="tags" class="form-control" placeholder="comma-separated tags">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Referred By (Affiliate ID):</strong></label>
                                            <input type="number" name="referred_by" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><strong>Global Clicks Cap:</strong></label>
                                            <input type="number" name="global_clicks_cap" class="form-control" placeholder="No limit if blank">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Global Conversions Cap:</strong></label>
                                            <input type="number" name="global_conversions_cap" class="form-control" placeholder="No limit if blank">
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
                                    <div class="col-12 mt-3">
                                        <div class="form-group">
                                            <label><strong>Private Note (Internal Admin Use Only):</strong></label>
                                            <textarea name="private_note" class="form-control" rows="4" placeholder="Add comments/notes about this affiliate..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-file-upload mr-2 text-primary"></i>Upload Documents</h5>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="form-group mb-0">
                                                    <label>Select Verification Document (Tax W8/W9, ID proof, Company Cert):</label>
                                                    <div class="custom-file mt-2">
                                                        <input type="file" class="custom-file-input" id="docUploadInput">
                                                        <label class="custom-file-label" id="uploadFileLabel" for="docUploadInput">Choose file...</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h5 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-folder-open mr-2 text-info"></i>Uploaded Verification Docs</h5>
                                        <div class="list-group" id="docList">
                                            <div class="list-group-item text-muted text-center py-3">No documents uploaded yet</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Performance Stats -->
                            <div class="tab-pane fade" id="stats" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Affiliate Performance Stats</h4>
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
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Payout</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_payout">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card border-left-success shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_revenue">$0.00</div>
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
                                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Earnings Per Click (EPC)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat_epc">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Assigned Offers & Overrides -->
                            <div class="tab-pane fade" id="offers" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Assigned Offers & Custom Payout Rules</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="assignedOffersTable">
                                        <thead>
                                            <tr>
                                                <th>Offer Name</th>
                                                <th>Default Pricing (Rev/Payout)</th>
                                                <th>Revenue Override</th>
                                                <th>Payout Override</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignedOffersBody">
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No offers assigned to this affiliate</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 5: Postback Settings -->
                            <div class="tab-pane fade" id="postback" role="tabpanel">
                                <h4 class="mb-2 text-gray-800 font-weight-bold">Global Postback Configuration</h4>
                                <p class="text-muted small mb-4">Set up query parameter mappings for affiliate tracking. These are matched with URL placeholders on postbacks.</p>
                                
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label><strong>Postback URL:</strong></label>
                                        <input type="url" name="pb_url" class="form-control" placeholder="e.g. http://track.affiliate.com/postback?click_id={tid}&payout={payout}">
                                        <small class="form-text text-muted">Use standard macros like `{tid}` (Transaction ID), `{payout}`, `{offer_id}`, etc.</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Transaction ID Parameter</label>
                                            <input type="text" name="pb_tid" class="form-control" placeholder="e.g. click_id">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate ID Parameter</label>
                                            <input type="text" name="pb_aff_id" class="form-control" placeholder="e.g. aff_id">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Event ID Parameter</label>
                                            <input type="text" name="pb_event_id" class="form-control" placeholder="e.g. event_id">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Sub Affiliate ID Parameter</label>
                                            <input type="text" name="pb_sub_aff_id" class="form-control" placeholder="e.g. sub_aff">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate Sub1 Parameter</label>
                                            <input type="text" name="pb_aff_sub1" class="form-control" placeholder="e.g. sub1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate Sub2 Parameter</label>
                                            <input type="text" name="pb_aff_sub2" class="form-control" placeholder="e.g. sub2">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate Sub3 Parameter</label>
                                            <input type="text" name="pb_aff_sub3" class="form-control" placeholder="e.g. sub3">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate Sub4 Parameter</label>
                                            <input type="text" name="pb_aff_sub4" class="form-control" placeholder="e.g. sub4">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Affiliate Sub5 Parameter</label>
                                            <input type="text" name="pb_aff_sub5" class="form-control" placeholder="e.g. sub5">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 6: Payout History -->
                            <div class="tab-pane fade" id="payouts" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Conversion & Payout Logs</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="payoutsHistoryTable">
                                        <thead>
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>Offer Name</th>
                                                <th>Status</th>
                                                <th>Payout</th>
                                                <th>Revenue</th>
                                                <th>Profit</th>
                                                <th>Country</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payoutsHistoryBody">
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No payout history found for this affiliate</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 7: Referral Tree -->
                            <div class="tab-pane fade" id="referrals" role="tabpanel">
                                <h4 class="mb-4 text-gray-800 font-weight-bold">Referred Affiliates</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="referralsTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Company</th>
                                                <th>Signup Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="referralsBody">
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No referred affiliates found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- Update Button Row -->
                        <div class="row mt-4 card-footer bg-white border-top-0 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Update Affiliate Profile
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
        // Global variables to hold parsed database response data
        let overrideList = [];
        let affiliateId = null;

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
            affiliateId = id;

            if (!id) {
                alert('No ID provided');
                window.location.href = 'list.php';
                return;
            }

            // 1. Populate standard static options
            // Countries
            const countrySelect = $('select[name="country"]');
            Object.entries(COUNTRIES).forEach(([code, name]) => {
                countrySelect.append(new Option(name, code));
            });

            // Timezone 
            const timezoneSelect = $('select[name="time_zone"]');
            Object.entries(TIMEZONE_LIST).forEach(([code, name]) => {
                timezoneSelect.append(new Option(name, code));
            });

            // Currency
            const currencySelect = $('select[name="currency"]');
            Object.entries(CURRENCIES).forEach(([code, name]) => {
                currencySelect.append(new Option(name, code));
            });

            // Payment Methods
            const paymentMethodSelect = $('select[name="payment_method"]');
            Object.entries(PMT_METHODS).forEach(([code, name]) => {
                paymentMethodSelect.append(new Option(name, code));
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

            // 2. Fetch Affiliate Details
            try {
                const response = await API.fetchWithAuth(`<?php echo $api_link_to_edit_affiliate; ?>/${id}`);
                const data = response.user;
                const dataAud = response.aud;
                overrideList = response.payout_overrides || [];

                if (data) {
                    // Form fields basic binding
                    $('input[name="name"]').val(data.name);
                    $('input[name="phone"]').val(data.phone);                    
                    $('input[name="email"]').val(data.email);
                    $('input[name="company"]').val(data.company);
                    $('input[name="job_title"]').val(data.job_title);
                    $('input[name="instant_messenger_name"]').val(data.instant_messenger_name);
                    $('input[name="instant_messenger_value"]').val(data.instant_messenger_value);
                    $('input[name="address"]').val(data.address);
                    $('input[name="city"]').val(data.city);
                    $('input[name="state"]').val(data.state);
                    countrySelect.val(data.country || "");
                    $('input[name="zipcode"]').val(data.zipcode);
                    $('#employee_id').val(data.employee_id || "");

                    // Form fields AUD binding
                    $('input[name="additional_tracking_url_tokens"]').val(dataAud.additional_tracking_url_tokens);
                    timezoneSelect.val(dataAud.time_zone || "");
                    currencySelect.val(dataAud.currency || "");
                    paymentMethodSelect.val(dataAud.payment_method || "");
                    $('textarea[name="payment_details"]').val(dataAud.payment_details);
                    $('textarea[name="billing_terms"]').val(dataAud.billing_terms);
                    $('input[name="add_tax_basic_name"]').val(dataAud.add_tax_basic_name);
                    $('input[name="add_tax_type"]').val(dataAud.add_tax_type);
                    $('input[name="add_tax_number"]').val(dataAud.add_tax_number);
                    $('input[name="add_tax"]').val(dataAud.add_tax);

                    // Form fields Overview binding
                    $('input[name="website"]').val(dataAud.website || "");
                    $('input[name="traffic_source"]').val(dataAud.traffic_source || "");
                    $('input[name="tags"]').val(dataAud.tags || "");
                    $('input[name="referred_by"]').val(dataAud.referred_by || "");
                    $('input[name="global_clicks_cap"]').val(dataAud.global_clicks_cap || "");
                    $('input[name="global_conversions_cap"]').val(dataAud.global_conversions_cap || "");
                    $('input[name="status_reason"]').val(dataAud.status_reason || "");
                    $('textarea[name="private_note"]').val(dataAud.private_note || "");

                    // Form fields Postback binding
                    if (response.postback_cols) {
                        $('input[name="pb_url"]').val(response.postback_cols.url || "");
                        $('input[name="pb_tid"]').val(response.postback_cols.tid || "");
                        $('input[name="pb_aff_id"]').val(response.postback_cols.aff_id || "");
                        $('input[name="pb_event_id"]').val(response.postback_cols.event_id || "");
                        $('input[name="pb_sub_aff_id"]').val(response.postback_cols.sub_aff_id || "");
                        $('input[name="pb_aff_sub1"]').val(response.postback_cols.aff_sub1 || "");
                        $('input[name="pb_aff_sub2"]').val(response.postback_cols.aff_sub2 || "");
                        $('input[name="pb_aff_sub3"]').val(response.postback_cols.aff_sub3 || "");
                        $('input[name="pb_aff_sub4"]').val(response.postback_cols.aff_sub4 || "");
                        $('input[name="pb_aff_sub5"]').val(response.postback_cols.aff_sub5 || "");
                    }

                    // Render Uploaded Documents
                    renderDocs(dataAud.docs);

                    // Set Status in Overview Radio and Basic status
                    if (data.status) {
                        let statusVal = (typeof data.status === 'object') ? data.status.value : data.status;
                        $(`input[name="status"][value="${statusVal}"]`).prop('checked', true);
                    } else {
                        $('#statusActiveTab').prop('checked', true);
                    }
                }
            } catch (error) {
                console.error("Load Error:", error);
                alert('Failed to load affiliate details');
            }

            // 3. Fetch stats
            try {
                const statRes = await API.fetchWithAuth(`${rolePrefix}/affiliates/${id}/stats`);
                if (statRes && statRes.success) {
                    const s = statRes.stats;
                    $('#stat_clicks').text(s.total_clicks);
                    $('#stat_conv_approved').text(s.approved_conversions);
                    $('#stat_conv_pending').text(s.pending_conversions);
                    $('#stat_conv_rejected').text(s.rejected_conversions);
                    $('#stat_payout').text('$' + parseFloat(s.total_payout).toFixed(2));
                    $('#stat_revenue').text('$' + parseFloat(s.total_revenue).toFixed(2));
                    $('#stat_profit').text('$' + parseFloat(s.total_profit).toFixed(2));
                    $('#stat_cr').text(parseFloat(s.cr).toFixed(2) + '%');
                    $('#stat_epc').text('$' + parseFloat(s.epc).toFixed(2));
                }
            } catch (e) {
                console.error("Failed to load stats:", e);
            }

            // 4. Fetch Assigned Offers
            loadAssignedOffers();

            // 5. Fetch Payout History
            loadPayoutHistory();

            // 6. Fetch Referrals
            loadReferrals();

            // Document Upload AJAX trigger
            $('#docUploadInput').change(async function() {
                const file = this.files[0];
                if (!file) return;

                const label = $('#uploadFileLabel');
                label.text('Uploading: ' + file.name + '...');

                const uploadFormData = new FormData();
                uploadFormData.append('uid', id);
                uploadFormData.append('doc', file);

                try {
                    await API.fetchWithAuth(`${rolePrefix}/affiliates/upload-doc`, {
                        method: 'POST',
                        body: uploadFormData
                    });
                    alert('Document uploaded successfully!');
                    window.location.reload();
                } catch(e) {
                    alert('Document upload failed: ' + e.message);
                    label.text('Choose file...');
                }
            });

            // General Profile Form Update Submit Handler
            $('#affiliateForm').on('submit', async function (e) {
                e.preventDefault();
                $('.alert').remove();

                const form = $(this);
                const formData = new FormData(form[0]);
                const payload = Object.fromEntries(formData.entries());

                // Set user ID
                payload.uid = id;
                if (!payload.job_title) payload.job_title = 'Affiliate';

                try {
                    const response = await API.fetchWithAuth(`<?php echo $api_link_to_update_affiliate; ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (response.status === 1) {
                        alert(response.msg || 'Affiliate updated successfully');
                        window.location.href = 'list.php';
                    } else {
                        if (response.error && typeof response.error === 'object') {
                            alert('Validation failed: ' + Object.values(response.error).flat().join(', '));
                        } else {
                            throw new Error(response.error || 'Update failed');
                        }
                    }
                } catch (error) {
                    alert('Update failed: ' + error.message);
                }
            });
        });

        // Submethods

        function renderDocs(docsJson) {
            const list = $('#docList');
            let docs = [];
            try {
                docs = typeof docsJson === 'string' ? JSON.parse(docsJson) : docsJson;
            } catch(e) {}

            if (docs && docs.length > 0) {
                list.empty();
                docs.forEach(doc => {
                    const fileUrl = `${API.BASE_URL}/../storage/affiliate_docs/${affiliateId}/${doc}`;
                    list.append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-truncate font-weight-bold" style="max-width: 70%;"><i class="fas fa-file mr-2 text-primary"></i>${doc}</span>
                            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </div>
                    `);
                });
            }
        }

        async function loadAssignedOffers() {
            const body = $('#assignedOffersBody');
            body.html('<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>');
            
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/reports/offers-assigned/${affiliateId}`);
                body.empty();

                if (response && response.length > 0) {
                    response.forEach(off => {
                        // Find matching override rule
                        const override = overrideList.find(rule => parseInt(rule.offer_id) === parseInt(off.id));
                        
                        const row = `
                            <tr>
                                <td>
                                    <div class="font-weight-bold">${off.offer_name}</div>
                                    <small class="text-muted">ID: ${off.id} | Model: ${off.ad_model}</small>
                                </td>
                                <td>
                                    <div>Rev: ${off.ad_pricing} ${off.currency}</div>
                                    <div>Pay: ${off.aff_pricing} ${off.currency}</div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <input type="number" step="0.01" class="form-control" id="revenue_override_${off.id}" value="${override ? override.revenue_pricing : ''}" placeholder="${off.ad_pricing}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <input type="number" step="0.01" class="form-control" id="payout_override_${off.id}" value="${override ? override.payout_pricing : ''}" placeholder="${off.aff_pricing}">
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="saveOverride(${off.id})">
                                        <i class="fas fa-save mr-1"></i> Save
                                    </button>
                                </td>
                            </tr>
                        `;
                        body.append(row);
                    });
                } else {
                    body.html('<tr><td colspan="5" class="text-center py-4 text-muted">No offers assigned to this affiliate</td></tr>');
                }
            } catch(e) {
                console.error("Assigned Offers fetch error:", e);
                body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load assigned offers</td></tr>');
            }
        }

        async function saveOverride(offerId) {
            const payout = $(`#payout_override_${offerId}`).val();
            const revenue = $(`#revenue_override_${offerId}`).val();
            
            try {
                const res = await API.fetchWithAuth(`${rolePrefix}/affiliates/payout-override`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        offer_id: offerId,
                        affiliate_id: affiliateId,
                        payout: payout || null,
                        revenue: revenue || null
                    })
                });
                alert(res.msg || 'Override saved successfully');
                window.location.reload();
            } catch (e) {
                alert('Failed to save override: ' + e.message);
            }
        }

        async function loadPayoutHistory() {
            const body = $('#payoutsHistoryBody');
            body.html('<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>');
            
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/reports/affiliate-payouts/${affiliateId}`);
                body.empty();

                if (response && response.length > 0) {
                    response.forEach(c => {
                        const row = `
                            <tr>
                                <td><small class="font-weight-bold text-monospace">${c.tid}</small></td>
                                <td>${c.offer_name}</td>
                                <td><span class="badge badge-${c.status === 'confirmed' || c.status === 'approved' ? 'success' : (c.status === 'pending' ? 'warning' : 'danger')}">${c.status}</span></td>
                                <td>$${parseFloat(c.payout).toFixed(2)}</td>
                                <td>$${parseFloat(c.revenue).toFixed(2)}</td>
                                <td>$${parseFloat(c.profit).toFixed(2)}</td>
                                <td>${c.country || '-'}</td>
                                <td><small>${c.date} ${c.time}</small></td>
                            </tr>
                        `;
                        body.append(row);
                    });
                } else {
                    body.html('<tr><td colspan="8" class="text-center py-4 text-muted">No payout history found for this affiliate</td></tr>');
                }
            } catch(e) {
                console.error("Payout history fetch error:", e);
                body.html('<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load payout history</td></tr>');
            }
        }

        async function loadReferrals() {
            const body = $('#referralsBody');
            body.html('<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>');
            
            try {
                const response = await API.fetchWithAuth(`${rolePrefix}/affiliates/${affiliateId}/referrals`);
                body.empty();

                if (response && response.referrals && response.referrals.length > 0) {
                    response.referrals.forEach(ref => {
                        const row = `
                            <tr>
                                <td>${ref.id}</td>
                                <td><a href="edit.php?id=${ref.id}" class="font-weight-bold text-primary">${ref.name}</a></td>
                                <td>${ref.email}</td>
                                <td>${ref.company || '-'}</td>
                                <td><small>${new Date(ref.created_at).toLocaleString()}</small></td>
                            </tr>
                        `;
                        body.append(row);
                    });
                } else {
                    body.html('<tr><td colspan="5" class="text-center py-4 text-muted">No referred affiliates found</td></tr>');
                }
            } catch(e) {
                console.error("Referrals fetch error:", e);
                body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load referred affiliates</td></tr>');
            }
        }
    </script>
</body>

</html>