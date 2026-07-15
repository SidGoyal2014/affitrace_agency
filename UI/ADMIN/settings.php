<?php
$pathPrefix = '.';
$activePage = 'settings';
$pageTitle = 'Settings';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>Settings</h1>
            </div>


            <div class="row">
                <!-- Timezone Settings Card -->
                <div class="col-lg-6">
                    <div class="card-modern">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-globe-americas mr-2 text-primary"></i>
                            <h6 class="m-0 font-weight-bold">Timezone Preference</h6>
                        </div>
                        <div class="card-body">
                            <div id="tz-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Set your preferred timezone. All dates and times across the dashboard
                                (reports, logs, charts) will be displayed in this timezone.
                            </p>

                            <div class="form-group">
                                <label><strong>Your Timezone</strong></label>
                                <select name="timezone" id="timezone" class="form-control">
                                    <option value="">Loading...</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <small class="text-muted" id="tz-current-display">
                                    <i class="fas fa-clock mr-1"></i> Current: <span id="tz-current-label">—</span>
                                </small>
                                <button type="button" class="btn btn-primary" id="btnSaveTimezone">
                                    <i class="fas fa-save mr-1"></i> Save Timezone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Currency Settings Card -->
                <div class="col-lg-6">
                    <div class="card-modern">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-coins mr-2 text-primary"></i>
                            <h6 class="m-0 font-weight-bold">Default Currency Preference</h6>
                        </div>
                        <div class="card-body">
                            <div id="cur-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Set your default currency. All financial stats across the dashboard
                                (margin, revenue, payouts) will be displayed in this currency.
                            </p>

                            <div class="form-group">
                                <label><strong>Default Currency</strong></label>
                                <select name="default_currency" id="default_currency" class="form-control">
                                    <option value="">Loading...</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <small class="text-muted" id="cur-current-display">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Current: <span
                                        id="cur-current-label">—</span>
                                </small>
                                <button type="button" class="btn btn-primary" id="btnSaveCurrency">
                                    <i class="fas fa-save mr-1"></i> Save Currency
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- White Label & Branding Settings Card -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card-modern">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-palette mr-2 text-primary"></i>
                            <h6 class="m-0 font-weight-bold">Platform Branding &amp; White Label Settings</h6>
                        </div>
                        <div class="card-body">
                            <div id="brand-alert-container"></div>

                            <form id="brandingForm" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Platform Name -->
                                    <div class="col-md-6 form-group">
                                        <label for="platform_name"><strong>Platform Name <span
                                                    class="text-danger">*</span></strong></label>
                                        <input type="text" name="platform_name" id="platform_name" class="form-control"
                                            placeholder="e.g. GrowthX Performance Platform" required maxlength="100">
                                        <small class="text-muted">Used in page titles, sidebar headers, emails, and
                                            notifications.</small>
                                    </div>

                                    <!-- Support Email -->
                                    <div class="col-md-3 form-group">
                                        <label for="support_email"><strong>Support Email Address</strong></label>
                                        <input type="email" name="support_email" id="support_email" class="form-control"
                                            placeholder="e.g. support@yourcompany.com" maxlength="100">
                                        <small class="text-muted">Displayed to users in email footers.</small>
                                    </div>

                                    <!-- Custom Copyright -->
                                    <div class="col-md-3 form-group">
                                        <label for="custom_copyright"><strong>Custom Copyright Footer</strong></label>
                                        <input type="text" name="custom_copyright" id="custom_copyright"
                                            class="form-control" placeholder="e.g. Your Agency LLC" maxlength="150">
                                        <small class="text-muted">Custom copyright text shown at the bottom of
                                            pages/emails.</small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Platform Logo -->
                                    <div class="col-md-6 form-group">
                                        <label><strong>Platform Logo</strong></label>
                                        <div class="d-flex align-items-start mt-2">
                                            <div id="logo-preview-container"
                                                class="mr-3 border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                                style="width: 180px; height: 70px;">
                                                <span class="text-muted small">No Logo</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="platform_logo" id="platform_logo"
                                                    class="form-control-file"
                                                    accept="image/png, image/jpeg, image/svg+xml">
                                                <small class="text-muted d-block mt-1">PNG, JPG, SVG. Recommended:
                                                    250x80 px (Max 2MB)</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2 d-none"
                                                    id="btnRemoveLogo">
                                                    <i class="fas fa-trash mr-1"></i> Remove Logo
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Favicon -->
                                    <div class="col-md-6 form-group">
                                        <label><strong>Browser Favicon</strong></label>
                                        <div class="d-flex align-items-start mt-2">
                                            <div id="favicon-preview-container"
                                                class="mr-3 border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                                style="width: 70px; height: 70px;">
                                                <span class="text-muted small">No Icon</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="platform_favicon" id="platform_favicon"
                                                    class="form-control-file" accept="image/png, image/x-icon">
                                                <small class="text-muted d-block mt-1">PNG, ICO. Recommended: 32x32 or
                                                    64x64 px (Max 512KB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Brand Colors -->
                                    <div class="col-md-4 form-group">
                                        <label><strong>Primary Color</strong></label>
                                        <div class="input-group">
                                            <input type="color" id="primary_picker" class="form-control p-1"
                                                style="width: 50px; flex: 0 0 auto; height: 46px;" value="#2563EB">
                                            <input type="text" name="primary_color" id="primary_color"
                                                class="form-control text-uppercase" placeholder="#2563EB" required
                                                maxlength="7">
                                        </div>
                                        <small class="text-muted">Header active items and primary buttons.</small>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label><strong>Secondary Color</strong></label>
                                        <div class="input-group">
                                            <input type="color" id="secondary_picker" class="form-control p-1"
                                                style="width: 50px; flex: 0 0 auto; height: 46px;" value="#1E293B">
                                            <input type="text" name="secondary_color" id="secondary_color"
                                                class="form-control text-uppercase" placeholder="#1E293B" required
                                                maxlength="7">
                                        </div>
                                        <small class="text-muted">Secondary texts and labels.</small>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label><strong>Accent Color (Success Highlights)</strong></label>
                                        <div class="input-group">
                                            <input type="color" id="accent_picker" class="form-control p-1"
                                                style="width: 50px; flex: 0 0 auto; height: 46px;" value="#10B981">
                                            <input type="text" name="accent_color" id="accent_color"
                                                class="form-control text-uppercase" placeholder="#10B981" required
                                                maxlength="7">
                                        </div>
                                        <small class="text-muted">Badges, green indicators, and highlights.</small>
                                    </div>
                                </div>

                                <div class="border-top pt-3 mt-4 d-flex align-items-center justify-content-between">
                                    <!-- Live Color Preview indicator -->
                                    <div>
                                        <span class="mr-2">Live Preview:</span>
                                        <button type="button" class="btn btn-sm btn-primary" id="preview-btn"
                                            style="pointer-events: none;">Primary Button</button>
                                        <span class="badge badge-success ml-2" id="preview-badge"
                                            style="pointer-events: none;">Accent Badge</span>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="btnSaveBranding">
                                        <i class="fas fa-save mr-1"></i> Save Branding Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Timezone data -->
    <script src="<?php echo $pathPrefix; ?>/../common/js/timezones.js"></script>

    <script>
        $(document).ready(async function () {
            console.log('[Timezone Settings] Page initialised');

            if (!API.isAuthenticated()) {
                console.warn('[Timezone Settings] User not authenticated – redirecting to login');
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user && user.name) {
                $('.user-name').text(user.name);
            }

            // Populate the timezone dropdown from the static list
            const $tzSelect = $('#timezone');
            $tzSelect.empty().append('<option value="">Select your timezone...</option>');
            Object.entries(TIMEZONE_LIST).forEach(([key, label]) => {
                $tzSelect.append(`<option value="${key}">${label}</option>`);
            });
            console.log('[Timezone Settings] Dropdown populated with', Object.keys(TIMEZONE_LIST).length, 'timezones');

            // Initialize Select2 if available
            if ($.fn.select2) {
                $tzSelect.select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Search for a timezone...' });
            }

            // Load user's current timezone from the API
            try {
                console.log('[Timezone Settings] Fetching current user timezone from /me …');
                const data = await API.fetchWithAuth('me');
                console.log('[Timezone Settings] /me response:', data);
                if (data && data.timezone) {
                    $tzSelect.val(data.timezone).trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST[data.timezone] || data.timezone);
                    console.log('[Timezone Settings] Current timezone loaded:', data.timezone);
                } else {
                    $tzSelect.val('UTC').trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST['UTC']);
                    console.log('[Timezone Settings] No timezone set – defaulting to UTC');
                }
            } catch (err) {
                console.error('[Timezone Settings] Failed to load user timezone:', err);
            }

            // Load settings and currencies
            const $curSelect = $('#default_currency');
            try {
                console.log('[Settings] Fetching current settings and currencies...');
                const data = await API.fetchWithAuth('admin/settings');
                console.log('[Settings] GET settings response:', data);

                $curSelect.empty().append('<option value="">Select default currency...</option>');
                Object.entries(data.currencies || {}).forEach(([key, val]) => {
                    $curSelect.append(`<option value="${key}">${key} - ${val}</option>`);
                });

                if (data.default_currency) {
                    $curSelect.val(data.default_currency).trigger('change');
                    $('#cur-current-label').text(data.default_currency);
                }
            } catch (err) {
                console.error('[Settings] Failed to load settings:', err);
            }

            // Save currency button handler
            $('#btnSaveCurrency').on('click', async function () {
                const cur = $curSelect.val();
                if (!cur) {
                    $('#cur-alert-container').html('<div class="alert alert-warning">Please select a currency.</div>');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                $('#cur-alert-container').empty();

                try {
                    const result = await API.fetchWithAuth('admin/settings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ default_currency: cur })
                    });

                    if (result.status === 1) {
                        $('#cur-current-label').text(cur);
                        $('#cur-alert-container').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Default currency updated successfully!</div>');
                    } else {
                        throw new Error(result.msg || 'Update failed');
                    }
                } catch (err) {
                    console.error('[Settings] Currency update failed:', err);
                    $('#cur-alert-container').html(`<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> ${err.message || 'Failed to update default currency.'}</div>`);
                } finally {
                    $(this).prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Currency');
                }
            });

            // Save button handler
            $('#btnSaveTimezone').on('click', async function () {
                const tz = $tzSelect.val();
                console.log('[Timezone Settings] Save clicked – selected timezone:', tz);

                if (!tz) {
                    console.warn('[Timezone Settings] No timezone selected – aborting save');
                    $('#tz-alert-container').html('<div class="alert alert-warning">Please select a timezone.</div>');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                $('#tz-alert-container').empty();

                try {
                    console.log('[Timezone Settings] Sending POST update-timezone with payload:', { timezone: tz });
                    const result = await API.fetchWithAuth('update-timezone', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ timezone: tz })
                    });
                    console.log('[Timezone Settings] update-timezone response:', result);

                    if (result.timezone) {
                        // Update localStorage so TimezoneHelper picks up the new value
                        const user = JSON.parse(localStorage.getItem('user') || '{}');
                        const oldTz = user.timezone || 'N/A';
                        user.timezone = result.timezone;
                        localStorage.setItem('user', JSON.stringify(user));
                        console.log('[Timezone Settings] localStorage updated – old:', oldTz, '→ new:', result.timezone);

                        $('#tz-current-label').text(TIMEZONE_LIST[result.timezone] || result.timezone);
                        $('#tz-alert-container').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Timezone updated successfully!</div>');
                    }
                } catch (err) {
                    console.error('[Timezone Settings] Timezone update failed:', err);
                    $('#tz-alert-container').html(`<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> ${err.message || 'Failed to update timezone.'}</div>`);
                } finally {
                    $(this).prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Timezone');
                }
            });
        });
    </script>
    <script src="<?php echo $pathPrefix; ?>/../common/js/branding_settings.js"></script>
</body>

</html>