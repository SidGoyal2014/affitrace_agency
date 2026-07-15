<?php
$activePage = 'tools';
$pageTitle = '3rd Party Fraud Config';
//$pathPrefix = '../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-plug mr-2"></i>3rd Party Fraud Tool Configuration</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="index.php">Fraud Detection</a></li>
                <li class="breadcrumb-item active">3rd Party Config</li>
            </ol>
        </nav>
    </div>

    <div id="alert-container" class="mb-3"></div>

    <div class="row" id="providerCards">
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-secondary"></div>
        </div>
    </div>
</div>

<!-- Configure Provider Modal -->
<div class="modal fade" id="configModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Configure Provider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProvider">
                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" class="form-control" id="modalApiKey" placeholder="Enter full API key">
                    <small class="text-muted">Current key is masked. Leave blank to keep the existing key
                        unchanged.</small>
                </div>
                <div class="form-group">
                    <label>API URL <small class="text-muted">(optional override)</small></label>
                    <input type="text" class="form-control" id="modalApiUrl" placeholder="https://...">
                </div>
                <div class="form-group mb-0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="modalActive">
                        <label class="custom-control-label" for="modalActive">Enable this provider</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveConfig()">
                    <i class="fas fa-save mr-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
    const providerMeta = {
        ipqs: {
            name: 'IPQualityScore (IPQS)',
            icon: 'fas fa-shield-alt',
            color: 'primary',
            desc: 'VPN, proxy, bot, and datacenter IP detection. Used by VPN/Proxy and Datacenter fraud rules.',
            url: 'https://www.ipqualityscore.com',
        },
        anura: {
            name: 'Anura',
            icon: 'fas fa-robot',
            color: 'success',
            desc: 'Invalid traffic and bot detection for performance marketing campaigns.',
            url: 'https://www.anura.io',
        },
        '24metrics': {
            name: '24metrics',
            icon: 'fas fa-chart-pie',
            color: 'warning',
            desc: 'Fraud scoring and bot detection built specifically for affiliate networks.',
            url: 'https://www.24metrics.com',
        },
    };

    let currentConfigs = {};

    async function loadConfigs() {
        try {
            const res = await API.fetchWithAuth('admin/fraud/third-party-config');
            if (res.status !== 1) throw new Error(res.error || 'Failed');
            currentConfigs = res.data;
            renderCards(res.data);
        } catch (err) {
            document.getElementById('providerCards').innerHTML =
                `<div class="col-12"><div class="alert alert-danger">${err.message}</div></div>`;
        }
    }

    function renderCards(data) {
        const container = document.getElementById('providerCards');
        container.innerHTML = Object.entries(data).map(([key, cfg]) => {
            const meta = providerMeta[key] || { name: key, icon: 'fas fa-cog', color: 'secondary', desc: '', url: '#' };
            const statusBadge = cfg.is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';
            return `
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-${meta.color} text-white d-flex align-items-center justify-content-between py-3">
                    <span class="font-weight-bold"><i class="${meta.icon} mr-2"></i>${meta.name}</span>
                    ${statusBadge}
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">${meta.desc}</p>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted pl-0" style="width:90px;">API Key</td>
                            <td>${cfg.api_key ? `<code>${cfg.api_key}</code>` : '<em class="text-muted">Not configured</em>'}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-0">Updated</td>
                            <td>${cfg.updated_at || '<em class="text-muted">Never</em>'}</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                    <a href="${meta.url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-external-link-alt mr-1"></i>Docs
                    </a>
                    <button class="btn btn-sm btn-${meta.color}" onclick="openModal('${key}')">
                        <i class="fas fa-cog mr-1"></i>Configure
                    </button>
                </div>
            </div>
        </div>`;
        }).join('');
    }

    function openModal(provider) {
        const cfg = currentConfigs[provider] || {};
        const meta = providerMeta[provider] || { name: provider };

        document.getElementById('modalProvider').value = provider;
        document.getElementById('modalTitle').textContent = 'Configure ' + meta.name;
        document.getElementById('modalApiKey').value = '';
        document.getElementById('modalApiUrl').value = cfg.api_url || '';
        document.getElementById('modalActive').checked = !!cfg.is_active;

        $('#configModal').modal('show');
    }

    async function saveConfig() {
        const ac = document.getElementById('alert-container');
        const apiKey = document.getElementById('modalApiKey').value.trim();

        const payload = {
            provider: document.getElementById('modalProvider').value,
            api_url: document.getElementById('modalApiUrl').value.trim(),
            is_active: document.getElementById('modalActive').checked,
        };
        if (apiKey) payload.api_key = apiKey;

        try {
            const res = await API.fetchWithAuth('admin/fraud/third-party-config', {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            $('#configModal').modal('hide');
            if (res.status === 1) {
                ac.innerHTML = '<div class="alert alert-success">Configuration saved successfully.</div>';
                loadConfigs();
            } else {
                ac.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
            }
        } catch (err) {
            $('#configModal').modal('hide');
            ac.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', loadConfigs);
</script>