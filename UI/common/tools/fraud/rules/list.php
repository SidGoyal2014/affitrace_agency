<?php
$activePage = 'tools';
$pageTitle = 'Fraud Rules';
//$pathPrefix = '../../..';
include $pathPrefix . '/includes/head.php';
include $pathPrefix . '/includes/topnav.php';
?>

<div class="container-fluid mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Fraud Rules</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="../../../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="../index.php">Fraud Detection</a></li>
                    <li class="breadcrumb-item active">Rules</li>
                </ol>
            </nav>
        </div>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Create Rule</a>
    </div>

    <div id="alert-container"></div>

    <div class="card shadow">
        <div class="card-body p-0">
            <table id="dataTable" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Scope</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rulesBody"></tbody>
            </table>
        </div>
    </div>
</div>

<?php include $pathPrefix . '/includes/footer.php'; ?>
<?php include $pathPrefix . '/includes/scripts.php'; ?>
<script>
    const actionColor = { block: 'danger', flag: 'warning', auto_reject: 'dark' };
    let dt;

    async function loadRules() {
        try {
            const res = await API.fetchWithAuth('admin/fraud/rules');
            if (res.status !== 1) throw new Error(res.error || 'Failed');
            const tbody = document.getElementById('rulesBody');
            if (dt) { dt.destroy(); }

            tbody.innerHTML = (res.data || []).map((r, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>
                    <strong>${r.name}</strong>
                    ${r.description ? `<br><small class="text-muted">${r.description}</small>` : ''}
                </td>
                <td><span class="badge badge-secondary">${r.type_label}</span></td>
                <td>
                    <span class="badge badge-light">${r.scope_label}</span>
                    ${r.scope_id ? `<small class="text-muted ml-1">#${r.scope_id}</small>` : ''}
                </td>
                <td><span class="badge badge-${actionColor[r.rule_action] || 'secondary'}">${r.action_label}</span></td>
                <td>
                    <button onclick="toggleRule(${r.id}, this)"
                            class="btn btn-xs ${r.is_active ? 'btn-success' : 'btn-secondary'}">
                        ${r.is_active ? 'Active' : 'Inactive'}
                    </button>
                </td>
                <td>
                    ${r.created_at || '-'}<br>
                    <small class="text-muted">${r.created_by_name}</small>
                </td>
                <td>
                    <a href="edit.php?id=${r.id}" class="btn btn-xs btn-outline-primary mr-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteRule(${r.id})" class="btn btn-xs btn-outline-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`).join('');

            dt = $('#dataTable').DataTable({
                pageLength: 25,
                order: [[0, 'asc']],
                language: {
                    emptyTable: "No rules found. <a href='create.php'>Create one</a>."
                }
            });



        } catch (e) {
            document.getElementById('alert-container').innerHTML =
                `<div class="alert alert-danger">Failed to load rules: ${e.message}</div>`;
        }
    }

    async function toggleRule(id, btn) {
        try {
            const res = await API.fetchWithAuth(`admin/fraud/rules/${id}/toggle`, { method: 'PATCH' });
            if (res.status === 1) {
                btn.className = `btn btn-xs ${res.is_active ? 'btn-success' : 'btn-secondary'}`;
                btn.textContent = res.is_active ? 'Active' : 'Inactive';
            }
        } catch (e) { alert('Toggle failed'); }
    }

    async function deleteRule(id) {
        if (!confirm('Delete this fraud rule? This cannot be undone.')) return;
        try {
            const res = await API.fetchWithAuth(`admin/fraud/rules/${id}`, { method: 'DELETE' });
            if (res.status === 1) loadRules();
            else alert(res.error || 'Delete failed');
        } catch (e) { alert('Delete failed'); }
    }

    document.addEventListener('DOMContentLoaded', loadRules);
</script>