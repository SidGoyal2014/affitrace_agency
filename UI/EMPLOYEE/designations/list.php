<?php
$pathPrefix = '..';
$activePage = 'designations';
$pageTitle = 'Designations';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Designations</h1>
                <a href="<?php echo $pathPrefix; ?>/designations/create.php" class="btn btn-primary clsCreate" style="display: none;">
                    <i class="fas fa-plus mr-2"></i> Create Designation
                </a>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Designation List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Employees</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr><td colspan="5" class="text-center py-4 text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="designationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Designation</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="designationForm">
                    <div class="modal-body">
                        <input type="hidden" id="editId">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="desigName" required placeholder="e.g. Sales Executive">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" id="desigDesc" rows="2" placeholder="Optional"></textarea>
                        </div>
                        <div id="modal-alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>
    <?php
    /*
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    */
    ?>
    <script>
        let dtInstance = null;

        function openCreate() {
            $('#modalTitle').text('Add Designation');
            $('#editId').val('');
            $('#desigName').val('');
            $('#desigDesc').val('');
            $('#modal-alert').empty();
        }

        function openEdit(id, name, desc) {
            $('#modalTitle').text('Edit Designation');
            $('#editId').val(id);
            $('#desigName').val(name);
            $('#desigDesc').val(desc || '');
            $('#modal-alert').empty();
            $('#designationModal').modal('show');
        }

        async function loadDesignations() {
            try {
                const response = await API.fetchWithAuth('employee/designations');
                const items = response.designations;
                const tbody = $('#table_body');

                if (dtInstance) { dtInstance.destroy(); dtInstance = null; }
                tbody.empty();

                if (items && items.length > 0) {
                    items.forEach(d => {
                        tbody.append(`
                            <tr>
                                <td>${d.id}</td>
                                <td><strong>${d.name}</strong></td>
                                <td>${d.description || '-'}</td>
                                <td><span class="badge badge-secondary">${d.users_count || 0}</span></td>
                                <td>
                                    <button onclick="openEdit(${d.id}, '${d.name.replace(/'/g, "\\'")}', '${(d.description || '').replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-primary mr-1">Edit</button>
                                    <button onclick="deleteDesig(${d.id}, '${d.name.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>
                        `);
                    });
                    dtInstance = $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No designations found</td></tr>');
                }
            } catch (error) {
                $('#table_body').html('<tr><td colspan="5" class="text-center text-danger">Failed to load</td></tr>');
            }
        }

        async function deleteDesig(id, name) {
            if (!confirm(`Delete designation "${name}"?`)) return;
            try {
                await API.fetchWithAuth('employee/designations/' + id, { method: 'DELETE' });
                loadDesignations();
            } catch (error) {
                alert(error.message || 'Cannot delete. May have employees assigned.');
            }
        }

        $(document).ready(function () {
            if (!API.isAuthenticated()) { window.location.href = '../../login.php'; return; }
            if (!PermissionGuard.check('role_management', 'can_view')) return;
            
            // Handle UI Visibility
            const perms = JSON.parse(localStorage.getItem('permissions') || '{}');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user.user_type === 'admin' || (perms.role_management && perms.role_management.can_create)) {
                $('.clsCreate').show();
            }
            if (user.name) $('.user-name').text(user.name);

            loadDesignations();

            $('#designationForm').on('submit', async function (e) {
                e.preventDefault();
                $('#modal-alert').empty();
                const editId = $('#editId').val();
                const payload = { name: $('#desigName').val(), description: $('#desigDesc').val() };

                try {
                    if (editId) {
                        await API.fetchWithAuth('employee/designations/' + editId, {
                            method: 'PUT', body: JSON.stringify(payload),
                            headers: { 'Content-Type': 'application/json' },
                        });
                    } else {
                        await API.fetchWithAuth('employee/designations', {
                            method: 'POST', body: JSON.stringify(payload),
                            headers: { 'Content-Type': 'application/json' },
                        });
                    }
                    $('#designationModal').modal('hide');
                    loadDesignations();
                } catch (error) {
                    $('#modal-alert').html(`<div class="alert alert-danger">${error.message || 'Failed to save'}</div>`);
                }
            });
        });
    </script>
</body>
</html>
