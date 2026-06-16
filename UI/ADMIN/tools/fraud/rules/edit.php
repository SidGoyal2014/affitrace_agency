<?php
$activePage = 'tools';
$pageTitle  = 'Edit Fraud Rule';
$pathPrefix = '../../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header mb-4">
        <h1 class="h3 mb-0">Edit Fraud Rule</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="../index.php">Fraud Detection</a></li>
                <li class="breadcrumb-item"><a href="list.php">Rules</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow" style="max-width:760px;">
        <div class="card-body">
            <div id="alert-container"></div>
            <form id="editForm">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Rule Name <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="description" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Rule Type</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control bg-light" id="rule_type_display" readonly>
                        <small class="text-muted">Rule type cannot be changed after creation.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Scope</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control bg-light" id="scope_display" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Action <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="rule_action">
                            <option value="flag">Flag</option>
                            <option value="block">Block</option>
                            <option value="auto_reject">Auto-Reject Conversion</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Conditions (JSON)</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="conditions" rows="5"
                                  placeholder='{"min_seconds": 30}'
                                  style="font-family:monospace;font-size:0.875rem;"></textarea>
                        <small class="text-muted">
                            ctit: <code>{"min_seconds":30,"max_seconds":604800}</code> &nbsp;|&nbsp;
                            duplicate_ip: <code>{"max_count":5,"window_minutes":60}</code> &nbsp;|&nbsp;
                            bot: <code>{"ua_patterns":["bot","crawler"]}</code>
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Active</label>
                    <div class="col-sm-9 d-flex align-items-center pt-1">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active">
                            <label class="custom-control-label" for="is_active">Rule is active</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary mr-2">Save Changes</button>
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
const ruleId = new URLSearchParams(window.location.search).get('id');

document.addEventListener('DOMContentLoaded', async function () {
    if (!ruleId) { window.location.href = 'list.php'; return; }

    try {
        const res = await API.fetchWithAuth(`admin/fraud/rules/${ruleId}`);
        if (res.status !== 1) throw new Error(res.error || 'Rule not found');
        const d = res.data;

        document.getElementById('name').value            = d.name        || '';
        document.getElementById('description').value     = d.description || '';
        document.getElementById('rule_type_display').value = d.rule_type || '';
        document.getElementById('rule_action').value     = d.rule_action || 'flag';
        document.getElementById('scope_display').value   = d.scope + (d.scope_id ? ` (#${d.scope_id})` : '');
        document.getElementById('conditions').value      = JSON.stringify(d.conditions || {}, null, 2);
        document.getElementById('is_active').checked     = !!d.is_active;
    } catch (err) {
        document.getElementById('alert-container').innerHTML =
            `<div class="alert alert-danger">Failed to load rule: ${err.message}</div>`;
    }
});

document.getElementById('editForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const ac = document.getElementById('alert-container');

    let conditions;
    try {
        conditions = JSON.parse(document.getElementById('conditions').value || '{}');
    } catch (jsonErr) {
        ac.innerHTML = '<div class="alert alert-danger">Invalid JSON in the Conditions field. Please fix it before saving.</div>';
        return;
    }

    const payload = {
        name:        document.getElementById('name').value.trim(),
        description: document.getElementById('description').value.trim(),
        rule_action: document.getElementById('rule_action').value,
        conditions:  conditions,
        is_active:   document.getElementById('is_active').checked,
    };

    try {
        const res = await API.fetchWithAuth(`admin/fraud/rules/${ruleId}`, {
            method: 'PUT',
            body: JSON.stringify(payload),
        });
        if (res.status === 1) {
            ac.innerHTML = '<div class="alert alert-success">Rule updated successfully!</div>';
            setTimeout(() => window.location.href = 'list.php', 1200);
        } else {
            ac.innerHTML = `<div class="alert alert-danger">${res.error || 'Update failed'}</div>`;
        }
    } catch (err) {
        ac.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
});
</script>
