<?php
$activePage = 'tools';
$pageTitle = 'IP Whitelist';
//$pathPrefix = '../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-check-circle text-success mr-2"></i>IP Whitelist</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">IP Whitelist</li>
                </ol>
            </nav>
        </div>
        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Important:</strong> Whitelisted IPs <strong>bypass all fraud checks entirely</strong>.
        Only add trusted IPs such as office IPs, internal tools, or verified advertiser servers.
    </div>

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0">Add Trusted IP</h6>
                </div>
                <div class="card-body">
                    <div id="alert-wl"></div>
                    <div class="form-group">
                        <label>IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="wlIp" placeholder="e.g. 203.0.113.1">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" class="form-control" id="wlReason"
                            placeholder="e.g. Office IP, Advertiser server">
                    </div>
                    <button class="btn btn-success" onclick="addWhitelist()">
                        <i class="fas fa-plus mr-1"></i>Add to Whitelist
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-list"></div>
    <div class="card shadow">
        <div class="card-body p-0">
            <table id="wlTable" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>Added</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody id="wlBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
    let wlDt;

    async function loadWhitelist() {
        try {
            const res = await API.fetchWithAuth('admin/fraud/whitelist/ip');
            const tbody = document.getElementById('wlBody');
            if (wlDt) { wlDt.destroy(); }

            tbody.innerHTML = (res.data || []).length
                ? res.data.map((r, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td><code>${r.ip_address}</code></td>
                    <td>${r.reason || '-'}</td>
                    <td>${(r.created_at || '').substring(0, 10) || '-'}</td>
                    <td>
                        <button onclick="removeWl(${r.id})" class="btn btn-xs btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`).join('')
                : '';
            wlDt = $('#wlTable').DataTable({
                pageLength: 25,
                language: {
                    emptyTable: "No IPs whitelisted yet."
                }
            });

        } catch (e) {
            document.getElementById('alert-list').innerHTML =
                `<div class="alert alert-danger">Failed to load: ${e.message}</div>`;
        }
    }

    async function addWhitelist() {
        const ip = document.getElementById('wlIp').value.trim();
        const reason = document.getElementById('wlReason').value.trim();
        const ac = document.getElementById('alert-wl');
        if (!ip) { ac.innerHTML = '<div class="alert alert-warning">Enter an IP address.</div>'; return; }

        const res = await API.fetchWithAuth('admin/fraud/whitelist/ip', {
            method: 'POST',
            body: JSON.stringify({ ip_address: ip, reason }),
        });
        if (res.status === 1) {
            ac.innerHTML = '<div class="alert alert-success">IP added to whitelist.</div>';
            document.getElementById('wlIp').value = '';
            document.getElementById('wlReason').value = '';
            loadWhitelist();
        } else {
            ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
        }
    }

    async function removeWl(id) {
        if (!confirm('Remove this IP from whitelist?')) return;
        const res = await API.fetchWithAuth(`admin/fraud/whitelist/ip/${id}`, { method: 'DELETE' });
        if (res.status === 1) loadWhitelist();
        else alert(res.error || 'Remove failed');
    }

    document.addEventListener('DOMContentLoaded', loadWhitelist);
</script>