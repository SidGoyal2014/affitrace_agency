<?php include $pathPrefix . '/includes/head.php'; ?>

<body id="page-top">

<?php include $pathPrefix . '/includes/topnav.php'; ?>

<main id="main-content">
    <div class="container-fluid">

        <div class="page-header">
            <h1>Redirect Checker</h1>
            <p class="text-muted">Trace all redirect hops for any URL with custom proxy and device simulation.</p>
        </div>

        <!-- Check Form -->
        <div class="card-modern mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Run a New Check</h6>
            </div>
            <div class="card-body">
                <form id="checkForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>URL <span class="text-danger">*</span></label>
                                        <input type="url" id="inputUrl" class="form-control" placeholder="https://example.com/tracking-link" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Device</label>
                                        <select id="inputDevice" class="form-control">
                                            <option value="desktop_windows">Windows Desktop</option>
                                            <option value="desktop_mac">macOS Desktop</option>
                                            <option value="mobile_ios">iPhone / iOS</option>
                                            <option value="mobile_android">Android Phone</option>
                                            <option value="tablet_ios">iPad / iOS Tablet</option>
                                            <option value="tablet_android">Android Tablet</option>
                                            <option value="bot_googlebot">Googlebot</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group w-100">
                                        <button type="submit" class="btn btn-primary btn-block" id="traceBtn">
                                            <span id="traceLabel">Trace</span>
                                            <span id="traceSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Proxy Server <span class="text-muted small">(Optional)</span></label>
                                        <div class="input-group">
                                            <input type="text" id="inputProxy" class="form-control"
                                                placeholder="host:port:user:pass">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" id="testProxyBtn" title="Test Proxy">
                                                    <span id="testProxyLabel">🚦 Test Proxy</span>
                                                    <span id="testProxySpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Format: <code>host:port:user:pass</code> &nbsp;or&nbsp; <code>http://user:pass@host:port</code></small>
                                    </div>
                                    <!-- Proxy test result -->
                                    <div id="proxyTestResult" class="d-none mt-1 p-2 rounded border" style="font-size:0.82rem;background:#f8f9fc;">
                                        <div id="proxyTestSuccess" class="d-none">
                                            <p class="mb-1">🌐 <strong>IP:</strong> <span id="ptIp"></span></p>
                                            <p class="mb-1">🏳️ <strong>Country:</strong> <span id="ptCountry"></span> (<span id="ptCountryCode"></span>)</p>
                                            <p class="mb-1">🏙️ <strong>City:</strong> <span id="ptCity"></span></p>
                                            <p class="mb-1">🏢 <strong>ISP:</strong> <span id="ptIsp"></span></p>
                                            <p class="mb-0 text-success font-weight-bold" id="ptTrafficMsg"></p>
                                        </div>
                                        <div id="proxyTestError" class="text-danger d-none"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </row>
                    <?php
                    /*
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>URL <span class="text-danger">*</span></label>
                                <input type="url" id="inputUrl" class="form-control" placeholder="https://example.com/tracking-link" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Device</label>
                                <select id="inputDevice" class="form-control">
                                    <option value="desktop_windows">Windows Desktop</option>
                                    <option value="desktop_mac">macOS Desktop</option>
                                    <option value="mobile_ios">iPhone / iOS</option>
                                    <option value="mobile_android">Android Phone</option>
                                    <option value="tablet_ios">iPad / iOS Tablet</option>
                                    <option value="tablet_android">Android Tablet</option>
                                    <option value="bot_googlebot">Googlebot</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Proxy Server <span class="text-muted small">(Optional)</span></label>
                                <div class="input-group">
                                    <input type="text" id="inputProxy" class="form-control"
                                        placeholder="host:port:user:pass">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="testProxyBtn" title="Test Proxy">
                                            <span id="testProxyLabel">🚦 Test Proxy</span>
                                            <span id="testProxySpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Format: <code>host:port:user:pass</code> &nbsp;or&nbsp; <code>http://user:pass@host:port</code></small>
                            </div>
                            <!-- Proxy test result -->
                            <div id="proxyTestResult" class="d-none mt-1 p-2 rounded border" style="font-size:0.82rem;background:#f8f9fc;">
                                <div id="proxyTestSuccess" class="d-none">
                                    <p class="mb-1">🌐 <strong>IP:</strong> <span id="ptIp"></span></p>
                                    <p class="mb-1">🏳️ <strong>Country:</strong> <span id="ptCountry"></span> (<span id="ptCountryCode"></span>)</p>
                                    <p class="mb-1">🏙️ <strong>City:</strong> <span id="ptCity"></span></p>
                                    <p class="mb-1">🏢 <strong>ISP:</strong> <span id="ptIsp"></span></p>
                                    <p class="mb-0 text-success font-weight-bold" id="ptTrafficMsg"></p>
                                </div>
                                <div id="proxyTestError" class="text-danger d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-start pt-4 mt-1">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-block" id="traceBtn">
                                    <span id="traceLabel">Trace</span>
                                    <span id="traceSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    */?>
                </form>
            </div>
        </div>

        <!-- Result Banner (hidden until a trace runs) -->
        <div id="resultBanner" class="card-modern mb-4 d-none">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center">
                        <span id="resultIcon" class="fa-stack fa-2x">
                            <i class="fas fa-circle fa-stack-2x text-success"></i>
                            <i class="fas fa-check fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <div class="col-md-8">
                        <p class="mb-1"><strong>Input URL:</strong> <span id="resultInputUrl" class="text-muted small"></span></p>
                        <p class="mb-1"><strong>Final Destination:</strong> <a id="resultFinalUrl" href="#" target="_blank" class="text-break"></a></p>
                        <p class="mb-0"><strong>Total Hops:</strong> <span id="resultHopCount" class="badge badge-info"></span> &nbsp; <strong>Device:</strong> <span id="resultDevice" class="badge badge-secondary"></span></p>
                    </div>
                    <div class="col-md-3 text-right">
                        <button class="btn btn-outline-primary btn-sm" id="viewLatestDetailBtn">
                            <i class="fas fa-list"></i> View All Hops
                        </button>
                    </div>
                </div>
                <div id="resultError" class="alert alert-danger mt-2 d-none"></div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card-modern">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Check History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-flush mb-0" id="historyTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Checked On</th>
                                <th>URL</th>
                                <th>Final Destination</th>
                                <th>Hops</th>
                                <th>Device</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
                            <tr><td colspan="8" class="text-center py-4 text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Hop Detail Modal -->
<div class="modal fade" id="hopDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Redirect Hop Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="modalMeta" class="mb-3 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Input URL:</strong> <span id="modalInputUrl" class="text-break small"></span></p>
                            <p class="mb-0"><strong>Final Destination:</strong> <a id="modalFinalUrl" href="#" target="_blank" class="text-break small"></a></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Device:</strong> <span id="modalDevice"></span></p>
                            <p class="mb-1"><strong>Checked On:</strong> <span id="modalCheckedOn"></span></p>
                            <p class="mb-0"><strong>Proxy:</strong> <span id="modalProxy" class="text-monospace small"></span></p>
                        </div>
                    </div>
                </div>

                <div id="modalHopsContainer">
                    <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<style>
/* ── Hop Detail Modal — Theme Alignment ──────────────────────────────────── */

#hopDetailModal .modal-content {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    font-family: var(--font-sans);
}

#hopDetailModal .modal-header {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-main) 100%);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    border-bottom: none;
    padding: 1.1rem 1.5rem;
}

#hopDetailModal .modal-title {
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.01em;
}

#hopDetailModal .modal-header .close {
    color: rgba(255,255,255,0.8);
    text-shadow: none;
    opacity: 1;
}
#hopDetailModal .modal-header .close:hover { color: #fff; }

#hopDetailModal .modal-footer {
    border-top: 1px solid var(--border-light);
    padding: 0.75rem 1.5rem;
}

/* ── Meta block ──────────────────────────────────────────────────────────── */
#modalMeta {
    background: var(--bg-body) !important;   /* replaces Bootstrap bg-light */
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    font-size: 0.875rem;
}
#modalMeta strong { color: var(--text-primary); }
#modalMeta span, #modalMeta a { color: var(--text-secondary); }

/* ── Proxy badge in meta ─────────────────────────────────────────────────── */
#modalProxy .badge {
    background: rgba(15,23,42,0.08);
    color: var(--text-primary);
    font-weight: 500;
    border-radius: var(--radius-full);
    font-size: 0.78rem;
    letter-spacing: 0.02em;
}

/* ── Hops table ──────────────────────────────────────────────────────────── */
#modalHopsContainer .table {
    font-size: 0.825rem;
    border-color: var(--border-light);
}
#modalHopsContainer .table thead th {
    background: var(--bg-body);
    border-bottom: 2px solid var(--border-light);
    font-weight: 600;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    padding: 0.6rem 0.75rem;
}
#modalHopsContainer .table tbody td {
    border-color: var(--border-light);
    color: var(--text-secondary);
    vertical-align: middle;
    padding: 0.55rem 0.75rem;
}
#modalHopsContainer .table tbody tr:hover {
    background: rgba(99,102,241,0.03);
}

/* ── Status code badges ──────────────────────────────────────────────────── */
#modalHopsContainer .badge-success {
    background: var(--success-light);
    color: #047857;
}
#modalHopsContainer .badge-warning {
    background: var(--warning-light);
    color: #B45309;
}
#modalHopsContainer .badge-danger {
    background: var(--error-light);
    color: #B91C1C;
}
#modalHopsContainer .badge-secondary {
    background: #E2E8F0;
    color: #475569;
}

/* ── Final destination row highlight ─────────────────────────────────────── */
#modalHopsContainer .table-success,
#modalHopsContainer .table-success > td {
    background-color: var(--success-light) !important;
}

/* ── Time (ms) colour coding — aligned to theme palette ─────────────────── */
#modalHopsContainer .text-success  { color: #047857 !important; }   /* < 800ms  */
#modalHopsContainer .text-warning  { color: #B45309 !important; }   /* < 2000ms */
#modalHopsContainer .text-danger   { color: #B91C1C !important; }   /* ≥ 2000ms */
</style>
<script>
$(document).ready(async function () {
    if (!API.isAuthenticated()) { window.location.href = '<?php echo $pathPrefix; ?>/login.php'; return; }

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user) $('.user-name').text(user.name);

    const userType = '<?php echo $user_type; ?>';
    let latestCheckId = null;
    let historyDT = null;

    // ── Load history ──────────────────────────────────────────────────────────
    async function loadHistory() {
        try {
            const res = await API.fetchWithAuth(userType + '/redirect-checker?per_page=50');
            const rows = (res.data && res.data.data) ? res.data.data : [];
            const tbody = $('#historyBody');
            tbody.empty();

            if (rows.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No checks yet. Run your first trace above.</td></tr>');
                return;
            }

            rows.forEach(row => {
                const statusBadge = row.status === 'completed'
                    ? `<span class="badge badge-success">Completed</span>`
                    : row.status === 'failed'
                        ? `<span class="badge badge-danger">Failed</span>`
                        : `<span class="badge badge-warning">Pending</span>`;

                const finalUrlDisplay = row.final_url
                    ? `<a href="${escHtml(row.final_url)}" target="_blank" class="text-break small" title="${escHtml(row.final_url)}">${truncate(row.final_url, 60)}</a>`
                    : '<span class="text-muted">—</span>';

                tbody.append(`
                    <tr>
                        <td>${row.id}</td>
                        <td class="small">${row.checked_on || '—'}</td>
                        <td class="small text-break" style="max-width:220px" title="${escHtml(row.url)}">${truncate(row.url, 55)}</td>
                        <td style="max-width:220px">${finalUrlDisplay}</td>
                        <td><span class="badge badge-info">${row.hops}</span></td>
                        <td class="small">${row.device || '—'}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-xs btn-outline-primary btn-view-detail" data-id="${row.id}" title="View Hops">
                                <i class="fas fa-route"></i> View Details
                            </button>
                            <button class="btn btn-xs btn-outline-danger btn-delete" data-id="${row.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            if (historyDT) { historyDT.destroy(); }
            historyDT = $('#historyTable').DataTable({ pageLength: 25, order: [[0, 'desc']] });

        } catch (err) {
            console.error(err);
            $('#historyBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load history.</td></tr>');
        }
    }

    await loadHistory();

    // ── Submit check form ─────────────────────────────────────────────────────
    $('#checkForm').on('submit', async function (e) {
        e.preventDefault();

        const url    = $('#inputUrl').val().trim();
        const device = $('#inputDevice').val();
        const proxy  = $('#inputProxy').val().trim();

        if (!url) return;

        $('#traceBtn').prop('disabled', true);
        $('#traceLabel').text('Tracing...');
        $('#traceSpinner').removeClass('d-none');
        $('#resultBanner').addClass('d-none');

        try {
            const payload = { url, device };
            if (proxy) payload.proxy = proxy;

            const res = await API.fetchWithAuth(userType + '/redirect-checker/check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const data = res.data;
            latestCheckId = data.id;

            $('#resultInputUrl').text(data.input_url);
            $('#resultFinalUrl').text(truncate(data.final_url || '—', 80)).attr('href', data.final_url || '#');
            $('#resultHopCount').text(data.hop_count + ' hop' + (data.hop_count !== 1 ? 's' : ''));
            $('#resultDevice').text(data.device_label);
            $('#resultBanner').removeClass('d-none');

            if (data.status === 'failed') {
                $('#resultIcon').html('<i class="fas fa-circle fa-stack-2x text-danger"></i><i class="fas fa-times fa-stack-1x fa-inverse"></i>');
                $('#resultError').text(data.error_message || 'Trace failed.').removeClass('d-none');
            } else {
                $('#resultIcon').html('<i class="fas fa-circle fa-stack-2x text-success"></i><i class="fas fa-check fa-stack-1x fa-inverse"></i>');
                $('#resultError').addClass('d-none');
            }

            if (historyDT) { historyDT.destroy(); historyDT = null; }
            await loadHistory();

        } catch (err) {
            console.error(err);
            Toast.error('Trace request failed. Check console for details.');
        } finally {
            $('#traceBtn').prop('disabled', false);
            $('#traceLabel').text('Trace');
            $('#traceSpinner').addClass('d-none');
        }
    });

    // ── View latest detail (from result banner) ───────────────────────────────
    $('#viewLatestDetailBtn').on('click', function () {
        if (latestCheckId) openDetailModal(latestCheckId);
    });

    // ── Test Proxy ────────────────────────────────────────────────────────────
    $('#testProxyBtn').on('click', async function () {
        const proxy = $('#inputProxy').val().trim();
        if (!proxy) {
            Toast.error('Enter a proxy address first.');
            return;
        }

        $('#testProxyBtn').prop('disabled', true);
        $('#testProxyLabel').text('Testing...');
        $('#testProxySpinner').removeClass('d-none');
        $('#proxyTestResult').removeClass('d-none');
        $('#proxyTestSuccess').addClass('d-none');
        $('#proxyTestError').addClass('d-none').text('');

        try {
            const res = await API.fetchWithAuth(userType + '/redirect-checker/test-proxy', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ proxy }),
            });

            if (res.success && res.data) {
                const d = res.data;
                $('#ptIp').text(d.ip || '—');
                $('#ptCountry').text(d.country || '—');
                $('#ptCountryCode').text(d.country_code || '—');
                $('#ptCity').text(d.city || '—');
                $('#ptIsp').text(d.isp || '—');
                $('#ptTrafficMsg').text('Traffic successfully routed via ' + (d.country || 'proxy') + '!');
                $('#proxyTestSuccess').removeClass('d-none');
            } else {
                $('#proxyTestError').text(res.message || 'Proxy test failed.').removeClass('d-none');
            }
        } catch (err) {
            $('#proxyTestError').text('Proxy test failed: ' + (err.message || 'Unknown error')).removeClass('d-none');
        } finally {
            $('#testProxyBtn').prop('disabled', false);
            $('#testProxyLabel').text('🚦 Test Proxy');
            $('#testProxySpinner').addClass('d-none');
        }
    });



    // ── View detail from table row ────────────────────────────────────────────
    $(document).on('click', '.btn-view-detail', function () {
        const id = $(this).data('id');
        openDetailModal(id);
    });

    // ── Delete from table row ─────────────────────────────────────────────────
    $(document).on('click', '.btn-delete', async function () {
        const id = $(this).data('id');
        if (!confirm('Delete this check record?')) return;
        try {
            await API.fetchWithAuth(userType + '/redirect-checker/' + id, { method: 'DELETE' });
            Toast.success('Deleted.');
            if (historyDT) { historyDT.destroy(); historyDT = null; }
            await loadHistory();
        } catch (err) {
            Toast.error('Delete failed.');
        }
    });

    // ── Open detail modal ─────────────────────────────────────────────────────
    async function openDetailModal(id) {
        $('#hopDetailModal').modal('show');
        $('#modalHopsContainer').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');

        try {
            const res = await API.fetchWithAuth(userType + '/redirect-checker/' + id);
            const data = res.data;

            $('#modalInputUrl').text(data.input_url);
            $('#modalFinalUrl').text(data.final_url || '—').attr('href', data.final_url || '#');
            $('#modalDevice').text(data.device_label);
            $('#modalCheckedOn').text(data.checked_on);
            $('#modalProxy').html(
                data.proxy_display
                    ? `<span class="badge badge-dark">${escHtml(data.proxy_display)}</span>`
                    : '<span class="text-muted">None (direct connection)</span>'
            );

            const hops = data.hops || [];
            if (hops.length === 0) {
                $('#modalHopsContainer').html('<p class="text-muted text-center">No hop data available.</p>');
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:60px">Hop #</th>
                                <th>URL</th>
                                <th style="width:110px">Status</th>
                                <th style="width:100px">Time (ms)</th>
                                <th>Redirects To</th>
                            </tr>
                        </thead>
                        <tbody>`;

            hops.forEach(hop => {
                const isLast    = !hop.redirect_to;
                const statusCls = hop.status_code >= 200 && hop.status_code < 300
                    ? 'badge-success'
                    : hop.status_code >= 300 && hop.status_code < 400
                        ? 'badge-warning'
                        : hop.status_code >= 400
                            ? 'badge-danger'
                            : 'badge-secondary';

                const timeCls = hop.time_ms > 2000
                    ? 'text-danger font-weight-bold'
                    : hop.time_ms > 800
                        ? 'text-warning font-weight-bold'
                        : 'text-success';

                const redirectCell = hop.redirect_to
                    ? `<a href="${escHtml(hop.redirect_to)}" target="_blank" class="text-break small">${escHtml(hop.redirect_to)}</a>`
                    : (isLast ? '<span class="badge badge-success">Final Destination</span>' : '<span class="text-muted">—</span>');

                const errorNote = hop.error ? `<br><small class="text-danger">${escHtml(hop.error)}</small>` : '';

                const timeDisplay = hop.time_ms != null
                    ? `<span>${hop.time_ms} ms</span>`
                    : '<span class="text-muted">—</span>';

                html += `
                    <tr>
                        <td class="text-center">${hop.hop}</td>
                        <td class="small text-break">${escHtml(hop.url)}${errorNote}</td>
                        <td class="text-center"><span class="badge ${statusCls}">${hop.status_code || 'N/A'}</span></td>
                        <td class="text-center">${timeDisplay}</td>
                        <td>${redirectCell}</td>
                    </tr>`;
            });

            html += '</tbody></table></div>';
            $('#modalHopsContainer').html(html);

        } catch (err) {
            $('#modalHopsContainer').html('<p class="text-danger text-center">Failed to load hop details.</p>');
        }
    }


    // ── Helpers ───────────────────────────────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function truncate(str, len) {
        if (!str) return '—';
        return str.length > len ? str.substring(0, len) + '…' : str;
    }
});
</script>

</body>
</html>