<?php
$rolePrefix = isset($api_link_to_list_affiliates) ? explode('/', $api_link_to_list_affiliates)[0] : 'admin';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>All Affiliates</h1>
                <a href="<?php echo $pathPrefix; ?>/affiliates/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Affiliate
                </a>
            </div>

            <!-- Horizontal Filter Panel -->
            <div class="card card-modern mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row align-items-end">
                        <div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Status</label>
                            <select name="status" id="filter_status" class="form-control form-control-sm">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Blocked</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <?php
                        /* 
                        <div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Manager</label>
                            <select name="manager_id" id="filter_manager" class="form-control form-control-sm">
                                <option value="">All Managers</option>
                            </select>
                        </div>
                        */
                        ?>
                        <div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Country</label>
                            <input type="text" name="country" id="filter_country" class="form-control form-control-sm"
                                placeholder="e.g. US, IN">
                        </div>
                        <div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Traffic Source</label>
                            <input type="text" name="traffic_source" id="filter_traffic_source"
                                class="form-control form-control-sm" placeholder="e.g. FB, SEO">
                        </div>
                        <?php
                        /*<div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Signup Start</label>
                            <input type="date" name="signup_start" id="filter_signup_start" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 form-group mb-2 mb-md-0">
                            <label class="form-label font-weight-bold small text-uppercase">Signup End</label>
                            <input type="date" name="signup_end" id="filter_signup_end" class="form-control form-control-sm">
                        </div>*/
                        ?>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button type="button" id="btnExportCSV" class="btn btn-outline-success btn-sm mr-2">
                                <i class="fas fa-file-export mr-1"></i> Export CSV
                            </button>
                            <button type="button" id="btnResetFilters" class="btn btn-outline-secondary btn-sm mr-2">
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter mr-1"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div class="bulk-actions-bar card card-modern mb-3 d-none bg-light border-left-primary" id="bulkActionsBar">
                <div class="card-body py-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="font-weight-bold text-primary" id="selectedCount">0</span> affiliates selected
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-success mr-2" id="bulkApproveBtn">
                            <i class="fas fa-check mr-1"></i> Bulk Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger mr-3" id="bulkRejectBtn">
                            <i class="fas fa-times mr-1"></i> Bulk Reject
                        </button>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <select id="bulkManagerSelect" class="form-control form-control-sm">
                                <option value="">Assign Manager...</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="bulkAssignManagerBtn">Assign</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affiliates Table -->
            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAll"></th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Manager</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Company</th>
                                    <th>Country</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        const rolePrefix = '<?php echo $rolePrefix; ?>';
        function getStatusBadge(status) {
            switch (status) {
                case 'active':
                    return '<span class="badge badge-success px-2 py-1">Active</span>';
                case 'pending':
                    return '<span class="badge badge-warning px-2 py-1">Pending</span>';
                case 'rejected':
                    return '<span class="badge badge-danger px-2 py-1" title="Blocked">Blocked</span>';
                case 'suspended':
                    return '<span class="badge badge-secondary px-2 py-1" title="Suspended">Suspended</span>';
                default:
                    return `<span class="badge badge-light px-2 py-1">${status}</span>`;
            }
        }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            // Populate managers filter & bulk action dropdown
            try {
                const empResponse = await API.fetchWithAuth(`${rolePrefix}/employees/list`);
                const managers = empResponse.data;
                const filterManager = $('#filter_manager');
                const bulkManager = $('#bulkManagerSelect');

                managers.forEach(m => {
                    filterManager.append(`<option value="${m.id}">${m.name}</option>`);
                    bulkManager.append(`<option value="${m.id}">${m.name}</option>`);
                });
            } catch (err) {
                console.error("Failed to load managers:", err);
            }

            // Load initial affiliate list
            loadAffiliates();

            // Submit filter form
            $('#filterForm').submit(function (e) {
                e.preventDefault();
                loadAffiliates();
            });

            // Reset filters
            $('#btnResetFilters').click(function () {
                $('#filterForm')[0].reset();
                loadAffiliates();
            });

            // Master checkbox event
            $('#selectAll').change(function () {
                $('.select-affiliate').prop('checked', $(this).prop('checked'));
                updateBulkActionsBar();
            });

            // Bulk Approve Click
            $('#bulkApproveBtn').click(async function () {
                const ids = getSelectedAffiliateIds();
                if (ids.length === 0) return;
                if (!confirm(`Are you sure you want to approve ${ids.length} selected affiliates?`)) return;

                try {
                    await API.fetchWithAuth(`${rolePrefix}/affiliates/bulk-status`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: ids, status: 'active' })
                    });
                    alert('Selected affiliates approved successfully');
                    loadAffiliates();
                } catch (e) {
                    alert('Bulk approval failed: ' + e.message);
                }
            });

            // Bulk Reject Click
            $('#bulkRejectBtn').click(async function () {
                const ids = getSelectedAffiliateIds();
                if (ids.length === 0) return;
                const reason = prompt(`Enter rejection reason for ${ids.length} selected affiliates:`);
                if (reason === null) return;

                try {
                    await API.fetchWithAuth(`${rolePrefix}/affiliates/bulk-status`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: ids, status: 'rejected', reason: reason })
                    });
                    alert('Selected affiliates rejected/blocked successfully');
                    loadAffiliates();
                } catch (e) {
                    alert('Bulk rejection failed: ' + e.message);
                }
            });

            // Bulk Assign Manager
            $('#bulkAssignManagerBtn').click(async function () {
                const val = $('#bulkManagerSelect').val();
                if (!val) {
                    alert('Please select a manager to assign');
                    return;
                }
                const ids = getSelectedAffiliateIds();
                if (ids.length === 0) return;
                if (!confirm(`Are you sure you want to assign manager to ${ids.length} selected affiliates?`)) return;

                try {
                    await Promise.all(ids.map(id =>
                        API.fetchWithAuth(`${rolePrefix}/affiliates/${id}/assign-manager`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ employee_id: val })
                        })
                    ));
                    alert('Manager assigned successfully to selected affiliates');
                    loadAffiliates();
                } catch (e) {
                    alert('Bulk manager assignment failed: ' + e.message);
                }
            });

            // Export CSV click
            $('#btnExportCSV').click(async function () {
                try {
                    const token = localStorage.getItem('access_token');
                    const agencyId = localStorage.getItem('agency_id') || localStorage.getItem('x_agency_id') || '1';

                    const params = $('#filterForm').serialize();
                    const baseUrl = API.BASE_URL ? API.BASE_URL : '/api';
                    const downloadUrl = `${baseUrl}/${rolePrefix}/affiliates/export?${params}`;

                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Exporting...');

                    const response = await fetch(downloadUrl, {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'X-Agency-Id': agencyId
                        }
                    });

                    if (!response.ok) {
                        alert('Failed to export affiliates');
                        return;
                    }

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `affiliates_export_${new Date().toISOString().slice(0, 10)}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();
                } catch (e) {
                    console.error("Export Error:", e);
                    alert("Export failed: " + e.message);
                } finally {
                    $('#btnExportCSV').prop('disabled', false).html('<i class="fas fa-file-export mr-1"></i> Export CSV');
                }
            });
        });

        async function loadAffiliates() {
            const tbody = $('#table_body');
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">Loading...</td></tr>');

            // Clear checked state
            $('#selectAll').prop('checked', false);
            updateBulkActionsBar();

            try {
                const params = $('#filterForm').serialize();
                const response = await API.fetchWithAuth(`<?php echo $api_link_to_list_affiliates; ?>?${params}`);
                const data = response.data;
                tbody.empty();

                // Destroy existing datatable
                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().destroy();
                }

                if (data && data.length > 0) {
                    data.forEach(aff => {
                        const statusBadge = getStatusBadge(aff.status);
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="select-affiliate" value="${aff.uid}"></td>
                                <td>
                                    <div class="font-weight-bold">${aff.name}</div>
                                    ${aff.referred_by ? `<div class="small text-muted"><i class="fas fa-users mr-1"></i>Referred: ${aff.referred_by}</div>` : ''}
                                </td>
                                <td>${statusBadge}</td>
                                <td>${aff.manager_name || '<span class="text-muted small">Unassigned</span>'}</td>
                                <td>${aff.email}</td>
                                <td>${aff.phone || '-'}</td>
                                <td>${aff.company || '-'}</td>
                                <td>${aff.country || '-'}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit.php?id=${aff.uid}"><i class="fas fa-edit mr-2 text-primary"></i> Edit Profile</a>
                                            <button class="dropdown-item" onclick="impersonateUser(${aff.uid})"><i class="fas fa-user-secret mr-2 text-warning"></i> Login As</button>
                                            <a class="dropdown-item" href="../reports/payouts.php?aid=${aff.uid}"><i class="fas fa-chart-line mr-2 text-info"></i> Payout History</a>
                                            <div class="dropdown-divider"></div>
                                            ${aff.status === 'pending' ? `
                                                <button class="dropdown-item" onclick="updateStatus(${aff.uid}, 'active')"><i class="fas fa-check mr-2 text-success"></i> Approve</button>
                                                <button class="dropdown-item" onclick="rejectAffiliatePrompt(${aff.uid})"><i class="fas fa-times mr-2 text-danger"></i> Reject</button>
                                            ` : ''}
                                            ${aff.status === 'active' ? `
                                                <button class="dropdown-item" onclick="suspendAffiliatePrompt(${aff.uid})"><i class="fas fa-ban mr-2 text-danger"></i> Suspend / Block</button>
                                            ` : ''}
                                            ${(aff.status === 'rejected' || aff.status === 'suspended') ? `
                                                <button class="dropdown-item" onclick="updateStatus(${aff.uid}, 'active')"><i class="fas fa-check mr-2 text-success"></i> Reactivate</button>
                                            ` : ''}
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger" onclick="deleteAffiliate(${aff.uid})"><i class="fas fa-trash-alt mr-2"></i> Delete Account</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    // Bind checkbox events after inserting rows
                    bindCheckboxEvents();

                    $('#dataTable').DataTable({
                        pageLength: 25,
                        columnDefs: [
                            { orderable: false, targets: [0, 8] } // disable sorting on checkbox and action columns
                        ]
                    });
                } else {
                    tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">No affiliates found</td></tr>');
                }

            } catch (error) {
                console.error("Load Error:", error);
                $('#table_body').html('<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load affiliates</td></tr>');
            }
        }

        function bindCheckboxEvents() {
            $('.select-affiliate').change(function () {
                updateBulkActionsBar();
            });
        }

        function getSelectedAffiliateIds() {
            const ids = [];
            $('.select-affiliate:checked').each(function () {
                ids.push($(this).val());
            });
            return ids;
        }

        function updateBulkActionsBar() {
            const selectedIds = getSelectedAffiliateIds();
            const count = selectedIds.length;
            const bar = $('#bulkActionsBar');

            if (count > 0) {
                $('#selectedCount').text(count);
                bar.removeClass('d-none');
            } else {
                bar.addClass('d-none');
            }
        }

        async function impersonateUser(id) {
            if (!confirm("Are you sure you want to log in as this affiliate?")) return;
            try {
                const res = await API.fetchWithAuth(`<?php echo $api_link_for_impersonate; ?>/${id}`, { method: 'POST' });

                // Save admin state
                localStorage.setItem('admin_access_token', localStorage.getItem('access_token'));
                localStorage.setItem('admin_user', localStorage.getItem('user'));

                // Set impersonated state
                localStorage.setItem('access_token', res.token);
                localStorage.setItem('user', JSON.stringify(res.user));
                localStorage.setItem('is_impersonating', 'true');
                localStorage.setItem('impersonator', JSON.stringify(res.impersonator));

                window.location.href = '../../AFFILIATE/dashboard.php';
            } catch (e) {
                alert('Failed to start impersonation: ' + (typeof e.message === 'object' ? Object.values(e.message).flat().join(' ') : e.message));
            }
        }

        async function updateStatus(id, status, reason = '') {
            if (!confirm(`Are you sure you want to change this affiliate's status to ${status}?`)) return;
            try {
                await API.fetchWithAuth(`${rolePrefix}/affiliates/${id}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: status, reason: reason })
                });
                alert('Affiliate status updated successfully');
                loadAffiliates();
            } catch (e) {
                alert('Failed to update status: ' + e.message);
            }
        }

        function suspendAffiliatePrompt(id) {
            const reason = prompt("Please enter the reason for blocking/suspending this affiliate:");
            if (reason === null) return; // cancelled
            updateStatus(id, 'suspended', reason);
        }

        function rejectAffiliatePrompt(id) {
            const reason = prompt("Please enter the reason for rejecting this affiliate:");
            if (reason === null) return; // cancelled
            updateStatus(id, 'rejected', reason);
        }

        async function deleteAffiliate(id) {
            if (!confirm("WARNING: Are you sure you want to permanently delete this affiliate account? This action cannot be undone.")) return;
            try {
                await API.fetchWithAuth(`${rolePrefix}/affiliates/${id}`, {
                    method: 'DELETE'
                });
                alert('Affiliate account deleted successfully');
                loadAffiliates();
            } catch (e) {
                alert('Failed to delete affiliate: ' + e.message);
            }
        }
    </script>
</body>

</html>