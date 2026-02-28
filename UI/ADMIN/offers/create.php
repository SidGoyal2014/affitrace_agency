<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Create Offer';
include $pathPrefix . '/includes/head.php';
?>

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

            <div class="row">
                <div class="col-lg-10">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Offer Details</h6>
                        </div>
                        <div class="card-body">
                            <div id="alert-container"></div>

                            <form id="createOfferForm">
                                <!-- Basic Info -->
                                <h6 class="text-muted mb-3">Basic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Offer Name <span class="text-danger">*</span></label>
                                            <input type="text" name="offer_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Advertiser <span class="text-danger">*</span></label>
                                            <select name="advertiser_id" class="form-control" required>
                                                <option value="">Select Advertiser</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control" required>
                                                <option value="">Select Category</option>
                                                <option value="1">Finance</option>
                                                <option value="2">Gaming</option>
                                                <option value="3">E-commerce</option>
                                                <option value="4">Education</option>
                                                <option value="5">Health</option>
                                                <option value="6">Entertainment</option>
                                                <option value="7">Utilities</option>
                                                <option value="8">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Target Country</label>
                                            <select name="target_country" class="form-control select2-country">
                                                <option value="">Global (All Countries)</option>
                                                <!-- Populated by JS -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- URLs -->
                                <h6 class="text-muted mb-3">URLs</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Offer URL <span class="text-danger">*</span></label>
                                            <input type="url" name="offer_url" class="form-control"
                                                placeholder="https://example.com/offer?click_id={tid}" required>
                                            <small class="text-muted">Use {tid} for click tracking ID</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Preview URL</label>
                                            <input type="url" name="preview_url" class="form-control"
                                                placeholder="https://example.com/preview">
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Pricing -->
                                <h6 class="text-muted mb-3">Pricing Model</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Ad Model <span class="text-danger">*</span></label>
                                            <select name="ad_model" class="form-control" required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Revenue (Ad Pricing)</label>
                                            <input type="number" name="ad_pricing" class="form-control" step="0.01"
                                                value="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Aff Model <span class="text-danger">*</span></label>
                                            <select name="aff_model" class="form-control" required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payout (Aff Pricing)</label>
                                            <input type="number" name="aff_pricing" class="form-control" step="0.01"
                                                value="0.00">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Currency <span class="text-danger">*</span></label>
                                            <select name="currency" class="form-control" required>
                                                <option value="USD" selected>USD</option>
                                                <option value="EUR">EUR</option>
                                                <option value="INR">INR</option>
                                                <option value="GBP">GBP</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Visibility</label>
                                            <select name="offer_visibility" class="form-control">
                                                <option value="public">Public</option>
                                                <option value="private">Private</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
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

                                <hr class="my-4">

                                <!-- Description -->
                                <!-- <h6 class="text-muted mb-3">Additional Details</h6>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="offer_desc" class="form-control" rows="3"
                                        placeholder="Public description visible to affiliates"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Private Note</label>
                                    <textarea name="private_note" class="form-control" rows="2"
                                        placeholder="Internal notes (not visible to affiliates)"></textarea>
                                </div> -->

                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save mr-2"></i> Create Offer
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


    <!-- Countries JS -->
    <script src="../../common/js/countries.js"></script>
    <script>
        $(document).ready(async function () {
            // Initialize Select2 for country dropdown
            $('.select2-country').select2({
                placeholder: "Select Countries (Leave empty for Global)",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4'
            });

            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            // Load initial data (Advertisers, Categories, AdModels, Currencies)
            try {
                // The create endpoint returns all necessary reference data
                const response = await API.fetchWithAuth('admin/offer/create');

                // 1. Advertisers
                const advSelect = $('select[name="advertiser_id"]');
                advSelect.empty().append('<option value="">Select Advertiser</option>');
                (response.advertisers || []).forEach(adv => {
                    advSelect.append(`<option value="${adv.id}">${adv.name}</option>`);
                });

                // 2. Categories
                const catSelect = $('select[name="category_id"]');
                catSelect.empty().append('<option value="">Select Category</option>');
                (response.arrCategories || []).forEach(cat => {
                    catSelect.append(`<option value="${cat.id}">${cat.category_name}</option>`);
                });

                // 3. Ad Models (Populate ID for ad_model)
                const adModelSelect = $('select[name="ad_model"]');
                adModelSelect.empty().append('<option value="">Select Model</option>');
                (response.admodels || []).forEach(model => {
                    adModelSelect.append(`<option value="${model.id}">${model.name}</option>`);
                });

                // 4. Aff Models (same as Ad Models - uses ID)
                const affModelSelect = $('select[name="aff_model"]');
                affModelSelect.empty().append('<option value="">Select Model</option>');
                (response.admodels || []).forEach(model => {
                    affModelSelect.append(`<option value="${model.id}">${model.name}</option>`);
                });

                // 5. Currencies
                const curSelect = $('select[name="currency"]');
                curSelect.empty().append('<option value="">Select Currency</option>');

                let currencies = response.arrCur || [];
                // Handle if it's an object (associative array) instead of array
                if (!Array.isArray(currencies) && typeof currencies === 'object') {
                    currencies = Object.values(currencies);
                }

                if (Array.isArray(currencies)) {
                    currencies.forEach(cur => {
                        curSelect.append(`<option value="${cur}">${cur}</option>`);
                    });
                }

                // 6. Countries
                const countrySelect = $('select[name="target_country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });

            } catch (e) {
                console.error("Failed to load initial data:", e);
                $('#alert-container').html(`<div class="alert alert-danger">Failed to load reference data: ${e.message}</div>`);
            }

            $('#createOfferForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();

                const formData = {
                    offer_name: $('input[name="offer_name"]').val(),
                    advertiser_id: parseInt($('select[name="advertiser_id"]').val()),
                    category_id: parseInt($('select[name="category_id"]').val()),
                    offer_url: $('input[name="offer_url"]').val(),
                    preview_url: $('input[name="preview_url"]').val(),

                    // ad_model is now ID
                    ad_model: $('select[name="ad_model"]').val(),

                    aff_model: $('select[name="aff_model"]').val(),
                    ad_pricing: parseFloat($('input[name="ad_pricing"]').val()).toFixed(2),
                    aff_pricing: parseFloat($('input[name="aff_pricing"]').val()).toFixed(2),
                    currency: $('select[name="currency"]').val(),
                    target_country: $('select[name="target_country"]').val(),
                    country_permission: $('input[name="country_permission"]').val(),
                    offer_visibility: $('select[name="offer_visibility"]').val(),
                    offer_status: $('select[name="offer_status"]').val(),
                    campaign_start_date: $('input[name="campaign_start_date"]').val(),
                    campaign_end_date: $('input[name="campaign_end_date"]').val(),
                    offer_desc: $('textarea[name="offer_desc"]').val(),
                    private_note: $('textarea[name="private_note"]').val()
                };

                try {
                    const response = await API.fetchWithAuth('admin/offer/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });

                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success">Offer created successfully! Redirecting...</div>');
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
                    $('#alert-container').html(`<div class="alert alert-danger">${error.message}</div>`);
                    window.scrollTo(0, 0);
                }
            });
        });
    </script>
</body>

</html>