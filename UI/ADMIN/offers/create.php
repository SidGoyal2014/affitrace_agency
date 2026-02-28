<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Create Offer';
include $pathPrefix . '/includes/head.php';
?>
<!-- Countries and Locations JS -->
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/locations.js"></script>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Create New Offer</h1>
                <a href="approved.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Offers
                </a>
            </div>

            <!-- Scroll Navigation -->
            <ul class="offer-scroll-nav" id="offerTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#tabGeneral">
                        <i class="fas fa-info-circle"></i> General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tabTargeting">
                        <i class="fas fa-crosshairs"></i> Targeting
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tabAffiliates">
                        <i class="fas fa-users"></i> Affiliates
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tabEvents">
                        <i class="fas fa-bullseye"></i> Events/Goals
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tabMMP">
                        <i class="fas fa-share-alt"></i> MMP Config
                    </a>
                </li>
            </ul>

            <div class="offer-sections">

                <!-- ============ GENERAL TAB ============ -->
                <div class="offer-section mb-5 pt-3" id="tabGeneral">
                    <div class="card-modern">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-plus-circle mr-2" style="color: var(--primary-main);"></i>Offer Details
                            </h6>
                            <span class="badge badge-info">All fields with <span class="text-danger">•</span> are
                                required</span>
                        </div>
                        <div class="card-body">
                            <div id="general-alert"></div>
                            <form id="createOfferForm">

                                <!-- Basic Info -->
                                <div class="offer-form-section section-hero">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-info-circle"></i></div>
                                        <h6>Basic Information</h6>
                                        <span class="section-badge"><i class="fas fa-star"></i> Get started</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Offer Name <span class="required-dot"></span></label>
                                                <input type="text" name="offer_name" class="form-control"
                                                    placeholder="Enter a descriptive offer name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Advertiser <span class="required-dot"></span></label>
                                                <select name="advertiser_id" class="form-control" required>
                                                    <option value="">Select Advertiser</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Category <span class="required-dot"></span></label>
                                                <select name="category_id" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Offer URL <span class="required-dot"></span></label>
                                                <textarea name="offer_url" class="form-control" rows="2"
                                                    placeholder="https://example.com/offer?click_id={tid}" required></textarea>
                                                <span class="field-hint">Use <code>{tid}</code> for click tracking ID</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="form-divider">

                                <!-- Pricing -->
                                <div class="offer-form-section">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-dollar-sign"></i></div>
                                        <h6>Pricing Model</h6>
                                    </div>

                                    <div class="field-group">
                                        <div class="field-group-label">
                                            <i class="fas fa-chart-line"></i> Revenue & Payout
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="pricing-pair">
                                                    <div class="form-group">
                                                        <label>Ad Model <span class="required-dot"></span></label>
                                                        <select name="ad_model" class="form-control" required></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Revenue</label>
                                                        <input type="number" name="ad_pricing" class="form-control"
                                                            step="0.01" value="0.00" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="pricing-pair">
                                                    <div class="form-group">
                                                        <label>Aff Model <span class="required-dot"></span></label>
                                                        <select name="aff_model" class="form-control" required></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Payout</label>
                                                        <input type="number" name="aff_pricing" class="form-control"
                                                            step="0.01" value="0.00" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="form-divider">

                                <!-- Settings & Schedule -->
                                <div class="offer-form-section">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-cog"></i></div>
                                        <h6>Settings & Schedule</h6>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Currency <span class="required-dot"></span></label>
                                                <select name="currency" class="form-control" required>
                                                    <option value="USD" selected>USD</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="offer_status" class="form-control">
                                                    <option value="approve">Approved</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="paused">Paused</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><i class="fas fa-play-circle mr-1 text-success"></i> Start
                                                    Date</label>
                                                <input type="datetime-local" name="campaign_start_date"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><i class="fas fa-stop-circle mr-1 text-danger"></i> End
                                                    Date</label>
                                                <input type="datetime-local" name="campaign_end_date"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="form-divider">

                                <!-- Conversion Tracking -->
                                <div class="offer-form-section">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-exchange-alt"></i></div>
                                        <h6>Conversion Tracking</h6>
                                    </div>

                                    <div class="tracking-config-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold">Tracking Method</label>
                                                    <select name="conversion_tracking_method" class="form-control">
                                                        <option value="s2s">Server Postback (S2S)</option>
                                                        <option value="image_pixel">Image Pixel</option>
                                                        <option value="iframe_pixel">Iframe Pixel</option>
                                                        <option value="script_pixel">Javascript Pixel</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="form-divider">

                                <!-- Description -->
                                <div class="offer-form-section">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-align-left"></i></div>
                                        <h6>Description</h6>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea name="offer_desc" class="form-control" rows="3"
                                                    placeholder="Enter a public description for this offer..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="form-submit-area">
                                    <a href="approved.php" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Offer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ============ TARGETING TAB ============ -->
                <div class="offer-section mb-5 pt-3" id="tabTargeting">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-shield-alt mr-2" style="color: var(--primary-main);"></i>Tracking
                                Filters
                            </h6>
                            <small class="text-muted">Block or allow traffic based on location, device, and
                                browser</small>
                        </div>
                        <div class="card-body">
                            <div id="targeting-alert"></div>

                            <!-- Location group -->
                            <div class="targeting-group-title">
                                <i class="fas fa-map-marker-alt mr-1"></i> Location Filters
                            </div>

                            <!-- Country -->
                            <div class="targeting-row">
                                <div class="targeting-label">
                                    <i class="fas fa-globe"></i> Country
                                </div>
                                <div class="targeting-permission">
                                    <select name="country_permission" class="form-control">
                                        <option value="allow">Allow</option>
                                        <option value="block">Block</option>
                                    </select>
                                </div>
                                <div class="targeting-value">
                                    <select name="target_country" class="form-control select2-country"
                                        style="width: 100%;">
                                        <option value="">Global (All Countries)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- State -->
                            <div class="targeting-row">
                                <div class="targeting-label">
                                    <i class="fas fa-map"></i> State
                                </div>
                                <div class="targeting-permission">
                                    <select name="state_permission" class="form-control">
                                        <option value="allow">Allow</option>
                                        <option value="block">Block</option>
                                    </select>
                                </div>
                                <div class="targeting-value">
                                    <select name="target_state" class="form-control select2-state"
                                        style="width: 100%;">
                                        <option value="">All States</option>
                                    </select>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="targeting-row">
                                <div class="targeting-label">
                                    <i class="fas fa-city"></i> City
                                </div>
                                <div class="targeting-permission">
                                    <select name="city_permission" class="form-control">
                                        <option value="allow">Allow</option>
                                        <option value="block">Block</option>
                                    </select>
                                </div>
                                <div class="targeting-value">
                                    <input type="text" name="target_city" class="form-control"
                                        placeholder="e.g., New York, Los Angeles (comma separated)">
                                </div>
                            </div>

                            <!-- Device/Browser group -->
                            <div class="targeting-group-title">
                                <i class="fas fa-laptop mr-1"></i> Device & Browser Filters
                            </div>

                            <!-- Device -->
                            <div class="targeting-row">
                                <div class="targeting-label">
                                    <i class="fas fa-mobile-alt"></i> Device
                                </div>
                                <div class="targeting-permission">
                                    <select name="device_permission" class="form-control">
                                        <option value="allow">Allow</option>
                                        <option value="block">Block</option>
                                    </select>
                                </div>
                                <div class="targeting-value">
                                    <select name="target_device" class="form-control">
                                        <option value="">All Devices</option>
                                        <option value="iPhone">iPhone</option>
                                        <option value="Android">Android</option>
                                        <option value="iPad">iPad</option>
                                        <option value="Desktop">Desktop</option>
                                        <option value="Mobile">Mobile (Any)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Browser -->
                            <div class="targeting-row">
                                <div class="targeting-label">
                                    <i class="fab fa-chrome"></i> Browser
                                </div>
                                <div class="targeting-permission">
                                    <select name="browser_permission" class="form-control">
                                        <option value="allow">Allow</option>
                                        <option value="block">Block</option>
                                    </select>
                                </div>
                                <div class="targeting-value">
                                    <select name="browser" class="form-control">
                                        <option value="">All Browsers</option>
                                        <option value="Chrome">Chrome</option>
                                        <option value="Safari">Safari</option>
                                        <option value="Firefox">Firefox</option>
                                        <option value="Edge">Edge</option>
                                        <option value="Opera">Opera</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ AFFILIATES TAB ============ -->
                <div class="offer-section mb-5 pt-3" id="tabAffiliates">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card-modern">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-user-plus mr-2" style="color: var(--primary-main);"></i>Assign
                                        Affiliates
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-flush mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Affiliate</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="affiliateTableBody">
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">
                                                        <i class="fas fa-info-circle mr-2"></i>Save the offer first to manage affiliates
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="card-modern">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-link mr-2" style="color: var(--primary-main);"></i>Tracking URL
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="trackingUrlDisplay" class="alert alert-info">
                                        Save the offer first to generate tracking URLs.
                                    </div>

                                    <h6 class="mt-4 mb-3">Available Tokens</h6>
                                    <div id="tokensListContainer" class="row">
                                        <!-- Token checkboxes populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ EVENTS TAB ============ -->
                <div class="offer-section mb-5 pt-3" id="tabEvents">
                    <div class="card-modern">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-bullseye mr-2" style="color: var(--primary-main);"></i>Events / Goals
                            </h6>
                            <button class="btn btn-primary btn-sm" disabled>
                                <i class="fas fa-plus mr-2"></i>Create Event
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0">
                                    <thead>
                                        <tr>
                                            <th>Event Name</th>
                                            <th>Token</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idListEvents">
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-2"></i>Save the offer first to manage events
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ MMP TAB ============ -->
                <div class="offer-section mb-5 pt-3" id="tabMMP">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-share-alt mr-2" style="color: var(--primary-main);"></i>MMP
                                Configuration
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="offer-form-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>MMP Platform</label>
                                            <select name="mmp_id" id="mmp_id" class="form-control">
                                                <option value="">Select Platform</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Attribution URL</label>
                                    <textarea name="attribution_url" class="form-control" rows="3"
                                        placeholder="Paste the attribution link from the MMP..."></textarea>
                                    <small class="text-muted">This URL will be used as the destination (Attribution
                                        Link) for this offer.</small>
                                </div>
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
        $(document).ready(async function () {
            // Initialize Select2 for ALL dropdowns (searchable)
            function initSelect2() {
                $('select.form-control').each(function () {
                    const $el = $(this);
                    if ($el.data('select2')) return;

                    const opts = {
                        theme: 'bootstrap4',
                        width: '100%',
                        allowClear: true,
                        dropdownParent: $el.parent()
                    };

                    const firstOpt = $el.find('option[value=""]');
                    if (firstOpt.length) {
                        opts.placeholder = firstOpt.text();
                    }

                    if ($el.hasClass('select2-country')) {
                        opts.placeholder = "Select Countries (Leave empty for Global)";
                    }

                    if ($el.hasClass('select2-state')) {
                        opts.tags = true;
                    }

                    $el.select2(opts);

                    // Firefox fix: search box focus
                    $el.on('select2:open', function () {
                        setTimeout(function () {
                            const searchField = document.querySelector('.select2-container--open .select2-search__field');
                            if (searchField) searchField.focus();
                        }, 50);
                    });
                });
            }

            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            // Load initial data
            try {
                const response = await API.fetchWithAuth('admin/offer/create');

                // Advertisers
                const advSelect = $('select[name="advertiser_id"]');
                advSelect.empty().append('<option value="">Select Advertiser</option>');
                (response.advertisers || []).forEach(adv => {
                    advSelect.append(`<option value="${adv.id}">${adv.name}</option>`);
                });

                // Categories
                const catSelect = $('select[name="category_id"]');
                catSelect.empty().append('<option value="">Select Category</option>');
                (response.arrCategories || []).forEach(cat => {
                    catSelect.append(`<option value="${cat.id}">${cat.category_name}</option>`);
                });

                // Ad Models
                const adModelSelect = $('select[name="ad_model"]');
                adModelSelect.empty().append('<option value="">Select Model</option>');
                (response.admodels || []).forEach(model => {
                    adModelSelect.append(`<option value="${model.id}">${model.name}</option>`);
                });

                // Aff Models
                const affModelSelect = $('select[name="aff_model"]');
                affModelSelect.empty().append('<option value="">Select Model</option>');
                (response.admodels || []).forEach(model => {
                    affModelSelect.append(`<option value="${model.id}">${model.name}</option>`);
                });

                // Currencies
                const curSelect = $('select[name="currency"]');
                curSelect.empty().append('<option value="">Select Currency</option>');
                let currencies = response.arrCur || [];
                if (!Array.isArray(currencies) && typeof currencies === 'object') {
                    currencies = Object.values(currencies);
                }
                if (Array.isArray(currencies)) {
                    currencies.forEach(cur => {
                        curSelect.append(`<option value="${cur}">${cur}</option>`);
                    });
                }

                // Countries (for targeting)
                const countrySelect = $('select[name="target_country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });

                // States (for targeting)
                if (typeof LOCATIONS !== 'undefined') {
                    const stateSelect = $('select[name="target_state"]');
                    const states = LOCATIONS.states || {};
                    Object.entries(states).forEach(([code, name]) => {
                        stateSelect.append(new Option(name, code));
                    });
                }

                // MMP Platforms
                if (response.mmps) {
                    const mmpSelect = $('select[name="mmp_id"]');
                    mmpSelect.empty().append('<option value="">Select Platform</option>');
                    (response.mmps || []).forEach(m => {
                        mmpSelect.append(`<option value="${m.id}">${m.name}</option>`);
                    });
                }

                // Initialize Select2 after all options loaded
                initSelect2();

            } catch (e) {
                console.error("Failed to load initial data:", e);
                $('#general-alert').html(`<div class="alert alert-danger">Failed to load reference data: ${e.message}</div>`);
            }

            // ScrollSpy for Sections
            $(window).on('scroll', function () {
                let current = '';
                const scrollPos = $(window).scrollTop() + 160;

                $('.offer-section').each(function () {
                    if (scrollPos >= $(this).offset().top) {
                        current = $(this).attr('id');
                    }
                });

                if (current) {
                    $('#offerTabs .nav-link').removeClass('active');
                    $(`#offerTabs .nav-link[href="#${current}"]`).addClass('active');
                }
            });

            // Smooth scrolling for nav links
            $('#offerTabs a').on('click', function (e) {
                e.preventDefault();
                const targetId = $(this).attr('href');
                if ($(targetId).length) {
                    const targetPosition = $(targetId).offset().top - 120;
                    $('html, body').stop().animate({
                        scrollTop: targetPosition
                    }, 500, 'swing');

                    if (history.replaceState) {
                        history.replaceState(null, null, targetId);
                    }
                }
            });

            // Form Submit
            $('#createOfferForm').on('submit', async function (e) {
                e.preventDefault();
                $('#general-alert').empty();

                const formData = {
                    offer_name: $('input[name="offer_name"]').val(),
                    advertiser_id: parseInt($('select[name="advertiser_id"]').val()),
                    category_id: parseInt($('select[name="category_id"]').val()),
                    offer_url: $('textarea[name="offer_url"]').val(),
                    preview_url: $('input[name="preview_url"]').val(),
                    ad_model: $('select[name="ad_model"]').val(),
                    aff_model: $('select[name="aff_model"]').val(),
                    ad_pricing: parseFloat($('input[name="ad_pricing"]').val()).toFixed(2),
                    aff_pricing: parseFloat($('input[name="aff_pricing"]').val()).toFixed(2),
                    currency: $('select[name="currency"]').val(),
                    target_country: $('select[name="target_country"]').val(),
                    country_permission: $('select[name="country_permission"]').val(),
                    target_state: $('select[name="target_state"]').val(),
                    state_permission: $('select[name="state_permission"]').val(),
                    target_city: $('input[name="target_city"]').val(),
                    city_permission: $('select[name="city_permission"]').val(),
                    target_device: $('select[name="target_device"]').val(),
                    device_permission: $('select[name="device_permission"]').val(),
                    browser: $('select[name="browser"]').val(),
                    browser_permission: $('select[name="browser_permission"]').val(),
                    offer_visibility: $('select[name="offer_visibility"]').val(),
                    offer_status: $('select[name="offer_status"]').val(),
                    campaign_start_date: $('input[name="campaign_start_date"]').val(),
                    campaign_end_date: $('input[name="campaign_end_date"]').val(),
                    offer_desc: $('textarea[name="offer_desc"]').val(),
                    conversion_tracking_method: $('select[name="conversion_tracking_method"]').val(),
                    mmp_id: $('select[name="mmp_id"]').val(),
                    attribution_url: $('textarea[name="attribution_url"]').val()
                };

                try {
                    const response = await API.fetchWithAuth('admin/offer/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });

                    if (response.status === 1) {
                        $('#general-alert').html('<div class="alert alert-success">Offer created successfully! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'approved.php', 1500);
                    } else {
                        let errorMsg = 'Failed to create offer';
                        if (response.error) {
                            if (typeof response.error === 'object') {
                                errorMsg = Object.values(response.error).flat().join('<br>');
                            } else {
                                errorMsg = response.error;
                            }
                        }
                        throw new Error(errorMsg);
                    }
                } catch (error) {
                    $('#general-alert').html(`<div class="alert alert-danger">${error.message}</div>`);
                    window.scrollTo(0, 0);
                }
            });
        });
    </script>
</body>

</html>