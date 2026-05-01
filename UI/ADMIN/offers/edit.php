<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Edit Offer';

// Bootstrap Laravel removed to prevent 500 error

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
                <h1>Edit Offer</h1>
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
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-edit mr-2" style="color: var(--primary-main);"></i>Offer Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="general-alert"></div>
                            <form id="frmOffer">
                                <input type="hidden" name="offer_id" id="offer_id">

                                <!-- Basic Info -->
                                <div class="offer-form-section section-hero">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-info-circle"></i></div>
                                        <h6>Basic Information</h6>
                                        <span class="section-badge"><i class="fas fa-pencil-alt"></i> Core
                                            details</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Offer Name</label>
                                                <input type="text" name="offer_name" class="form-control"
                                                    placeholder="Enter offer name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Advertiser</label>
                                                <select name="advertiser_id" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category_id" class="form-control"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Offer URL</label>
                                                <textarea name="offer_url" class="form-control" rows="2"
                                                    placeholder="https://example.com/offer?click_id={tid}"></textarea>
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
                                                        <label>Ad Model</label>
                                                        <select name="ad_model" class="form-control"></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Revenue</label>
                                                        <input type="number" name="ad_pricing" class="form-control"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="pricing-pair">
                                                    <div class="form-group">
                                                        <label>Aff Model</label>
                                                        <select name="aff_model" class="form-control"></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Payout</label>
                                                        <input type="number" name="aff_pricing" class="form-control"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="form-divider">

                                <!-- Settings -->
                                <div class="offer-form-section">
                                    <div class="section-title">
                                        <div class="section-icon"><i class="fas fa-cog"></i></div>
                                        <h6>Settings & Schedule</h6>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <select name="currency" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="offer_status" class="form-control">
                                                    <option value="approve">Approved</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="ended">Ended</option>
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
                                                    <select name="conversion_tracking_method" class="form-control"
                                                        onchange="updatePixelCodeDisplay()">
                                                        <option value="s2s">Server Postback (S2S)</option>
                                                        <option value="image_pixel">Image Pixel</option>
                                                        <option value="iframe_pixel">Iframe Pixel</option>
                                                        <option value="script_pixel">Javascript Pixel</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group mb-0" id="pixelCodeGroup" style="display:none;">
                                                    <label>Tracking Code (For Advertiser)</label>
                                                    <div class="input-group">
                                                        <input type="text" id="pixelCodeDisplay" class="form-control"
                                                            readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button"
                                                                onclick="copyPixelCode()">Copy</button>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Place on Thank You page. Replace
                                                        <code>CLICK_ID_MACRO</code> with the advertiser's macro
                                                        for our Click ID.</small>
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Offer
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
                            <form id="frmTargeting">

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

                                <div class="form-submit-area">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Targeting
                                    </button>
                                </div>
                            </form>
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
                                                    <td colspan="3" class="text-center py-4 text-muted">Loading...</td>
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
                                        Select an affiliate to generate tracking URL.
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
                            <button onclick="createNewOfferEvent()" class="btn btn-primary btn-sm">
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
                                            <th>Revenue</th>
                                            <th>Payout</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idListEvents">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
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
                            <div id="mmp-alert"></div>
                            <form id="frmMMP">
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
                                <div class="form-submit-area">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save MMP Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const offerId = urlParams.get('id');
        let currentBaseUrl = "";

        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }
            if (!offerId) {
                Toast.error('Error', 'No offer ID specified');
                window.location.href = 'approved.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            await loadOfferData();

            // Initialize Select2 for ALL dropdowns (searchable)
            $('select.form-control').each(function () {
                const $el = $(this);
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

            // Event Listeners for Cascading
            $('select[name="target_country"]').on('change', function () {
                populateStateDropdown($(this).val());
            });

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

            // Initial Scroll to Hash
            const hash = window.location.hash;
            if (hash && $(hash).length) {
                setTimeout(() => {
                    const targetPosition = $(hash).offset().top - 120;
                    $('html, body').stop().animate({
                        scrollTop: targetPosition
                    }, 500, 'swing');
                }, 500);
            }


        });

        async function loadOfferData() {
            try {
                const response = await API.fetchWithAuth(`admin/offer/edit/${offerId}`);
                const offer = response.offer;

                // General Tab
                $('#offer_id').val(offer.id);
                $('input[name="offer_name"]').val(offer.offer_name);
                $('textarea[name="offer_url"]').val(offer.offer_url);
                $('input[name="ad_pricing"]').val(parseFloat(offer.ad_pricing).toFixed(2));
                $('input[name="aff_pricing"]').val(parseFloat(offer.aff_pricing).toFixed(2));
                $('input[name="campaign_start_date"]').val(offer.campaign_start_date);
                $('input[name="campaign_end_date"]').val(offer.campaign_end_date);
                $('textarea[name="offer_desc"]').val(offer.offer_desc);
                $('select[name="offer_status"]').val(offer.offer_status);
                $('select[name="conversion_tracking_method"]').val(offer.conversion_tracking_method || 's2s').trigger('change');

                // Dropdowns
                populateSelect('advertiser_id', response.advertisers, 'id', 'name', offer.advertiser_id);
                populateSelect('category_id', response.arrCategories, 'id', 'category_name', offer.category_id);
                populateSelect('ad_model', response.admodels, 'id', 'name', offer.ad_model);
                populateSelect('aff_model', response.admodels, 'id', 'name', offer.aff_model);

                const curSelect = $('select[name="currency"]').empty();

                let currencies = response.arrCur || [];
                if (!Array.isArray(currencies) && typeof currencies === 'object') {
                    currencies = Object.values(currencies);
                }

                if (Array.isArray(currencies)) {
                    currencies.forEach(cur => {
                        curSelect.append(`<option value="${cur}" ${cur == offer.currency ? 'selected' : ''}>${cur}</option>`);
                    });
                }

                // Targeting Tab
                $('select[name="country_permission"]').val(offer.country_permission || 'allow');

                // Populate Countries Dropdown
                const countrySelect = $('select[name="target_country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });

                // Pre-select values
                if (offer.target_country) {
                    let val = offer.target_country;
                    if (val.includes(',')) {
                        val = val.split(',')[0].trim();
                    }
                    countrySelect.val(val).trigger('change');
                } else {
                    countrySelect.val("").trigger('change');
                }
                $('select[name="state_permission"]').val(offer.state_permission || 'allow');

                // Populate State and City
                let savedState = offer.target_state ? offer.target_state.split(',')[0].trim() : '';
                let savedCity = offer.target_city ? offer.target_city.split(',')[0].trim() : '';

                populateStateDropdown(countrySelect.val(), savedState);
                $('input[name="target_city"]').val(savedCity);

                $('select[name="city_permission"]').val(offer.city_permission || 'allow');
                $('select[name="device_permission"]').val(offer.device_permission || 'allow');
                $('select[name="target_device"]').val(offer.target_device || '');
                $('select[name="browser_permission"]').val(offer.browser_permission || 'allow');
                $('select[name="browser"]').val(offer.browser || '');

                // Affiliates Tab
                loadAffiliates(response.arrAffiliates || [], response.arrOA || [], offer);

                // Events Tab
                loadEvents(response.arrEvents || []);

                // Tokens
                loadTokens(response.offerTokens || []);

                // MMP
                loadMMP(response.mmpPlatforms || [], response.offerMmp);

            } catch (error) {
                console.error("Load Error:", error);
                Toast.error('Load Failed', 'Failed to load offer data: ' + error.message);
            }
        }

        function populateSelect(name, data, valueKey, textKey, selectedValue) {
            const sel = $(`select[name="${name}"]`).empty().append('<option value="">Select</option>');

            let items = data || [];
            if (!Array.isArray(items) && typeof items === 'object') {
                items = Object.values(items);
            }

            if (Array.isArray(items)) {
                items.forEach(item => {
                    sel.append(`<option value="${item[valueKey]}" ${item[valueKey] == selectedValue ? 'selected' : ''}>${item[textKey]}</option>`);
                });
            }
        }

        function populateStateDropdown(countryCode, selectedState = null) {
            const stateSelect = $('select[name="target_state"]');
            if (selectedState === null) selectedState = stateSelect.val();

            stateSelect.empty().append('<option value="">All States</option>');

            if (countryCode && typeof LOCATIONS !== 'undefined' && LOCATIONS[countryCode]) {
                const states = Object.keys(LOCATIONS[countryCode]);
                states.forEach(state => {
                    stateSelect.append(new Option(state, state));
                });
            }

            if (selectedState) {
                stateSelect.val(selectedState).trigger('change.select2');
            }
        }



        function loadAffiliates(affiliates, assignments, offer) {
            const assigned = {};
            assignments.forEach(oa => assigned[oa.affiliate_id] = oa);

            const tbody = $('#affiliateTableBody').empty();
            affiliates.forEach(aff => {
                const oa = assigned[aff.id];
                let status = '<span class="badge badge-secondary">Not Assigned</span>';
                let actions = `<button onclick="assignOffer(${offer.id}, ${aff.id})" class="btn btn-sm btn-primary">Assign</button>`;

                if (oa && oa.request_status !== 'Unassigned') {
                    status = `<span class="badge badge-${oa.request_status === 'Accepted' ? 'success' : 'warning'}">${oa.request_status}</span>`;
                    actions = `
                        <button onclick="showTrackingUrl('${oa.tracking_url}')" class="btn btn-sm btn-info mr-1">URL</button>
                        <button onclick="unassignOffer(${offer.id}, ${aff.id})" class="btn btn-sm btn-outline-danger">Unassign</button>
                    `;
                }

                tbody.append(`<tr><td>${aff.name}</td><td>${status}</td><td>${actions}</td></tr>`);
            });

            if (affiliates.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center py-4 text-muted">No affiliates found</td></tr>');
            }
        }

        function loadEvents(events) {
            const tbody = $('#idListEvents').empty();
            events.forEach(evt => {
                const rev = (evt.revenue !== null && evt.revenue !== undefined && evt.revenue !== '') ? parseFloat(evt.revenue).toFixed(2) : '<span class="text-muted">—</span>';
                const pay = (evt.payout !== null && evt.payout !== undefined && evt.payout !== '') ? parseFloat(evt.payout).toFixed(2) : '<span class="text-muted">—</span>';
                tbody.append(`
                    <tr>
                        <td>${evt.event_name}</td>
                        <td><code>${evt.event_token}</code></td>
                        <td>${rev}</td>
                        <td>${pay}</td>
                        <td>
                            <button onclick="editEvent(${evt.id})" class="btn btn-sm btn-outline-primary mr-1">Edit</button>
                            <button onclick="deleteEvent(${evt.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    </tr>
                `);
            });

            if (events.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No events configured</td></tr>');
            }
        }

        function loadTokens(tokens) {
            const container = $('#tokensListContainer').empty();
            tokens.forEach(token => {
                container.append(`
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <input type="checkbox" class="token-checkbox mr-2" data-macro="{${token.token_micro}}">
                            <code class="mr-2">{${token.token_micro}}</code>
                            <small class="text-muted">${token.token_desc || ''}</small>
                        </div>
                    </div>
                `);
            });

            $('.token-checkbox').on('change', updateTrackingURL);
        }

        function updatePixelCodeDisplay() {
            const method = $('select[name="conversion_tracking_method"]').val();
            const group = $('#pixelCodeGroup');
            const display = $('#pixelCodeDisplay');

            if (method === 's2s') {
                group.hide();
                return;
            }

            group.show();

            const pixelUrl = `${API_BASE_URL}/pixel?aff_click_id=CLICK_ID_MACRO`;

            let code = '';
            if (method === 'image_pixel') {
                code = `<img src="${pixelUrl}" height="1" width="1" />`;
            } else if (method === 'iframe_pixel') {
                code = `<iframe src="${pixelUrl}" height="1" width="1" frameborder="0"></iframe>`;
            } else if (method === 'script_pixel') {
                code = `<script src="${pixelUrl}"><\/script>`;
            }

            display.val(code);
        }

        function copyPixelCode() {
            const copyText = document.getElementById("pixelCodeDisplay");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            try {
                document.execCommand("copy");
                Toast.success('Copied!', 'Pixel code copied to clipboard');
            } catch (err) {
                Toast.error('Copy Failed', 'Unable to copy to clipboard');
            }
        }

        // Form Submissions
        $('#frmOffer').on('submit', async function (e) {
            e.preventDefault();
            try {
                const res = await API.fetchWithAuth('admin/offer/update_offer', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        offer_id: offerId,
                        offer_name: $('input[name="offer_name"]').val(),
                        conversion_tracking_method: $('select[name="conversion_tracking_method"]').val(),
                        advertiser_id: $('select[name="advertiser_id"]').val(),
                        category_id: $('select[name="category_id"]').val(),
                        offer_url: $('textarea[name="offer_url"]').val(),
                        ad_model: $('select[name="ad_model"]').val(),
                        ad_pricing: $('input[name="ad_pricing"]').val(),
                        aff_model: $('select[name="aff_model"]').val(),
                        aff_pricing: $('input[name="aff_pricing"]').val(),
                        currency: $('select[name="currency"]').val(),
                        campaign_start_date: $('input[name="campaign_start_date"]').val(),
                        campaign_end_date: $('input[name="campaign_end_date"]').val(),
                        offer_status: $('select[name="offer_status"]').val(),
                        offer_desc: $('textarea[name="offer_desc"]').val()
                    })
                });
                $('#general-alert').html(`<div class="alert alert-${res.status === 1 ? 'success' : 'danger'}">${res.msg || res.error || 'Error'}</div>`);
            } catch (err) {
                $('#general-alert').html(`<div class="alert alert-danger">${err.message}</div>`);
            }
        });

        $('#frmTargeting').on('submit', async function (e) {
            e.preventDefault();
            try {
                const res = await API.fetchWithAuth('admin/offer/update-targetting', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        offer_id: offerId,
                        country_permission: $('select[name="country_permission"]').val() || 'allow',
                        target_country: $('select[name="target_country"]').val(),
                        state_permission: $('select[name="state_permission"]').val(),
                        target_state: $('select[name="target_state"]').val(),
                        city_permission: $('select[name="city_permission"]').val(),
                        target_city: $('select[name="target_city"]').val(),
                        device_permission: $('select[name="device_permission"]').val(),
                        target_device: $('select[name="target_device"]').val(),
                        browser_permission: $('select[name="browser_permission"]').val(),
                        browser: $('select[name="browser"]').val()
                    })
                });
                $('#targeting-alert').html(`<div class="alert alert-${res.status === 1 ? 'success' : 'danger'}">${res.msg || res.error || 'Error'}</div>`);
            } catch (err) {
                $('#targeting-alert').html(`<div class="alert alert-danger">${err.message}</div>`);
            }
        });

        $('#frmMMP').on('submit', async function (e) {
            e.preventDefault();
            try {
                const res = await API.fetchWithAuth('admin/offer/update-mmp-details', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        offer_id: offerId,
                        mmp_id: $('#mmp_id').val(),
                        attribution_url: $('textarea[name="attribution_url"]').val()
                    })
                });
                $('#mmp-alert').html(`<div class="alert alert-${res.status === 1 ? 'success' : 'danger'}">${res.msg || res.error || 'Error'}</div>`);
            } catch (err) {
                $('#mmp-alert').html(`<div class="alert alert-danger">${err.message}</div>`);
            }
        });

        function loadMMP(platforms, details) {
            const sel = $('#mmp_id').empty().append('<option value="">Select Platform</option>');
            platforms.forEach(p => {
                sel.append(`<option value="${p.id}">${p.title}</option>`);
            });

            if (details) {
                sel.val(details.mmp_id);
                $('textarea[name="attribution_url"]').val(details.attribution_url);
            }
        }

        // Actions
        async function assignOffer(oid, aid) {
            try {
                const res = await API.fetchWithAuth(`admin/offer/assign/${oid}/${aid}`);
                if (res.status === 1) {
                    Toast.success('Success', 'Affiliate assigned successfully');
                    await loadOfferData();
                }
                else Toast.error('Assignment Failed', res.error || 'Failed to assign affiliate');
            } catch (e) { Toast.error('Error', e.message); }
        }

        async function unassignOffer(oid, aid) {
            if (!confirm("Unassign this affiliate?")) return;
            try {
                const res = await API.fetchWithAuth(`admin/offer/unassign/${oid}/${aid}`);
                if (res.status === 1) {
                    Toast.success('Success', 'Affiliate unassigned successfully');
                    await loadOfferData();
                }
                else Toast.error('Unassign Failed', res.error || 'Failed to unassign affiliate');
            } catch (e) { Toast.error('Error', e.message); }
        }

        function showTrackingUrl(url) {
            currentBaseUrl = url;
            updateTrackingURL();
        }

        function updateTrackingURL() {
            if (!currentBaseUrl) {
                $('#trackingUrlDisplay').html('<div class="text-muted">Select an affiliate to generate tracking URL.</div>');
                return;
            }
            let finalUrl = currentBaseUrl;

            const agencyId = localStorage.getItem('agency_id');
            if (agencyId && !finalUrl.includes('eai=')) {
                finalUrl += (finalUrl.includes('?') ? '&' : '?') + 'eai=' + agencyId;
            }

            $('.token-checkbox:checked').each(function () {
                const macro = $(this).data('macro');
                const key = macro.replace('{', '').replace('}', '');
                finalUrl += (finalUrl.includes('?') ? '&' : '?') + key + '=' + macro;
            });
            $('#trackingUrlDisplay').html(`
                <strong>Tracking URL:</strong><br>
                <code class="d-block mt-2 p-2 bg-light" style="word-break: break-all;">${finalUrl}</code>
                <button onclick="copyToClipboard('${finalUrl}')" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
            `);
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    Toast.success('Copied!', 'Tracking URL copied to clipboard');
                }, function (err) {
                    Toast.error('Copy Failed', 'Unable to copy to clipboard');
                });
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        Toast.success('Copied!', 'Tracking URL copied to clipboard');
                    } else {
                        Toast.error('Copy Failed', 'Unable to copy to clipboard');
                    }
                } catch (err) {
                    Toast.error('Copy Failed', 'Unable to copy to clipboard');
                }
                document.body.removeChild(textArea);
            }
        }

        function createNewOfferEvent() {
            openEventModal(null);
        }

        function openEventModal(eid) {
            $('#eventModalForm')[0].reset();
            $('#eventModalEventId').val('');
            $('#eventModalError').hide().text('');

            if (eid) {
                $('#eventModalTitle').text('Edit Event');
                $('#eventModalSaveBtn').text('Update Event');
                API.fetchWithAuth(`admin/offer/get-event/${eid}`).then(res => {
                    if (!res || !res.event) {
                        Toast.error('Error', 'Failed to load event data');
                        return;
                    }
                    const e = res.event;
                    $('#eventModalEventId').val(e.id);
                    $('#eventModalName').val(e.event_name);
                    $('#eventModalToken').val(e.event_token);
                    $('#eventModalRevenue').val(e.revenue !== null ? e.revenue : '');
                    $('#eventModalPayout').val(e.payout !== null ? e.payout : '');
                    $('#eventModal').modal('show');
                }).catch(() => Toast.error('Error', 'Failed to load event data'));
            } else {
                $('#eventModalTitle').text('Create Event');
                $('#eventModalSaveBtn').text('Create Event');
                $('#eventModal').modal('show');
            }
        }

        async function saveEventModal() {
            const eid = $('#eventModalEventId').val();
            const name = $('#eventModalName').val().trim();
            const token = $('#eventModalToken').val().trim();
            const revenue = $('#eventModalRevenue').val().trim();
            const payout = $('#eventModalPayout').val().trim();

            if (!name) { $('#eventModalError').text('Event name is required.').show(); return; }
            if (!token) { $('#eventModalError').text('Event token is required.').show(); return; }
            if (!/^[A-Za-z0-9_]+$/.test(token)) { $('#eventModalError').text('Token may only contain letters, numbers, and underscores.').show(); return; }

            $('#eventModalSaveBtn').prop('disabled', true).text('Saving...');
            $('#eventModalError').hide();

            try {
                let res;
                if (eid) {
                    res = await API.fetchWithAuth('admin/offer/update-event', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: eid, offer_id: offerId, event_name: name, event_token: token, revenue: revenue || null, payout: payout || null })
                    });
                } else {
                    res = await API.fetchWithAuth('admin/offer/save-event', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ offer_id: offerId, event_name: name, event_token: token, revenue: revenue || null, payout: payout || null })
                    });
                }

                if (res.status === 1) {
                    $('#eventModal').modal('hide');
                    Toast.success('Success', eid ? 'Event updated successfully' : 'Event created successfully');
                    await loadOfferData();
                } else {
                    $('#eventModalError').text(res.error || 'Failed to save event.').show();
                }
            } catch (err) {
                $('#eventModalError').text(err.message || 'An error occurred.').show();
            } finally {
                $('#eventModalSaveBtn').prop('disabled', false).text(eid ? 'Update Event' : 'Create Event');
            }
        }

        function editEvent(eid) {
            openEventModal(eid);
        }

        function deleteEvent(eid) {
            if (!confirm("Delete this event?")) return;
            API.fetchWithAuth(`admin/offer/remove-event/${offerId}/${eid}`).then(async res => {
                if (res.status === 1) {
                    Toast.success('Success', 'Event deleted successfully');
                    await loadOfferData();
                }
                else Toast.error('Delete Failed', res.error || 'Failed to delete event');
            });
        }
    </script>
</body>

<!-- Event Create/Edit Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Create Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="eventModalForm" autocomplete="off">
                    <input type="hidden" id="eventModalEventId">
                    <div class="alert alert-danger" id="eventModalError" style="display:none;"></div>

                    <div class="form-group">
                        <label>Event Name <span class="text-danger">*</span></label>
                        <input type="text" id="eventModalName" class="form-control" placeholder="e.g. Purchase, Registration">
                    </div>

                    <div class="form-group">
                        <label>Event Token <span class="text-danger">*</span></label>
                        <input type="text" id="eventModalToken" class="form-control" placeholder="e.g. purchase or reg_complete">
                        <small class="text-muted">Only letters, numbers, and underscores. Used in postback URL as <code>event_id=purchase</code>.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Revenue <small class="text-muted">(optional)</small></label>
                                <input type="number" id="eventModalRevenue" class="form-control" step="0.01" min="0" placeholder="Override offer revenue">
                                <small class="text-muted">Leave blank to use offer default.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payout <small class="text-muted">(optional)</small></label>
                                <input type="number" id="eventModalPayout" class="form-control" step="0.01" min="0" placeholder="Override affiliate payout">
                                <small class="text-muted">Leave blank to use offer default.</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="eventModalSaveBtn" onclick="saveEventModal()">Create Event</button>
            </div>
        </div>
    </div>
</div>
</html>