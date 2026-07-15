<?php
$pathPrefix = '..';
$activePage  = 'tools';
$pageTitle   = 'Postback Simulator';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';


?>

<div class="container-fluid">

  <!-- Page Heading -->
  <div class="page-header">
    <h1>Postback Simulator</h1>
    <small class="text-muted">Test advertiser &amp; affiliate postback flows</small>
  </div>

  <!-- Nav Tabs -->
  <ul class="nav nav-tabs mb-4" id="simTabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="tab-adv" data-toggle="tab" href="#panel-adv" role="tab">
        <i class="fas fa-arrow-right mr-1"></i> Advertiser → Network
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="tab-aff" data-toggle="tab" href="#panel-aff" role="tab">
        <i class="fas fa-arrow-left mr-1"></i> Network → Affiliate
      </a>
    </li>
  </ul>

  <div class="tab-content" id="simTabContent">

    <!-- ══════════════════════════════════════════════════════════════════════
         TAB 1 — Advertiser → Network
    ══════════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade show active" id="panel-adv" role="tabpanel">
      <div class="row">

        <!-- Left: Form -->
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
              <h6 class="m-0">
                Fire Advertiser Postback <small class="ml-2 font-weight-normal opacity-75">→ GET /api/offer/sd</small>
              </h6>
            </div>
            <div class="card-body">

              <!-- Quick TID lookup -->
              <div class="form-group">
                <label>Quick TID Lookup <small class="text-muted">(recent clicks)</small></label>
                <select id="clickLookup" class="form-control select2-no-init">
                  <option value="">— select a recent click to pre-fill TID —</option>
                </select>
              </div>
              <hr>

              <div id="advAlert"></div>

              <form id="advForm">
                <div class="form-group">
                  <label>aff_click_id / TID <span class="text-danger">*</span></label>
                  <input type="text" id="aff_click_id" class="form-control" placeholder="e.g. abc123xyz" required>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label>event_id</label>
                    <input type="text" id="event_id" class="form-control" placeholder="e.g. install">
                  </div>
                  <div class="form-group col-md-6">
                    <label>sale (revenue amount)</label>
                    <input type="number" id="sale" class="form-control" placeholder="e.g. 9.99" step="0.01" min="0">
                  </div>
                </div>
                <div class="form-group">
                  <label>currency</label>
                  <input type="text" id="currency" class="form-control" placeholder="e.g. USD" maxlength="10">
                </div>
                <div class="form-group">
                  <label>status</label>
                  <select id="status" class="form-control">
                    <option value="">— not set —</option>
                    <option value="approved">approved</option>
                    <option value="pending">pending</option>
                    <option value="rejected">rejected</option>
                  </select>
                </div>

                <!-- adv_sub params (collapsible) -->
                <div class="mb-2">
                  <a href="#advSubCollapse" data-toggle="collapse" class="text-sm text-primary">
                    <i class="fas fa-chevron-down mr-1"></i>adv_sub1 – adv_sub5 (optional)
                  </a>
                </div>
                <div class="collapse" id="advSubCollapse">
                  <div class="form-row">
                    <?php foreach ([1,2,3,4,5] as $n): ?>
                    <div class="form-group col-md-6">
                      <label>adv_sub<?= $n ?></label>
                      <input type="text" id="adv_sub<?= $n ?>" class="form-control" placeholder="adv_sub<?= $n ?>">
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3" id="advFireBtn">
                  <i class="fas fa-paper-plane mr-1"></i> Fire Postback
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Right: Result -->
        <div class="col-lg-6">
          <div class="card shadow mb-4" id="advResultCard" style="display:none;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" id="advResultHeader">
              <h6 class="m-0 font-weight-bold">Result</h6>
              <span id="advResultBadge" class="badge"></span>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="font-weight-bold">Request URL</label>
                <div class="bg-light border rounded p-2 small" style="word-break:break-all;" id="advReqUrl"></div>
              </div>
              <div class="mb-3">
                <label class="font-weight-bold">HTTP Status</label>
                <span id="advHttpStatus" class="badge badge-secondary ml-2"></span>
                <span id="advDuration" class="text-muted small ml-2"></span>
              </div>
              <div>
                <label class="font-weight-bold">Response</label>
                <pre id="advResponseBody" class="bg-dark text-success p-3 rounded small" style="max-height:300px;overflow:auto;white-space:pre-wrap;"></pre>
              </div>
            </div>
          </div>
          <!-- Placeholder when no result yet -->
          <div id="advPlaceholder" class="text-center text-muted mt-5 pt-5">
            <i class="fas fa-satellite-dish fa-3x mb-3 text-gray-300"></i>
            <p>Fill the form and click <strong>Fire Postback</strong> to see results here.</p>
          </div>
        </div>

      </div>
    </div><!-- /panel-adv -->

    <!-- ══════════════════════════════════════════════════════════════════════
         TAB 2 — Network → Affiliate
    ══════════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade" id="panel-aff" role="tabpanel">
      <div class="row">

        <!-- Left: Form -->
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
              <h6 class="m-0">Fire Affiliate Postback</h6>
            </div>
            <div class="card-body">

              <div id="affAlert"></div>

              <form id="affForm">
                <div class="form-group">
                  <label>Select Conversion <span class="text-danger">*</span></label>
                  <select id="conversionSelect" class="form-control" required>
                    <option value="">— loading conversions... —</option>
                  </select>
                  <small class="text-muted">Shows last 50 conversions</small>
                </div>

                <!-- Conversion details (pre-filled when selected) -->
                <div id="convDetails" class="alert alert-light border small mt-2" style="display:none;">
                  <table class="table table-sm table-borderless mb-0">
                    <tr><td class="font-weight-bold">TID</td><td id="convTid">—</td></tr>
                    <tr><td class="font-weight-bold">Offer</td><td id="convOffer">—</td></tr>
                    <tr><td class="font-weight-bold">Affiliate</td><td id="convAffiliate">—</td></tr>
                    <tr><td class="font-weight-bold">Event</td><td id="convEvent">—</td></tr>
                    <tr><td class="font-weight-bold">Status</td><td id="convStatus">—</td></tr>
                    <tr><td class="font-weight-bold">Payout</td><td id="convPayout">—</td></tr>
                  </table>
                </div>

                <div class="form-group mt-3">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="dryRun">
                    <label class="custom-control-label" for="dryRun">
                      Dry Run <small class="text-muted">(build URL but do NOT fire it)</small>
                    </label>
                  </div>
                </div>

                <button type="submit" class="btn btn-success btn-block mt-3" id="affFireBtn">
                  <i class="fas fa-paper-plane mr-1"></i> Fire Affiliate Postback
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Right: Result -->
        <div class="col-lg-6">
          <div class="card shadow mb-4" id="affResultCard" style="display:none;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" id="affResultHeader">
              <h6 class="m-0 font-weight-bold">Result</h6>
              <span id="affResultBadge" class="badge"></span>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="font-weight-bold">Template URL <small class="text-muted">(from affiliate config)</small></label>
                <div class="bg-light border rounded p-2 small" style="word-break:break-all;" id="affTemplateUrl"></div>
              </div>
              <div class="mb-3">
                <label class="font-weight-bold">Built URL <small class="text-muted">(macros replaced)</small></label>
                <div class="bg-light border rounded p-2 small" style="word-break:break-all;" id="affBuiltUrl"></div>
              </div>
              <div id="affHttpRow" class="mb-3" style="display:none;">
                <label class="font-weight-bold">HTTP Status</label>
                <span id="affHttpStatus" class="badge badge-secondary ml-2"></span>
                <span id="affDuration" class="text-muted small ml-2"></span>
              </div>
              <div id="affResponseRow" style="display:none;">
                <label class="font-weight-bold">Response Body</label>
                <pre id="affResponseBody" class="bg-dark text-success p-3 rounded small" style="max-height:300px;overflow:auto;white-space:pre-wrap;"></pre>
              </div>
              <div id="affDryRunNote" class="alert alert-warning mt-2" style="display:none;">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Dry Run:</strong> URL built but NOT fired. No log entry created.
              </div>
            </div>
          </div>
          <div id="affPlaceholder" class="text-center text-muted mt-5 pt-5">
            <i class="fas fa-satellite-dish fa-3x mb-3 text-gray-300"></i>
            <p>Select a conversion and click <strong>Fire Affiliate Postback</strong>.</p>
          </div>
        </div>

      </div>
    </div><!-- /panel-aff -->

  </div><!-- /tab-content -->

</div><!-- /container-fluid -->

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Postback Simulator JS
// ═══════════════════════════════════════════════════════════════════════════

const API_BASE = 'admin/tools/postback-simulator';

// ── Helpers ──────────────────────────────────────────────────────────────

function showAlert(containerId, msg, type = 'danger') {
    $(`#${containerId}`).html(
        `<div class="alert alert-${type} alert-dismissible fade show">
            ${msg}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>`
    );
}

function httpStatusBadge(code) {
    if (code >= 200 && code < 300) return `<span class="badge badge-success">${code}</span>`;
    if (code >= 400 && code < 500) return `<span class="badge badge-warning">${code}</span>`;
    if (code >= 500) return `<span class="badge badge-danger">${code}</span>`;
    return `<span class="badge badge-secondary">${code || '?'}</span>`;
}

// ── Tab 1: Advertiser → Network ─────────────────────────────────────────

// Load recent clicks for quick lookup
API.fetchWithAuth(`${API_BASE}/recent-clicks`)
    .then(res => {
        if (res.status !== 1) return;
        const sel = $('#clickLookup');
        sel.empty().append('<option value="">— select a recent click to pre-fill TID —</option>');
        res.data.forEach(c => {
            sel.append(`<option value="${c.tid}" data-offer="${c.offer_name || ''}">${c.tid} | ${c.offer_name || 'Offer #'+c.offer_id} | ${c.country || '?'} | ${c.created_at}</option>`);
        });
    });

$('#clickLookup').on('change', function () {
    const tid = $(this).val();
    if (tid) $('#aff_click_id').val(tid);
});

$('#advForm').on('submit', async function (e) {
    e.preventDefault();
    $('#advAlert').html('');
    const btn = $('#advFireBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Firing...');

    const payload = {
        aff_click_id: $('#aff_click_id').val().trim(),
        event_id:     $('#event_id').val().trim()   || null,
        sale:         $('#sale').val()               || null,
        currency:     $('#currency').val().trim()    || null,
        status:       $('#status').val()             || null,
        adv_sub1:     $('#adv_sub1').val().trim()    || null,
        adv_sub2:     $('#adv_sub2').val().trim()    || null,
        adv_sub3:     $('#adv_sub3').val().trim()    || null,
        adv_sub4:     $('#adv_sub4').val().trim()    || null,
        adv_sub5:     $('#adv_sub5').val().trim()    || null,
    };

    // Remove null values
    Object.keys(payload).forEach(k => payload[k] === null && delete payload[k]);

    try {
        const json = await API.fetchWithAuth(`${API_BASE}/fire-advertiser-postback`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const d = json.data || {};

        $('#advPlaceholder').hide();
        $('#advResultCard').show();

        if (json.status === 1 && d.success) {
            $('#advResultHeader').removeClass('bg-danger').addClass('bg-success');
            $('#advResultBadge').attr('class', 'badge badge-light').text('SUCCESS');
        } else {
            $('#advResultHeader').removeClass('bg-success').addClass('bg-danger');
            $('#advResultBadge').attr('class', 'badge badge-light').text('FAILED');
        }

        $('#advReqUrl').text(d.request_url || '—');
        $('#advHttpStatus').html(httpStatusBadge(d.http_status));
        $('#advDuration').text(d.duration_ms ? `${d.duration_ms}ms` : '');

        const body = d.response_json
            ? JSON.stringify(d.response_json, null, 2)
            : (d.response_body || d.error || '(no response)');
        $('#advResponseBody').text(body);

    } catch (err) {
        showAlert('advAlert', 'Request failed: ' + err.message);
    } finally {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Fire Postback');
    }
});

// ── Tab 2: Network → Affiliate ──────────────────────────────────────────

let conversionsData = [];

API.fetchWithAuth(`${API_BASE}/recent-conversions`)
    .then(res => {
        if (res.status !== 1) return;
        conversionsData = res.data;
        const sel = $('#conversionSelect');
        sel.empty().append('<option value="">— select a conversion —</option>');
        res.data.forEach(c => {
            sel.append(`<option value="${c.id}">#${c.id} | ${c.offer_name || '?'} | ${c.affiliate_name || 'Aff#?'} | ${c.event || 'conversion'} | ${c.status} | ${c.payout} ${c.currency}</option>`);
        });
    });

$('#conversionSelect').on('change', function () {
    const id = parseInt($(this).val());
    const c = conversionsData.find(x => x.id === id);
    if (c) {
        $('#convTid').text(c.tid || '—');
        $('#convOffer').text(c.offer_name || '—');
        $('#convAffiliate').text(c.affiliate_name || '—');
        $('#convEvent').text(c.event || '—');
        $('#convStatus').text(c.status || '—');
        $('#convPayout').text(`${c.payout || 0} ${c.currency || ''}`);
        $('#convDetails').show();
    } else {
        $('#convDetails').hide();
    }
});

$('#affForm').on('submit', async function (e) {
    e.preventDefault();
    $('#affAlert').html('');
    const convId = $('#conversionSelect').val();
    if (!convId) { showAlert('affAlert', 'Please select a conversion.', 'warning'); return; }

    const dryRun = $('#dryRun').is(':checked');
    const btn = $('#affFireBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

    try {
        const json = await API.fetchWithAuth(`${API_BASE}/fire-affiliate-postback`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conversion_id: parseInt(convId), dry_run: dryRun }),
        });
        const d = json.data || {};

        $('#affPlaceholder').hide();
        $('#affResultCard').show();

        if (json.status === 1 && d.success) {
            $('#affResultHeader').removeClass('bg-danger').addClass(dryRun ? 'bg-warning' : 'bg-success');
            $('#affResultBadge').attr('class', 'badge badge-dark').text(dryRun ? 'DRY RUN' : 'FIRED');
        } else {
            $('#affResultHeader').removeClass('bg-success bg-warning').addClass('bg-danger');
            $('#affResultBadge').attr('class', 'badge badge-light').text('FAILED');
        }

        $('#affTemplateUrl').text(d.template_url || '— no template configured —');
        $('#affBuiltUrl').text(d.built_url || d.error || '—');

        if (!dryRun && d.http_status !== undefined) {
            $('#affHttpRow').show();
            $('#affHttpStatus').html(httpStatusBadge(d.http_status));
            $('#affDuration').text(d.duration_ms ? `${d.duration_ms}ms` : '');
            $('#affResponseRow').show();
            $('#affResponseBody').text(d.response_body || '(empty response)');
            $('#affDryRunNote').hide();
        } else if (dryRun) {
            $('#affHttpRow').hide();
            $('#affResponseRow').hide();
            $('#affDryRunNote').show();
        }

        if (!d.success) {
            showAlert('affAlert', d.error || 'An error occurred.', 'danger');
        }

    } catch (err) {
        showAlert('affAlert', 'Request failed: ' + err.message);
    } finally {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Fire Affiliate Postback');
    }
});
</script>