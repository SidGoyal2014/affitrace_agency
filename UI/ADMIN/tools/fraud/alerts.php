<?php
$activePage = 'tools';
$pageTitle  = 'Fraud Alerts';
$pathPrefix = '../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-bell text-danger mr-2"></i>Fraud Alerts Feed</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">Alerts</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadAlerts()">
            <i class="fas fa-sync mr-1"></i>Refresh
        </button>
    </div>

    <div id="alert-container"></div>

    <div class="card shadow">
        <div class="card-body p-0">
            <table id="alertsTable" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Entity ID</th>
                        <th>Rule</th>
                        <th>Offer</th>
                        <th>Affiliate</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody id="alertsBody">
                    <tr><td colspan="7" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-secondary"></div> Loading...
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
const eventBadge = {
    click_flagged:       'warning',
    click_blocked:       'danger',
    conversion_rejected: 'dark',
    ip_blacklisted:      'secondary',
    device_blacklisted:  'secondary',
    rule_triggered:      'info',
};
let alertsDt;

async function loadAlerts() {
    try {
        const res   = await API.fetchWithAuth('admin/fraud/alerts?limit=200');
        const tbody = document.getElementById('alertsBody');
        if (alertsDt) { alertsDt.destroy(); }

        tbody.innerHTML = (res.data && res.data.length)
            ? res.data.map(e => {
                const d     = e.details || {};
                const color = eventBadge[e.event_type] || 'secondary';
                return `<tr>
                    <td style="white-space:nowrap;">${(e.created_at || '').substring(0, 19)}</td>
                    <td><span class="badge badge-${color}">${e.event_type.replace(/_/g, ' ')}</span></td>
                    <td><code>${e.entity_id || '-'}</code></td>
                    <td>${e.rule ? e.rule.name : '-'}</td>
                    <td>${e.offer_id     || '-'}</td>
                    <td>${e.affiliate_id || '-'}</td>
                    <td><small class="text-muted">${JSON.stringify(d).substring(0, 100)}</small></td>
                </tr>`;
            }).join('')
            : '<tr><td colspan="7" class="text-muted text-center py-3">No fraud events recorded yet.</td></tr>';

        alertsDt = $('#alertsTable').DataTable({ pageLength: 50, order: [[0, 'desc']] });
    } catch (err) {
        document.getElementById('alertsBody').innerHTML =
            `<tr><td colspan="7" class="text-danger text-center py-3">${err.message}</td></tr>`;
    }
}

document.addEventListener('DOMContentLoaded', loadAlerts);

// Auto-refresh every 30 seconds
setInterval(loadAlerts, 30000);
</script>
