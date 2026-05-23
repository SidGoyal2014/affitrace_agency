<?php
$pathPrefix = '..';
$activePage = 'roles';
$pageTitle = 'Role Management';
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
                <h1>Roles</h1>
                <a href="<?php echo $pathPrefix; ?>/roles/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Role
                </a>
            </div>

            <!-- Filter by User Type -->
            <div class="card-modern mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="mb-0 font-weight-bold">Filter by User Type:</label>
                        </div>
                        <div class="col-md-4">
                            <select id="filterUserType" class="form-control">
                                <option value="">All</option>
                                <option value="admin">Admin</option>
                                <option value="affiliate">Affiliate</option>
                                <option value="advertiser">Advertiser</option>
                                <option value="employee">Employee</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Role List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>                                    
                                    <th>Name</th>
                                    <th>User Type</th>
                                    <th>Default</th>
                                    <th>Modules</th>
                                    <th>Description</th>
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

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- DataTables -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        let dtInstance = null;

        async function loadRoles(userType = '') {
            if (!API.isAuthenticated()) {
                window.location.href = '../../login.php';
                return;
            }

            try {
                let url = 'admin/roles';
                if (userType) url += '?user_type=' + userType;

                const response = await API.fetchWithAuth(url);
                const roles = response.roles;
                const tbody = $('#table_body');

                // Destroy existing DataTable if any
                if (dtInstance) {
                    dtInstance.destroy();
                    dtInstance = null;
                }
                tbody.empty();

                if (roles && roles.length > 0) {
                    roles.forEach(role => {
                        const moduleNames = role.modules.map(m => m.name).join(', ') || '<span class="text-muted">None</span>';
                        const defaultBadge = role.is_default
                            ? '<span class="badge badge-success">Default</span>'
                            : '';
                        const userTypeBadge = {
                            admin: '<span class="badge badge-danger">Admin</span>',
                            affiliate: '<span class="badge badge-primary">Affiliate</span>',
                            advertiser: '<span class="badge badge-info">Advertiser</span>',
                            employee: '<span class="badge badge-warning">Employee</span>',
                        }[role.user_type] || role.user_type;

                        const row = `
                            <tr>                                
                                <td>${role.name}</td>
                                <td>${userTypeBadge}</td>
                                <td>${defaultBadge}</td>
                                <td><small>${moduleNames}</small></td>
                                <td><small>${role.description || '-'}</small></td>
                                <td>
                                    <a href="edit.php?id=${role.id}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
                                    <button onclick="deleteRole(${role.id}, '${role.name}')" class="btn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                    dtInstance = $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No roles found</td></tr>');
                }
            } catch (error) {
                console.error("Load Error:", error);
                $('#table_body').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load roles</td></tr>');
            }
        }

        async function deleteRole(id, name) {
            if (!confirm(`Are you sure you want to delete the role "${name}"?`)) return;

            try {
                await API.fetchWithAuth('admin/roles/' + id, { method: 'DELETE' });
                loadRoles($('#filterUserType').val());
            } catch (error) {
                alert(error.message || 'Failed to delete role. It may have users assigned.');
            }
        }

        $(document).ready(function () {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            loadRoles();

            $('#filterUserType').on('change', function () {
                loadRoles($(this).val());
            });
        });
    </script>
</body>

</html>
