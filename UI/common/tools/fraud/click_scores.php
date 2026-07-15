<?php
$activePage = 'tools';
$pageTitle = 'Click Quality Scores';
//$pathPrefix = '../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-chart-bar text-info mr-2"></i>Click Quality Scores</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">Click Quality</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-3">
        <div class="card-body py-3">
            <div class="form-row align-items-end">
                <div class="col-md-2">
                    <label class="small font-weight-bold">Status</label>
                    <select class="form-control form-control-sm" id="filterStatus">
                        <option value="">All</option>
                        <option value="clean">Clean</option>
                        <option value="flagged">Flagged</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Date From</label>
                    <input type="date" class="form-control form-control-sm" id="filterFrom">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Date To</label>
                    <input type="date" class="form-control form-control-sm" id="filterTo">
                </div>
                <div class="col-md-2">
                    <label class="small">&nbsp;</label>
                    <button class="btn btn-primary btn-sm d-block" onclick="loadScores()">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                </div>
                <div class="col-auto ml-auto">
                    <button id="bulkBlockBtn" onclick="bulkBlock()" class="btn btn-sm btn-danger" style="display:none;">
                        <i class="fas fa-ban mr-1"></i>Bulk Block Selected IPs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="card shadow">
        <div class="card-body p-0">
            <table id="scoresTable" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>TID</th>
                        <th>Offer</th>
                        <th>Affiliate</th>
                        <th>IP</th>
                        <th>Score</th>
                        <th>Flags</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="scoresBody">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">Use filters and click Filter to load data.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
    let scoresDt;

    function scoreColor(s) {
        if (s >= 70) return 'success';
        if (s >= 40) return 'warning';
        return 'danger';
    }

    async function loadScores() {
        const params = new URLSearchParams({
            status: document.getElementById('filterStatus').value,
            date_from: document.getElementById('filterFrom').value,
            date_to: document.getElementById('filterTo').value,
            limit: 500,
        });

        const tbody = document.getElementById('scoresBody');
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';

        try {
            const res = await API.fetchWithAuth('admin/fraud/click-scores?' + params.toString());
            if (scoresDt) { scoresDt.destroy(); }

            const badgeMap = { clean: 'success', flagged: 'warning', blocked: 'danger' };

            tbody.innerHTML = (res.data && res.data.length)
                ? res.data.map(r => {
                    const flags = (r.flags || []).join(', ') || '-';
                    const color = scoreColor(r.score);
                    return `<tr>
                    <td><input type="checkbox" class="row-check" value="${r.ip_address || ''}"
                               data-tid="${r.tid}"></td>
                    <td><code style="font-size:0.75rem;">${r.tid}</code></td>
                    <td>${r.offer_name}</td>
                    <td>${r.affiliate_name}</td>
                    <td><code>${r.ip_address || '-'}</code></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress mr-2" style="width:70px;height:16px;">
                                <div class="progress-bar bg-${color}" style="width:${r.score}%;"></div>
                            </div>
                            <span class="text-${color} font-weight-bold small">${r.score}</span>
                        </div>
                    </td>
                    <td><small class="text-muted">${flags}</small></td>
                    <td><span class="badge badge-${badgeMap[r.status] || 'secondary'}">${r.status}</span></td>
                    <td style="white-space:nowrap;">${(r.created_at || '').substring(0, 16)}</td>
                </tr>`;
                }).join('')
                : '';

            scoresDt = $('#scoresTable').DataTable({
                pageLength: 50,
                order: [[8, 'desc']],
                language: {
                    emptyTable: "No records found for the selected filters."
                }
            });



        } catch (err) {
            document.getElementById('alert-container').innerHTML =
                `<div class="alert alert-danger">${err.message}</div>`;
        }
    }

    function toggleSelectAll(cb) {
        document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
        updateBulkBtn();
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-check')) updateBulkBtn();
    });

    function updateBulkBtn() {
        const any = document.querySelectorAll('.row-check:checked').length > 0;
        document.getElementById('bulkBlockBtn').style.display = any ? '' : 'none';
        document.getElementById('selectAll').checked = false;
    }

    async function bulkBlock() {
        const checked = document.querySelectorAll('.row-check:checked');
        const ips = [...checked].map(c => c.value).filter(Boolean);
        if (!ips.length) return;
        if (!confirm(`Bulk-block ${ips.length} IP address(es)? They will be added to the IP blacklist.`)) return;

        const res = await API.fetchWithAuth('admin/fraud/bulk-block', {
            method: 'POST',
            body: JSON.stringify({ type: 'ip', values: ips }),
        });
        const ac = document.getElementById('alert-container');
        if (res.status === 1) {
            ac.innerHTML = `<div class="alert alert-success">${res.message}</div>`;
            document.getElementById('bulkBlockBtn').style.display = 'none';
        } else {
            ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Pre-apply affiliate filter from URL (linked from dashboard)
        const params = new URLSearchParams(window.location.search);
        if (params.get('affiliate_id')) {
            document.getElementById('filterStatus').value = '';
        }
        loadScores();
    });
</script>