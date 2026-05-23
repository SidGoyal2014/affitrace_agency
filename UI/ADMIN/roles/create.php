<?php
$pathPrefix = '..';
$activePage = 'roles';
$pageTitle = 'Create Role';
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
                <h1>Create Role</h1>
                <a href="<?php echo $pathPrefix; ?>/roles/list.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Roles
                </a>
            </div>

            <form id="roleForm">
                <!-- Role Details -->
                <div class="card-modern mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Role Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="roleName" required placeholder="e.g. Sales Executive Role">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>User Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="userType" required>
                                        <option value="">Select User Type</option>
                                        <option value="affiliate">Affiliate</option>
                                        <option value="advertiser">Advertiser</option>
                                        <option value="employee">Employee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Default Role</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="isDefault">
                                        <label class="custom-control-label" for="isDefault">Set as default for this user type</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" id="roleDescription" rows="2" placeholder="Optional description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permission Matrix -->
                <div class="card-modern mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">Module Permissions</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-success mr-1" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect All</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-flush mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Module</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Create</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionMatrix">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Loading modules...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="alert-container"></div>

                <button type="submit" class="btn btn-primary btn-lg" id="btnSave">
                    <i class="fas fa-save mr-2"></i> Create Role
                </button>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let modules = [];

        async function loadModules() {
            try {
                const response = await API.fetchWithAuth('admin/modules');
                modules = response.modules;
                renderPermissionMatrix();
            } catch (error) {
                console.error('Failed to load modules:', error);
                $('#permissionMatrix').html('<tr><td colspan="5" class="text-center text-danger">Failed to load modules</td></tr>');
            }
        }

        function renderPermissionMatrix(existingPermissions = {}) {
            const tbody = $('#permissionMatrix');
            tbody.empty();

            modules.forEach(mod => {
                const perm = existingPermissions[mod.id] || {};
                const row = `
                    <tr>
                        <td>
                            <strong>${mod.name}</strong>
                            <br><small class="text-muted">${mod.description || ''}</small>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="perm-cb" style="width: 18px; height: 18px; cursor: pointer;" id="view_${mod.id}" data-module="${mod.id}" data-action="can_view" ${perm.can_view ? 'checked' : ''}>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="perm-cb" style="width: 18px; height: 18px; cursor: pointer;" id="create_${mod.id}" data-module="${mod.id}" data-action="can_create" ${perm.can_create ? 'checked' : ''}>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="perm-cb" style="width: 18px; height: 18px; cursor: pointer;" id="edit_${mod.id}" data-module="${mod.id}" data-action="can_edit" ${perm.can_edit ? 'checked' : ''}>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="perm-cb" style="width: 18px; height: 18px; cursor: pointer;" id="delete_${mod.id}" data-module="${mod.id}" data-action="can_delete" ${perm.can_delete ? 'checked' : ''}>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function selectAll() {
            $('.perm-cb').prop('checked', true);
        }

        function deselectAll() {
            $('.perm-cb').prop('checked', false);
        }

        function gatherPermissions() {
            const moduleMap = {};
            $('.perm-cb').each(function () {
                const moduleId = $(this).data('module');
                const action = $(this).data('action');
                if (!moduleMap[moduleId]) {
                    moduleMap[moduleId] = { module_id: moduleId };
                }
                moduleMap[moduleId][action] = $(this).is(':checked');
            });

            // Only include modules that have at least one permission checked
            return Object.values(moduleMap).filter(m =>
                m.can_view || m.can_create || m.can_edit || m.can_delete
            );
        }

        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            loadModules();

            $('#roleForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();
                $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

                const permissions = gatherPermissions();
                if (permissions.length === 0) {
                    $('#alert-container').html('<div class="alert alert-warning">Please select at least one module permission.</div>');
                    $('#btnSave').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Create Role');
                    return;
                }

                const payload = {
                    name: $('#roleName').val(),
                    user_type: $('#userType').val(),
                    description: $('#roleDescription').val(),
                    is_default: $('#isDefault').is(':checked'),
                    modules: permissions,
                };

                try {
                    await API.fetchWithAuth('admin/roles', {
                        method: 'POST',
                        body: JSON.stringify(payload),
                        headers: { 'Content-Type': 'application/json' },
                    });
                    window.location.href = 'list.php';
                } catch (error) {
                    $('#alert-container').html(`<div class="alert alert-danger">${error.message || 'Failed to create role'}</div>`);
                    $('#btnSave').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Create Role');
                }
            });
        });
    </script>
</body>

</html>
