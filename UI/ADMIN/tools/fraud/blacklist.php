<?php
$activePage = 'tools';
$pageTitle  = 'IP Blacklist';
$pathPrefix = '../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-ban text-danger mr-2"></i>IP Blacklist</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">IP Blacklist</li>
                </ol>
            </nav>
        </div>
        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    <ul class="nav nav-tabs mb-4" id="blTabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tabList">
                <i class="fas fa-list mr-1"></i>Blacklisted IPs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tabAdd">
                <i class="fas fa-plus mr-1"></i>Add Single IP
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tabImport">
                <i class="fas fa-file-import mr-1"></i>Bulk Import
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- List Tab -->
        <div class="tab-pane fade show active" id="tabList">
            <div id="alert-list"></div>
            <div class="card shadow">
                <div class="card-body p-0">
                    <table id="ipTable" class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr><th>#</th><th>IP Address</th><th>Reason</th><th>Source</th><th>Added</th><th>Remove</th></tr>
                        </thead>
                        <tbody id="ipBody">
                            <tr><td colspan="6" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Single IP Tab -->
        <div class="tab-pane fade" id="tabAdd">
            <div class="card shadow" style="max-width:500px;">
                <div class="card-header"><h6 class="m-0">Add IP to Blacklist</h6></div>
                <div class="card-body">
                    <div id="alert-add"></div>
                    <div class="form-group">
                        <label>IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newIp" placeholder="e.g. 1.2.3.4">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" class="form-control" id="newIpReason" placeholder="Optional reason">
                    </div>
                    <button class="btn btn-danger" onclick="addIp()">
                        <i class="fas fa-ban mr-1"></i>Blacklist IP
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Import Tab -->
        <div class="tab-pane fade" id="tabImport">
            <div class="card shadow" style="max-width:600px;">
                <div class="card-header"><h6 class="m-0">Bulk Import IPs</h6></div>
                <div class="card-body">
                    <div id="alert-import"></div>
                    <p class="text-muted small">Paste IP addresses — one per line or comma-separated. Duplicates and invalid entries are skipped.</p>
                    <textarea class="form-control mb-3" id="bulkIps" rows="10"
                              placeholder="1.2.3.4&#10;5.6.7.8&#10;9.10.11.12"></textarea>
                    <button class="btn btn-danger" onclick="importIps()">
                        <i class="fas fa-file-import mr-1"></i>Import IPs
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
let ipDt;

async function loadIpBlacklist() {
    try {
        const res   = await API.fetchWithAuth('admin/fraud/blacklist/ip');
        const tbody = document.getElementById('ipBody');
        if (ipDt) { ipDt.destroy(); }

        tbody.innerHTML = (res.data || []).length
            ? res.data.map((r, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td><code>${r.ip_address}</code></td>
                    <td>${r.reason || '-'}</td>
                    <td><span class="badge badge-light">${r.imported_from || 'manual'}</span></td>
                    <td>${(r.created_at || '').substring(0, 10) || '-'}</td>
                    <td>
                        <button onclick="removeIp(${r.id})" class="btn btn-xs btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`).join('')
            : '<tr><td colspan="6" class="text-muted text-center py-3">No IPs blacklisted yet.</td></tr>';

        ipDt = $('#ipTable').DataTable({ pageLength: 25 });
    } catch (e) {
        document.getElementById('alert-list').innerHTML =
            `<div class="alert alert-danger">Failed to load: ${e.message}</div>`;
    }
}

async function addIp() {
    const ip     = document.getElementById('newIp').value.trim();
    const reason = document.getElementById('newIpReason').value.trim();
    const ac     = document.getElementById('alert-add');
    if (!ip) { ac.innerHTML = '<div class="alert alert-warning">Enter an IP address.</div>'; return; }

    const res = await API.fetchWithAuth('admin/fraud/blacklist/ip', {
        method: 'POST',
        body: JSON.stringify({ ip_address: ip, reason }),
    });
    if (res.status === 1) {
        ac.innerHTML = '<div class="alert alert-success">IP blacklisted.</div>';
        document.getElementById('newIp').value     = '';
        document.getElementById('newIpReason').value = '';
        loadIpBlacklist();
    } else {
        ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
    }
}

async function removeIp(id) {
    if (!confirm('Remove this IP from blacklist?')) return;
    const res = await API.fetchWithAuth(`admin/fraud/blacklist/ip/${id}`, { method: 'DELETE' });
    if (res.status === 1) loadIpBlacklist();
    else alert(res.error || 'Remove failed');
}

async function importIps() {
    const raw = document.getElementById('bulkIps').value;
    const ips = raw.split(/[\n,]+/).map(s => s.trim()).filter(Boolean);
    const ac  = document.getElementById('alert-import');
    if (!ips.length) { ac.innerHTML = '<div class="alert alert-warning">No IPs found in input.</div>'; return; }

    const res = await API.fetchWithAuth('admin/fraud/blacklist/ip/import', {
        method: 'POST',
        body: JSON.stringify({ ips }),
    });
    if (res.status === 1) {
        ac.innerHTML = `<div class="alert alert-success">${res.message}</div>`;
        document.getElementById('bulkIps').value = '';
        loadIpBlacklist();
    } else {
        ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
    }
}

document.addEventListener('DOMContentLoaded', loadIpBlacklist);
</script>
