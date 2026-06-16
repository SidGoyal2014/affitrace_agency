<?php
$activePage       = 'tools';
$pageTitle        = 'Fraud Detection Dashboard';
$pathPrefix       = '../..';
$pathCommonPrefix = '../../../common';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Fraud Detection</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="../../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fraud Detection</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="alerts.php" class="btn btn-sm btn-outline-danger mr-2"><i class="fas fa-bell mr-1"></i>Alerts</a>
            <a href="rules/list.php" class="btn btn-sm btn-primary"><i class="fas fa-list mr-1"></i>Manage Rules</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Blocked Clicks</div>
                            <div class="h5 mb-0 font-weight-bold" id="kpiBlocked">-</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-ban fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Flagged Clicks</div>
                            <div class="h5 mb-0 font-weight-bold" id="kpiFlagged">-</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-flag fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clean Clicks</div>
                            <div class="h5 mb-0 font-weight-bold" id="kpiClean">-</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rejected Conversions Today</div>
                            <div class="h5 mb-0 font-weight-bold" id="kpiRejected">-</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Top Fraud Patterns -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Fraud Patterns (Last 7 Days)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0 px-3" id="patternsTable">
                        <thead><tr><th class="pl-3">Rule Type</th><th>Count</th></tr></thead>
                        <tbody id="patternsBody">
                            <tr><td colspan="2" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Top Fraud Affiliates -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top Fraudulent Affiliates</h6>
                    <a href="click_scores.php" class="btn btn-sm btn-outline-primary">View All Scores</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0" id="topAffTable">
                        <thead class="thead-light"><tr><th class="pl-3">Affiliate</th><th>Fraud Clicks</th><th>Avg Score</th><th>Action</th></tr></thead>
                        <tbody id="topAffBody">
                            <tr><td colspan="4" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Fraud Events -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Fraud Events</h6>
            <a href="alerts.php" class="btn btn-sm btn-outline-danger">View All Alerts</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Time</th><th>Event</th><th>Entity</th><th>Rule</th><th>Details</th></tr>
                </thead>
                <tbody id="eventsBody">
                    <tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <?php
        $links = [
            ['href' => 'rules/list.php',      'icon' => 'fa-list',         'color' => 'primary',   'title' => 'Fraud Rules',       'desc' => 'Manage detection rules'],
            ['href' => 'blacklist.php',        'icon' => 'fa-ban',          'color' => 'danger',    'title' => 'IP Blacklist',       'desc' => 'Blocked IP addresses'],
            ['href' => 'whitelist.php',        'icon' => 'fa-check-circle', 'color' => 'success',   'title' => 'IP Whitelist',       'desc' => 'Trusted IP addresses'],
            ['href' => 'device_blacklist.php', 'icon' => 'fa-mobile-alt',   'color' => 'warning',   'title' => 'Device Blacklist',   'desc' => 'Blocked device IDs'],
            ['href' => 'click_scores.php',     'icon' => 'fa-chart-bar',    'color' => 'info',      'title' => 'Click Quality',      'desc' => 'Click quality scores'],
            ['href' => 'third_party.php',      'icon' => 'fa-plug',         'color' => 'secondary', 'title' => '3rd Party Config',   'desc' => 'IPQS / Anura / 24metrics'],
        ];
        foreach ($links as $l): ?>
        <div class="col-xl-2 col-md-4 mb-3">
            <a href="<?= $l['href'] ?>" class="text-decoration-none">
                <div class="card border-left-<?= $l['color'] ?> shadow h-100 py-2">
                    <div class="card-body text-center py-3">
                        <i class="fas <?= $l['icon'] ?> fa-2x text-<?= $l['color'] ?> mb-2"></i>
                        <div class="font-weight-bold small"><?= $l['title'] ?></div>
                        <div class="text-muted" style="font-size:0.75rem;"><?= $l['desc'] ?></div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
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

document.addEventListener('DOMContentLoaded', async function () {
    try {
        const res = await API.fetchWithAuth('admin/fraud/dashboard');
        if (res.status !== 1) throw new Error(res.error || 'Failed to load dashboard');
        const d = res.data;

        document.getElementById('kpiBlocked').textContent  = (d.total_blocked  || 0).toLocaleString();
        document.getElementById('kpiFlagged').textContent  = (d.total_flagged  || 0).toLocaleString();
        document.getElementById('kpiClean').textContent    = (d.total_clean    || 0).toLocaleString();
        document.getElementById('kpiRejected').textContent = (d.rejected_conversions_today || 0);

        // Patterns
        const pb = document.getElementById('patternsBody');
        pb.innerHTML = (d.top_patterns && d.top_patterns.length)
            ? d.top_patterns.map(p => `<tr><td class="pl-3">${p.rule_type}</td><td><strong>${p.count}</strong></td></tr>`).join('')
            : '<tr><td colspan="2" class="text-muted text-center py-3">No pattern data yet</td></tr>';

        // Top affiliates
        const ab = document.getElementById('topAffBody');
        ab.innerHTML = (d.top_fraud_affiliates && d.top_fraud_affiliates.length)
            ? d.top_fraud_affiliates.map(a => `
                <tr>
                    <td class="pl-3">${a.name || 'ID ' + a.affiliate_id}</td>
                    <td>${a.fraud_count}</td>
                    <td>${parseFloat(a.avg_score || 0).toFixed(1)}</td>
                    <td><a href="click_scores.php?affiliate_id=${a.affiliate_id}" class="btn btn-xs btn-outline-danger">View</a></td>
                </tr>`).join('')
            : '<tr><td colspan="4" class="text-muted text-center py-3">No fraud affiliates found</td></tr>';

        // Recent events
        const eb = document.getElementById('eventsBody');
        eb.innerHTML = (d.recent_events && d.recent_events.length)
            ? d.recent_events.map(e => {
                const det   = e.details || {};
                const color = eventBadge[e.event_type] || 'secondary';
                return `<tr>
                    <td style="white-space:nowrap;">${(e.created_at || '').substring(0,19)}</td>
                    <td><span class="badge badge-${color}">${e.event_type.replace(/_/g, ' ')}</span></td>
                    <td><code>${e.entity_id || '-'}</code></td>
                    <td>${e.rule ? e.rule.name : '-'}</td>
                    <td><small>${JSON.stringify(det).substring(0, 80)}</small></td>
                </tr>`;
            }).join('')
            : '<tr><td colspan="5" class="text-muted text-center py-3">No recent events</td></tr>';

    } catch (err) {
        console.error('Fraud dashboard error:', err);
    }
});
</script>
