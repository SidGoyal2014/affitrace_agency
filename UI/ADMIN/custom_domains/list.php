<?php
$pathPrefix = '..';
$activePage = 'custom_domains';
$pageTitle = 'Custom Domains';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header d-flex justify-content-between align-items-center">
                <h1>Custom Domains</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addDomainModal">
                    <i class="fas fa-plus mr-2"></i>Add Domain
                </button>
            </div>

            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>How it works:</strong> Register a domain, add the DNS TXT verification record,
                then verify it here. Once verified and active you can assign it to any affiliate
                tracking link from the Offer edit page.
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Registered Domains</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>Verification</th>
                                    <th>Status</th>
                                    <th>Links Using</th>
                                    <th>Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ── Add Domain Modal ──────────────────────────────────────────── -->
    <div class="modal fade" id="addDomainModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Custom Domain</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="addDomainAlert" class="d-none"></div>
                    <div class="form-group">
                        <label>Domain Name <span class="text-danger">*</span></label>
                        <input type="text" id="newDomain" class="form-control" autofocus
                               placeholder="e.g. track.mybrand.com">
                        <small class="text-muted">Enter only the domain — no https:// and no trailing slash.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addDomainBtn">Add Domain</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Verify Instructions Modal ─────────────────────────────────── -->
    <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Verify Domain: <strong id="viDomain"></strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="verifyAlert" class="d-none"></div>

                    <div style="background:#fff3cd;color:#856404;padding:12px 16px;border-radius:6px;margin-bottom:1rem;">
                        <strong>Step 1 — Add DNS TXT record:</strong>
                        Log in to your DNS provider and add the record below for this domain.
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Type</th><th>Name / Host</th><th>Value</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>TXT</code></td>
                                <td>
                                    <code id="viTxtName"></code>
                                    <button class="btn btn-sm btn-outline-secondary ml-1"
                                            onclick="copyById('viTxtName')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                                <td>
                                    <code id="viTxtValue"></code>
                                    <button class="btn btn-sm btn-outline-secondary ml-1"
                                            onclick="copyById('viTxtValue')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="background:#d1ecf1;color:#0c5460;padding:12px 16px;border-radius:6px;margin-bottom:1rem;">
                        <strong>Step 2 — Point the A record:</strong>
                        Also ensure the domain's <strong>A record</strong> points to your server's IP
                        address so that visitor traffic actually reaches this application.
                    </div>

                    <div style="background:#e2e3e5;color:#383d41;padding:12px 16px;border-radius:6px;margin-bottom:0.5rem;">
                        <i class="fas fa-clock mr-1"></i>
                        DNS changes can take a few minutes to propagate. Once the TXT record is in
                        place, click <strong>Check Verification</strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="checkVerifyBtn">
                        <i class="fas fa-check-circle mr-1"></i>Check Verification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let currentVerifyId = null;

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            await loadDomains();

            $('#addDomainBtn').on('click', addDomain);
            $('#checkVerifyBtn').on('click', checkVerification);
            $('#addDomainModal').on('shown.bs.modal', function () { $('#newDomain').trigger('focus'); });
        });

        // ── Load & render table ──────────────────────────────────────────────
        async function loadDomains() {
            try {
                const response = await API.fetchWithAuth('admin/custom-domain/list');
                const domains  = response.domains || [];
                const tbody    = $('#tableBody').empty();

                if (domains.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center py-4 text-muted">No custom domains registered yet.</td></tr>');
                    return;
                }

                domains.forEach(function (d) {
                    const verifiedBadge = d.is_verified
                        ? '<span class="badge badge-success">Verified</span>'
                        : '<span class="badge badge-warning">Unverified</span> ' +
                          '<button onclick="showVerifyInstructions(' + d.id + ', \'' + d.domain + '\', \'' + d.verification_token + '\')" ' +
                          'class="btn btn-xs btn-outline-warning ml-1" style="font-size:11px;padding:1px 6px;">Instructions</button>';

                    const activeBadge = d.is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-secondary">Inactive</span>';

                    const toggleLabel = d.is_active ? 'Deactivate' : 'Activate';
                    const toggleClass = d.is_active ? 'btn-outline-warning' : 'btn-outline-success';
                    const addedDate   = d.created_at ? d.created_at.substring(0, 10) : '—';

                    tbody.append(
                        '<tr id="domain-row-' + d.id + '">' +
                            '<td><strong>' + d.domain + '</strong></td>' +
                            '<td>' + verifiedBadge + '</td>' +
                            '<td>' + activeBadge + '</td>' +
                            '<td>' + (d.offer_affiliates_count || 0) + ' link(s)</td>' +
                            '<td>' + addedDate + '</td>' +
                            '<td>' +
                                '<button onclick="toggleDomain(' + d.id + ')" class="btn btn-sm ' + toggleClass + ' mr-1">' + toggleLabel + '</button>' +
                                '<button onclick="deleteDomain(' + d.id + ')" class="btn btn-sm btn-outline-danger">Delete</button>' +
                            '</td>' +
                        '</tr>'
                    );
                });

                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().destroy();
                }
                $('#dataTable').DataTable({ pageLength: 25, order: [[4, 'desc']] });

            } catch (e) {
                Toast.error('Error', 'Failed to load domains: ' + e.message);
            }
        }

        // ── Add domain ───────────────────────────────────────────────────────
        async function addDomain() {
            const domain = $('#newDomain').val().trim();
            if (!domain) {
                showModalAlert('addDomainAlert', 'danger', 'Please enter a domain name.');
                return;
            }

            $('#addDomainBtn').prop('disabled', true).text('Adding...');
            try {
                const response = await API.fetchWithAuth('admin/custom-domain/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domain: domain })
                });

                if (response.status === 1) {
                    $('#addDomainModal').modal('hide');
                    $('#newDomain').val('');
                    await loadDomains();
                    Toast.success('Domain Added', 'Now complete DNS verification.');
                    showVerifyInstructions(
                        response.domain.id,
                        response.domain.domain,
                        response.domain.verification_token
                    );
                } else {
                    showModalAlert('addDomainAlert', 'danger', response.error || 'Failed to add domain.');
                }
            } catch (e) {
                showModalAlert('addDomainAlert', 'danger', e.message);
            } finally {
                $('#addDomainBtn').prop('disabled', false).text('Add Domain');
            }
        }

        // ── Verify instructions ──────────────────────────────────────────────
        function showVerifyInstructions(id, domain, token) {
            currentVerifyId = id;
            $('#viDomain').text(domain);
            $('#viTxtName').text('_affitrace-verify.' + domain);
            $('#viTxtValue').text(token);
            $('#verifyAlert').addClass('d-none').text('').removeClass('alert alert-success alert-danger');
            $('#verifyModal').modal('show');
        }

        async function checkVerification() {
            if (!currentVerifyId) return;
            const btn = $('#checkVerifyBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Checking...');

            try {
                const response = await API.fetchWithAuth('admin/custom-domain/' + currentVerifyId + '/verify', {
                    method: 'POST'
                });

                if (response.status === 1) {
                    showModalAlert('verifyAlert', 'success', response.msg || 'Domain verified successfully!');
                    setTimeout(async function () {
                        $('#verifyModal').modal('hide');
                        await loadDomains();
                    }, 1500);
                } else {
                    showModalAlert('verifyAlert', 'danger', response.error || 'Verification failed. Check the DNS record and try again.');
                }
            } catch (e) {
                const msg = (e.responseData && (e.responseData.error || e.responseData.message)) || e.message;
                showModalAlert('verifyAlert', 'danger', msg);
            } finally {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i>Check Verification');
            }
        }

        // ── Toggle active ────────────────────────────────────────────────────
        async function toggleDomain(id) {
            try {
                const response = await API.fetchWithAuth('admin/custom-domain/' + id + '/toggle', {
                    method: 'PATCH'
                });
                if (response.status === 1) {
                    Toast.success('Updated', response.msg);
                    await loadDomains();
                } else {
                    Toast.error('Error', response.error || 'Failed to update domain.');
                }
            } catch (e) {
                Toast.error('Error', e.message);
            }
        }

        // ── Delete ───────────────────────────────────────────────────────────
        async function deleteDomain(id) {
            if (!confirm('Delete this domain? This cannot be undone.')) return;
            try {
                const response = await API.fetchWithAuth('admin/custom-domain/' + id, {
                    method: 'DELETE'
                });
                if (response.status === 1) {
                    Toast.success('Deleted', 'Domain removed.');
                    await loadDomains();
                } else {
                    Toast.error('Cannot Delete', response.error || 'Failed to delete domain.');
                }
            } catch (e) {
                Toast.error('Error', e.message);
            }
        }

        // ── Helpers ──────────────────────────────────────────────────────────
        function copyById(elementId) {
            const text = document.getElementById(elementId).textContent;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    Toast.success('Copied', 'Copied to clipboard.');
                });
            } else {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                Toast.success('Copied', 'Copied to clipboard.');
            }
        }

        function showModalAlert(containerId, type, message) {
            const el = $('#' + containerId);
            el.removeClass('d-none alert-success alert-danger alert-warning alert-info')
              .addClass('alert alert-' + type)
              .text(message);
        }
    </script>
</body>
</html>