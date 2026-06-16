<?php
$activePage = 'tools';
$pageTitle  = 'Create Fraud Rule';
$pathPrefix = '../../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header mb-4">
        <h1 class="h3 mb-0">Create Fraud Rule</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="../index.php">Fraud Detection</a></li>
                <li class="breadcrumb-item"><a href="list.php">Rules</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow" style="max-width:760px;">
        <div class="card-body">
            <div id="alert-container"></div>
            <form id="createForm">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Rule Name <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" required placeholder="e.g. Global VPN Block">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="description" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Rule Type <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="rule_type" required onchange="showConditionFields()">
                            <option value="">-- Select Type --</option>
                            <option value="ctit">CTIT (Click-to-Install Time)</option>
                            <option value="duplicate_ip">Duplicate IP</option>
                            <option value="vpn_proxy">VPN / Proxy</option>
                            <option value="bot">Bot Traffic</option>
                            <option value="geo_mismatch">Geo Mismatch</option>
                            <option value="datacenter">Datacenter IP</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Action <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="rule_action" required>
                            <option value="flag">Flag</option>
                            <option value="block">Block</option>
                            <option value="auto_reject">Auto-Reject Conversion</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Scope <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="scope" required onchange="toggleScopeId()">
                            <option value="global">Global</option>
                            <option value="per_advertiser">Per Advertiser</option>
                            <option value="per_affiliate">Per Affiliate</option>
                            <option value="per_offer">Per Offer</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row" id="scopeIdRow" style="display:none;">
                    <label class="col-sm-3 col-form-label">Scope ID <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" id="scope_id" placeholder="Advertiser / Affiliate / Offer ID">
                        <small class="text-muted">Enter the numeric ID of the advertiser, affiliate, or offer.</small>
                    </div>
                </div>

                <!-- Dynamic condition fields injected here -->
                <div id="conditionFields"></div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Active</label>
                    <div class="col-sm-9 d-flex align-items-center pt-1">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" checked>
                            <label class="custom-control-label" for="is_active">Enable rule immediately</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary mr-2">Create Rule</button>
                        <a href="list.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
function toggleScopeId() {
    const scope = document.getElementById('scope').value;
    document.getElementById('scopeIdRow').style.display = (scope === 'global') ? 'none' : '';
}

function showConditionFields() {
    const type      = document.getElementById('rule_type').value;
    const container = document.getElementById('conditionFields');

    const templates = {
        ctit: `
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Min CTIT (seconds)</label>
                <div class="col-sm-9"><input type="number" class="form-control" id="cond_min_seconds" value="30" min="0">
                <small class="text-muted">Conversions faster than this are suspicious (0 = no minimum).</small></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Max CTIT (seconds)</label>
                <div class="col-sm-9"><input type="number" class="form-control" id="cond_max_seconds" value="604800" min="1">
                <small class="text-muted">604800 = 7 days. Conversions older than this are suspicious.</small></div>
            </div>`,
        duplicate_ip: `
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Max Clicks per IP</label>
                <div class="col-sm-9"><input type="number" class="form-control" id="cond_max_count" value="5" min="1"></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Time Window (minutes)</label>
                <div class="col-sm-9"><input type="number" class="form-control" id="cond_window_minutes" value="60" min="1"></div>
            </div>`,
        vpn_proxy: `<div class="alert alert-info py-2 mt-2">Uses IPQS API. Configure API key in <a href="../third_party.php">3rd Party Config</a>. No additional conditions needed.</div>`,
        bot: `
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">UA Patterns</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cond_ua_patterns" value="bot,crawler,spider,headless,python-requests">
                    <small class="text-muted">Comma-separated strings to match against User-Agent header.</small>
                </div>
            </div>`,
        geo_mismatch: `<div class="alert alert-info py-2 mt-2">Checks click country against offer's targeted countries. No additional conditions needed.</div>`,
        datacenter:   `<div class="alert alert-info py-2 mt-2">Uses IPQS API to detect datacenter IPs. Configure API key in <a href="../third_party.php">3rd Party Config</a>.</div>`,
    };

    container.innerHTML = type ? (templates[type] || '') : '';
}

function buildConditions() {
    const type = document.getElementById('rule_type').value;
    const val  = (id, def) => { const el = document.getElementById(id); return el ? el.value : def; };
    switch (type) {
        case 'ctit':
            return { min_seconds: parseInt(val('cond_min_seconds', 30)), max_seconds: parseInt(val('cond_max_seconds', 604800)) };
        case 'duplicate_ip':
            return { max_count: parseInt(val('cond_max_count', 5)), window_minutes: parseInt(val('cond_window_minutes', 60)) };
        case 'bot':
            return { ua_patterns: val('cond_ua_patterns', 'bot').split(',').map(s => s.trim()).filter(Boolean) };
        default:
            return {};
    }
}

document.getElementById('createForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const ac    = document.getElementById('alert-container');
    const scope = document.getElementById('scope').value;
    const scopeId = document.getElementById('scope_id').value;

    if (scope !== 'global' && !scopeId) {
        ac.innerHTML = '<div class="alert alert-warning">Please enter a Scope ID for the selected scope.</div>';
        return;
    }

    const payload = {
        name:        document.getElementById('name').value.trim(),
        description: document.getElementById('description').value.trim(),
        rule_type:   document.getElementById('rule_type').value,
        rule_action: document.getElementById('rule_action').value,
        scope:       scope,
        scope_id:    scopeId ? parseInt(scopeId) : null,
        conditions:  buildConditions(),
        is_active:   document.getElementById('is_active').checked,
    };

    try {
        const res = await API.fetchWithAuth('admin/fraud/rules', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        if (res.status === 1) {
            ac.innerHTML = '<div class="alert alert-success">Rule created successfully! Redirecting...</div>';
            setTimeout(() => window.location.href = 'list.php', 1200);
        } else {
            ac.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to create rule'}</div>`;
        }
    } catch (err) {
        ac.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
});
</script>
