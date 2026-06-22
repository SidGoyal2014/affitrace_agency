<?php
$rolePrefix = isset($api_link_to_list_advertisers) ? explode('/', $api_link_to_list_advertisers)[0] : 'admin';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>All Advertisers</h1>
                <div>
                    <button type="button" id="btnExportCSV" class="btn btn-outline-success mr-2">
                        <i class="fas fa-file-export mr-1"></i> Export CSV
                    </button>
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create Advertiser
                    </a>
                </div>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Advertiser List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Manager</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Tags</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notes & Tags Modal -->
    <div class="modal fade" id="notesTagsModal" tabindex="-1" role="dialog" aria-labelledby="notesTagsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notesTagsModalLabel">Edit Notes & Tags</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalAdvertiserId">
                    <div class="form-group">
                        <label for="modalTags"><strong>Tags:</strong> <small class="text-muted">(Comma-separated)</small></label>
                        <input type="text" id="modalTags" class="form-control" placeholder="e.g. vip, retail, finance">
                    </div>
                    <div class="form-group">
                        <label for="modalNotes"><strong>Private Notes:</strong></label>
                        <textarea id="modalNotes" class="form-control" rows="4" placeholder="Internal admin notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNotesTagsBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

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
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            // Load initial list
            loadAdvertisers();

            // Export CSV click
            $('#btnExportCSV').click(async function () {
                try {
                    const token = localStorage.getItem('access_token');
                    const agencyId = localStorage.getItem('agency_id') || localStorage.getItem('x_agency_id') || '1';
                    const baseUrl = API.BASE_URL ? API.BASE_URL : '/api';
                    const downloadUrl = `${baseUrl}/${rolePrefix}/advertisers/export`;

                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Exporting...');

                    const response = await fetch(downloadUrl, {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'X-Agency-Id': agencyId
                        }
                    });

                    if (!response.ok) {
                        alert('Failed to export advertisers');
                        return;
                    }

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `advertisers_export_${new Date().toISOString().slice(0, 10)}.csv`;
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

            // Save Notes/Tags click
            $('#saveNotesTagsBtn').click(async function () {
                const id = $('#modalAdvertiserId').val();
                const notes = $('#modalNotes').val();
                const tags = $('#modalTags').val();
                const btn = $(this);

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
                try {
                    await API.fetchWithAuth(`${rolePrefix}/advertisers/${id}/notes-tags`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ notes: notes, tags: tags })
                    });
                    $('#notesTagsModal').modal('hide');
                    loadAdvertisers();
                } catch (e) {
                    alert('Failed to save notes/tags: ' + e.message);
                } finally {
                    btn.prop('disabled', false).text('Save Changes');
                }
            });
        });

        async function loadAdvertisers() {
            try {
                const tbody = $('#table_body');
                tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Loading...</td></tr>');

                const response = await API.fetchWithAuth('<?php echo $api_link_to_list_advertisers; ?>');
                const data = response.data || [];
                tbody.empty();

                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().destroy();
                }

                if (data.length > 0) {
                    data.forEach((adv) => {
                        const statusBadge = getStatusBadge(adv.status);
                        
                        // Parse Tags
                        let tagsHtml = '-';
                        if (adv.tags && adv.tags.trim() !== '') {
                            tagsHtml = adv.tags.split(',').map(t => `<span class="badge badge-light border text-secondary px-2 py-1 mr-1">${t.trim()}</span>`).join('');
                        }

                        tbody.append(`
                            <tr>
                                <td>${adv.name}</td>
                                <td>${statusBadge}</td>
                                <td>${adv.manager_name || '<span class="text-muted small">Unassigned</span>'}</td>
                                <td>${adv.email}</td>
                                <td>${adv.company || '-'}</td>
                                <td>${tagsHtml}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit.php?id=${adv.uid}"><i class="fas fa-edit mr-2 text-primary"></i> Edit Profile</a>
                                            <button class="dropdown-item" onclick="impersonateUser(${adv.uid})"><i class="fas fa-user-secret mr-2 text-warning"></i> Login As</button>
                                            <button class="dropdown-item" onclick="openNotesTagsModal(${adv.uid}, \`${(adv.private_note || '').replace(/`/g, '\\`').replace(/'/g, "\\'")}\`, \`${(adv.tags || '').replace(/`/g, '\\`').replace(/'/g, "\\'")}\`)"><i class="fas fa-sticky-note mr-2 text-info"></i> Notes / Tags</button>
                                            <div class="dropdown-divider"></div>
                                            ${adv.status === 'pending' ? `
                                                <button class="dropdown-item" onclick="updateStatus(${adv.uid}, 'active')"><i class="fas fa-check mr-2 text-success"></i> Approve</button>
                                                <button class="dropdown-item" onclick="rejectAdvertiserPrompt(${adv.uid})"><i class="fas fa-times mr-2 text-danger"></i> Reject</button>
                                            ` : ''}
                                            ${adv.status === 'active' ? `
                                                <button class="dropdown-item" onclick="suspendAdvertiserPrompt(${adv.uid})"><i class="fas fa-ban mr-2 text-danger"></i> Suspend / Block</button>
                                            ` : ''}
                                            ${(adv.status === 'rejected' || adv.status === 'suspended') ? `
                                                <button class="dropdown-item" onclick="updateStatus(${adv.uid}, 'active')"><i class="fas fa-check mr-2 text-success"></i> Reactivate</button>
                                            ` : ''}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ 
                        pageLength: 25,
                        columnDefs: [
                            { orderable: false, targets: [6] }
                        ]
                    });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No advertisers found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#table_body').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        }

        function openNotesTagsModal(id, notes, tags) {
            $('#modalAdvertiserId').val(id);
            $('#modalNotes').val(notes);
            $('#modalTags').val(tags);
            $('#notesTagsModal').modal('show');
        }

        async function updateStatus(id, status, reason = '') {
            if (!confirm(`Are you sure you want to change this advertiser's status to ${status}?`)) return;
            try {
                await API.fetchWithAuth(`${rolePrefix}/advertisers/${id}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: status, reason: reason })
                });
                alert('Advertiser status updated successfully');
                loadAdvertisers();
            } catch (e) {
                alert('Failed to update status: ' + e.message);
            }
        }

        function suspendAdvertiserPrompt(id) {
            const reason = prompt("Please enter the reason for blocking/suspending this advertiser:");
            if (reason === null) return; // cancelled
            updateStatus(id, 'suspended', reason);
        }

        function rejectAdvertiserPrompt(id) {
            const reason = prompt("Please enter the reason for rejecting this advertiser:");
            if (reason === null) return; // cancelled
            updateStatus(id, 'rejected', reason);
        }

        async function impersonateUser(id) {
            if (!confirm("Are you sure you want to log in as this advertiser?")) return;
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

                window.location.href = '../../ADVERTISER/dashboard.php';
            } catch (e) {
                alert('Failed to start impersonation: ' + (typeof e.message === 'object' ? Object.values(e.message).flat().join(' ') : e.message));
            }
        }
    </script>
</body>

</html>