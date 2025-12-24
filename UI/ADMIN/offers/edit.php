<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Edit Offer';
include $pathPrefix . '/includes/head.php';
?>

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

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="offerTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabGeneral">
                        <i class="fas fa-info-circle mr-2"></i>General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabTargeting">
                        <i class="fas fa-crosshairs mr-2"></i>Targeting
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabAffiliates">
                        <i class="fas fa-users mr-2"></i>Affiliates
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabEvents">
                        <i class="fas fa-bullseye mr-2"></i>Events/Goals
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabMMP">
                        <i class="fas fa-share-alt mr-2"></i>MMP Config
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="tabGeneral">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Offer Details</h6>
                        </div>
                        <div class="card-body">
                            <div id="general-alert"></div>
                            <form id="frmOffer">
                                <input type="hidden" name="offer_id" id="offer_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Offer Name</label>
                                            <input type="text" name="offer_name" class="form-control">
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

                                <div class="form-group">
                                    <label>Offer URL</label>
                                    <textarea name="offer_url" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Ad Model</label>
                                            <select name="ad_model" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Revenue (Ad Pricing)</label>
                                            <input type="number" name="ad_pricing" class="form-control" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Aff Model</label>
                                            <select name="aff_model" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payout (Aff Pricing)</label>
                                            <input type="number" name="aff_pricing" class="form-control" step="0.01">
                                        </div>
                                    </div>
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
                                            <label>Start Date</label>
                                            <input type="datetime-local" name="campaign_start_date"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="datetime-local" name="campaign_end_date" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="offer_desc" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Private Note</label>
                                            <textarea name="private_note" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Terms</label>
                                            <textarea name="offer_terms" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update Offer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Targeting Tab -->
                <div class="tab-pane fade" id="tabTargeting">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-shield-alt mr-2"></i>Tracking Filters
                            </h6>
                            <small class="text-muted">Block or allow traffic based on location, device, and
                                browser</small>
                        </div>
                        <div class="card-body">
                            <div id="targeting-alert"></div>
                            <form id="frmTargeting">

                                <!-- Country -->
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label class="font-weight-bold"><i class="fas fa-globe mr-2"></i>Country</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="country_permission" class="form-control">
                                            <option value="allow">Allow</option>
                                            <option value="block">Block</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="target_country" class="form-control"
                                            placeholder="e.g., US, GB, IN (comma separated)">
                                    </div>
                                </div>

                                <!-- State -->
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label class="font-weight-bold"><i class="fas fa-map mr-2"></i>State</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="state_permission" class="form-control">
                                            <option value="allow">Allow</option>
                                            <option value="block">Block</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="target_state" class="form-control"
                                            placeholder="e.g., California, Texas (comma separated)">
                                    </div>
                                </div>

                                <!-- City -->
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label class="font-weight-bold"><i class="fas fa-city mr-2"></i>City</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="city_permission" class="form-control">
                                            <option value="allow">Allow</option>
                                            <option value="block">Block</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="target_city" class="form-control"
                                            placeholder="e.g., New York, Los Angeles (comma separated)">
                                    </div>
                                </div>

                                <hr>

                                <!-- Device -->
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label class="font-weight-bold"><i
                                                class="fas fa-mobile-alt mr-2"></i>Device</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="device_permission" class="form-control">
                                            <option value="allow">Allow</option>
                                            <option value="block">Block</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
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
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label class="font-weight-bold"><i
                                                class="fab fa-chrome mr-2"></i>Browser</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="browser_permission" class="form-control">
                                            <option value="allow">Allow</option>
                                            <option value="block">Block</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
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

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save Targeting
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Affiliates Tab -->
                <div class="tab-pane fade" id="tabAffiliates">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card-modern">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-user-plus mr-2"></i>Assign Affiliates
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
                                        <i class="fas fa-link mr-2"></i>Tracking URL
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

                <!-- Events Tab -->
                <div class="tab-pane fade" id="tabEvents">
                    <div class="card-modern">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">Events / Goals</h6>
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idListEvents">
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MMP Tab -->
                <div class="tab-pane fade" id="tabMMP">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">MMP Configuration</h6>
                        </div>
                        <div class="card-body">
                            <div id="mmp-alert"></div>
                            <form id="frmMMP">
                                <div class="form-group">
                                    <label>MMP Platform</label>
                                    <select name="mmp_id" id="mmp_id" class="form-control">
                                        <option value="">Select Platform</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Attribution URL</label>
                                    <textarea name="attribution_url" class="form-control" rows="3"
                                        placeholder="Paste the attribution link from the MMP..."></textarea>
                                    <small class="text-muted">This URL will be used as the destination (Attribution Link)
                                        for this offer.</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Save MMP Settings</button>
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
                alert("No offer ID specified");
                window.location.href = 'approved.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            await loadOfferData();
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
                $('textarea[name="private_note"]').val(offer.private_note);
                $('textarea[name="offer_terms"]').val(offer.offer_terms);
                $('select[name="offer_status"]').val(offer.offer_status);

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
                $('input[name="target_country"]').val(offer.target_country || '');
                $('select[name="state_permission"]').val(offer.state_permission || 'allow');
                $('input[name="target_state"]').val(offer.target_state || '');
                $('select[name="city_permission"]').val(offer.city_permission || 'allow');
                $('input[name="target_city"]').val(offer.target_city || '');
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
                alert("Failed to load offer data: " + error.message);
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
                tbody.append(`
                    <tr>
                        <td>${evt.event_name}</td>
                        <td><code>${evt.event_token}</code></td>
                        <td>
                            <button onclick="editEvent(${evt.id})" class="btn btn-sm btn-outline-primary mr-1">Edit</button>
                            <button onclick="deleteEvent(${evt.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    </tr>
                `);
            });

            if (events.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center py-4 text-muted">No events configured</td></tr>');
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
                        offer_desc: $('textarea[name="offer_desc"]').val(),
                        private_note: $('textarea[name="private_note"]').val(),
                        offer_terms: $('textarea[name="offer_terms"]').val()
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
                        country_permission: $('select[name="country_permission"]').val(),
                        target_country: $('input[name="target_country"]').val(),
                        state_permission: $('select[name="state_permission"]').val(),
                        target_state: $('input[name="target_state"]').val(),
                        city_permission: $('select[name="city_permission"]').val(),
                        target_city: $('input[name="target_city"]').val(),
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
                if (res.status === 1) location.reload();
                else alert(res.error || 'Failed');
            } catch (e) { alert(e.message); }
        }

        async function unassignOffer(oid, aid) {
            if (!confirm("Unassign this affiliate?")) return;
            try {
                const res = await API.fetchWithAuth(`admin/offer/unassign/${oid}/${aid}`);
                if (res.status === 1) location.reload();
                else alert(res.error || 'Failed');
            } catch (e) { alert(e.message); }
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

            // Append Agency ID from localStorage to ensure link works on localhost/single-domain
            const agencyId = localStorage.getItem('agency_id');
            if (agencyId && !finalUrl.includes('eai=')) {
                // Check if URL already has query params
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
            // Navigator clipboard api needs a secure context (https)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    alert('Copied to clipboard!');
                }, function (err) {
                    alert('Could not copy text: ', err);
                });
            } else {
                // Fallback for non-secure context (http)
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";  // Avoid scrolling to bottom
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    const msg = successful ? 'successful' : 'unsuccessful';
                    alert('Copied to clipboard!');
                } catch (err) {
                    alert('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textArea);
            }
        }

        function createNewOfferEvent() {
            const name = prompt("Event Name:");
            if (!name) return;
            const token = prompt("Event Token:");
            if (!token) return;

            API.fetchWithAuth('admin/offer/save-event', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ offer_id: offerId, event_name: name, event_token: token })
            }).then(res => {
                if (res.status === 1) location.reload();
                else alert(res.error || 'Failed');
            });
        }

        function editEvent(eid) {
            alert("Edit event: " + eid);
        }

        function deleteEvent(eid) {
            if (!confirm("Delete this event?")) return;
            API.fetchWithAuth(`admin/offer/remove-event/${offerId}/${eid}`).then(res => {
                if (res.status === 1) location.reload();
                else alert(res.error || 'Failed');
            });
        }
    </script>
</body>

</html>