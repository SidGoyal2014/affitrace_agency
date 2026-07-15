<?php
$activePage = 'tools';
$pageTitle = 'Device ID Blacklist';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-mobile-alt text-warning mr-2"></i>Device ID Blacklist</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">Device Blacklist</li>
                </ol>
            </nav>
        </div>
        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0">Add Device ID</h6>
                </div>
                <div class="card-body">
                    <div id="alert-dev"></div>
                    <div class="form-group">
                        <label>Device ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deviceId" placeholder="IDFA / GAID / Android ID">
                    </div>
                    <div class="form-group">
                        <label>Device Type</label>
                        <select class="form-control" id="deviceType">
                            <option value="">-- Select Type --</option>
                            <option value="ios_idfa">iOS IDFA</option>
                            <option value="google_aid">Google AID</option>
                            <option value="android_id">Android ID</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" class="form-control" id="deviceReason" placeholder="Optional reason">
                    </div>
                    <button class="btn btn-warning" onclick="addDevice()">
                        <i class="fas fa-plus mr-1"></i>Blacklist Device
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-list"></div>
    <div class="card shadow">
        <div class="card-body p-0">
            <table id="devTable" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Device ID</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Added</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody id="devBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
    let devDt;

    async function loadDevices() {
        try {
            const res = await API.fetchWithAuth('admin/fraud/blacklist/device');
            const tbody = document.getElementById('devBody');
            if (devDt) { devDt.destroy(); }

            tbody.innerHTML = (res.data || []).length
                ? res.data.map((r, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td><code>${r.device_id}</code></td>
                    <td>${r.device_type || '-'}</td>
                    <td>${r.reason || '-'}</td>
                    <td>${(r.created_at || '').substring(0, 10) || '-'}</td>
                    <td>
                        <button onclick="removeDevice(${r.id})" class="btn btn-xs btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`).join('')
                : '';

            devDt = $('#devTable').DataTable({
                pageLength: 25,
                language: {
                    emptyTable: "No devices blacklisted yet."
                }
            });
        } catch (e) {
            document.getElementById('alert-list').innerHTML =
                `<div class="alert alert-danger">Failed to load: ${e.message}</div>`;
        }
    }

    async function addDevice() {
        const ac = document.getElementById('alert-dev');
        const payload = {
            device_id: document.getElementById('deviceId').value.trim(),
            device_type: document.getElementById('deviceType').value,
            reason: document.getElementById('deviceReason').value.trim(),
        };
        if (!payload.device_id) { ac.innerHTML = '<div class="alert alert-warning">Enter a device ID.</div>'; return; }

        const res = await API.fetchWithAuth('admin/fraud/blacklist/device', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        if (res.status === 1) {
            ac.innerHTML = '<div class="alert alert-success">Device ID blacklisted.</div>';
            document.getElementById('deviceId').value = '';
            document.getElementById('deviceReason').value = '';
            document.getElementById('deviceType').value = '';
            loadDevices();
        } else {
            ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
        }
    }

    async function removeDevice(id) {
        if (!confirm('Remove this device ID from blacklist?')) return;
        const res = await API.fetchWithAuth(`admin/fraud/blacklist/device/${id}`, { method: 'DELETE' });
        if (res.status === 1) loadDevices();
        else alert(res.error || 'Remove failed');
    }

    document.addEventListener('DOMContentLoaded', loadDevices);
</script>