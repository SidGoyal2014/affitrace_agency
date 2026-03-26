<?php
$pathPrefix = '..';
$activePage = 'integrations';
$pageTitle = 'Integrations';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Data Integrations</h1>
                <p class="text-muted">Import Offers, Affiliates, and Advertisers from external networks.</p>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="api-tab" data-toggle="tab" href="#api" role="tab"
                                aria-controls="api" aria-selected="true">API Integration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="csv-tab" data-toggle="tab" href="#csv" role="tab"
                                aria-controls="csv" aria-selected="false">CSV Import</a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="integrationTabsContent">
                        <!-- API Tab -->
                        <div class="tab-pane fade show active" id="api" role="tabpanel" aria-labelledby="api-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card-modern">
                                        <div class="card-header">
                                            <h6 class="m-0 font-weight-bold">Configure Integration</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="general-alert"></div>
                                            <form id="frmIntegration">
                                                <input type="hidden" name="config_id" id="config_id">

                                                <div class="form-group">
                                                    <label>Provider</label>
                                                    <select name="provider" id="provider" class="form-control">
                                                        <option value="offer18">Offer18</option>
                                                        <option value="trackier">Trackier</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Account ID / MID / AID</label>
                                                    <input type="text" name="account_id" class="form-control"
                                                        placeholder="Enter Network ID or Affiliate ID">
                                                    <small class="text-muted">For Offer18 Network API, enter your
                                                        Account ID (mid). For Affiliate API, enter Affiliate ID
                                                        (aid).</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>API Key</label>
                                                    <input type="text" name="api_key" class="form-control"
                                                        placeholder="Enter API Key">
                                                </div>

                                                <div class="form-group" id="secret-group">
                                                    <label>Secret Key (Optional)</label>
                                                    <input type="password" name="api_secret" class="form-control"
                                                        placeholder="Enter Secret Key">
                                                    <small class="text-muted">Required for specific Network API
                                                        calls.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>API Domain (Optional)</label>
                                                    <input type="text" name="api_domain" class="form-control"
                                                        placeholder="e.g., https://api.offer18.com">
                                                    <small class="text-muted">Leave blank to use default. Useful for
                                                        custom domains.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                </div>

                                                <div class="text-right">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-2"></i>Save Configuration
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card-modern">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold">Sync Data</h6>
                                            <span id="last_sync_badge" class="badge badge-secondary">Never Synced</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center py-4">
                                                <i class="fas fa-sync fa-3x text-gray-300 mb-3" id="syncIcon"></i>
                                                <p class="mb-4">Click below to start the import process. This may take a
                                                    few minutes depending on the data size.</p>

                                                <div id="sync-alert"></div>

                                                <button id="btnSync" class="btn btn-success btn-lg" disabled>
                                                    <i class="fas fa-cloud-download-alt mr-2"></i>Start Import
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CSV Tab -->
                        <div class="tab-pane fade" id="csv" role="tabpanel" aria-labelledby="csv-tab">
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="card-modern">
                                        <div class="card-header">
                                            <h6 class="m-0 font-weight-bold">Upload CSV Data</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="csv-alert"></div>
                                            <form id="frmCsvImport" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label>Import Type</label>
                                                    <select name="type" id="import_type" class="form-control">
                                                        <option value="advertiser">Advertisers</option>
                                                        <option value="affiliate">Affiliates</option>
                                                        <option value="clicks">Clicks</option>
                                                        <option value="conversions">Conversions</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Select CSV File</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="csv_file"
                                                            name="csv_file" accept=".csv, .txt" required>
                                                        <label class="custom-file-label" for="csv_file">Choose
                                                            file...</label>
                                                    </div>
                                                </div>

                                                <div class="alert alert-info mt-3">
                                                    <strong>Required Columns (Case Insensitive):</strong>
                                                    <ul class="mb-0 pl-3" id="csv-columns-hint">
                                                        <li>Advertisers: email, name (or company)</li>
                                                    </ul>
                                                </div>

                                                <div class="text-right mt-4">
                                                    <button type="submit" class="btn btn-primary" id="btnCsvUpload">
                                                        <i class="fas fa-file-upload mr-2"></i>Upload & Import
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
        let currentIntegrationId = null;

        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }
            if (!PermissionGuard.check('integrations', 'can_view')) return;

            await loadConfig();
        });

        async function loadConfig() {
            try {
                const res = await API.fetchWithAuth('employee/integrations/config');
                // Assuming we handle one active integration for simplicity in UI, or list.
                // Logic: Grab the first implementation found for currently selected provider or just first one.
                const integrations = res.data;

                // If data exists, pre-fill form
                if (Array.isArray(integrations) && integrations.length > 0) {
                    const config = integrations[0]; // Take first
                    currentIntegrationId = config.id;

                    $('#provider').val(config.provider);
                    $('input[name="account_id"]').val(config.account_id);
                    $('input[name="api_key"]').val(config.api_key);
                    $('input[name="api_secret"]').val(config.api_secret); // Note: might be hidden in real secure app
                    $('input[name="api_domain"]').val(config.api_domain);
                    $('select[name="status"]').val(config.status);

                    if (config.last_sync_at) {
                        $('#last_sync_badge').text('Last Synced: ' + new Date(config.last_sync_at).toLocaleString())
                            .removeClass('badge-secondary').addClass('badge-info');
                    }

                    if (config.status === 'active') {
                        $('#btnSync').removeAttr('disabled');
                    }
                }

            } catch (e) {
                console.error(e);
            }
        }

        $('#frmIntegration').on('submit', async function (e) {
            e.preventDefault();
            const data = {
                provider: $('#provider').val(),
                account_id: $('input[name="account_id"]').val(),
                api_key: $('input[name="api_key"]').val(),
                api_secret: $('input[name="api_secret"]').val(),
                api_domain: $('input[name="api_domain"]').val(),
                status: $('select[name="status"]').val(),
            };

            try {
                const res = await API.fetchWithAuth('employee/integrations/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                if (res.status === 1) {
                    $('#general-alert').html('<div class="alert alert-success">' + res.message + '</div>');
                    // Reload to update IDs/Status
                    await loadConfig();
                } else {
                    $('#general-alert').html('<div class="alert alert-danger">' + (res.error || 'Failed to save') + '</div>');
                }
            } catch (err) {
                $('#general-alert').html('<div class="alert alert-danger">' + err.message + '</div>');
            }
        });

        $('#btnSync').on('click', async function () {
            if (!currentIntegrationId) return;

            if (!confirm("Start data import? This will fetch Offers, Affiliates, and Advertisers.")) return;

            const btn = $(this);
            const icon = $('#syncIcon');

            btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...');
            icon.addClass('fa-spin');
            $('#sync-alert').empty();

            try {
                const res = await API.fetchWithAuth('employee/integrations/sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: currentIntegrationId })
                });

                if (res.status === 1) {
                    $('#sync-alert').html('<div class="alert alert-success">' + res.message + '</div>');
                    await loadConfig(); // Refresh last sync time
                } else {
                    $('#sync-alert').html('<div class="alert alert-danger">' + (res.error || 'Sync Failed') + '</div>');
                }
            } catch (err) {
                $('#sync-alert').html('<div class="alert alert-danger">' + err.message + '</div>');
            } finally {
                btn.removeAttr('disabled').html('<i class="fas fa-cloud-download-alt mr-2"></i>Start Import');
                icon.removeClass('fa-spin');
            }
        });
        // CSV File Input Name Update
        $(".custom-file-input").on("change", function () {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        // Dynamic Columns Hint
        $('#import_type').on('change', function () {
            var type = $(this).val();
            var hints = {
                'advertiser': 'email, name, company, external_id, phone, address, city, state, country, zip, skype, status',
                'affiliate': 'email, name, company, external_id, phone, address, city, state, country, zip, skype, status',
                'clicks': 'offer_id, affiliate_id, transaction_id, ip, ua, country, os, browser, brand, isp, android_id, idfa, aff_sub1...5, source, timestamp',
                'conversions': 'transaction_id, payout, revenue, timestamp, event_name'
            };
            $('#csv-columns-hint').html('<li>' + hints[type] + '</li>');
        });

        // Handle CSV Upload
        $('#frmCsvImport').on('submit', async function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            var btn = $('#btnCsvUpload');

            btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Importing...');
            $('#csv-alert').empty();

            try {
                // Use fetch directly for form data or API utility if it supports FormData
                // Assuming API.fetchWithAuth supports body as FormData if headers set correctly (auth token)
                // Need to manually handle standard fetch if API utility assumes JSON
                var token = localStorage.getItem('token'); // Assuming standard token storage

                const response = await fetch(API_BASE_URL + '/integrations/upload-csv', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    body: formData
                });

                const res = await response.json();

                if (res.status === 1) {
                    $('#csv-alert').html('<div class="alert alert-success">' + res.message + '</div>');
                    // Show errors if any
                    if (res.data.failed > 0 && res.data.errors.length > 0) {
                        var errList = '<ul>' + res.data.errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
                        $('#csv-alert').append('<div class="alert alert-warning">Some rows failed:<br>' + errList + '</div>');
                    }
                    $('#frmCsvImport')[0].reset();
                    $(".custom-file-label").html('Choose file...');
                } else {
                    $('#csv-alert').html('<div class="alert alert-danger">' + (res.error || 'Upload Failed') + '</div>');
                }
            } catch (err) {
                $('#csv-alert').html('<div class="alert alert-danger">' + err.message + '</div>');
            } finally {
                btn.removeAttr('disabled').html('<i class="fas fa-file-upload mr-2"></i>Upload & Import');
            }
        });
    </script>
</body>