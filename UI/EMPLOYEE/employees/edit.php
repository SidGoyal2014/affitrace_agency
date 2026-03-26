<?php
$pathPrefix = '..';
$activePage = 'employees';
$pageTitle = 'Edit Employee';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>
    <main id="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1>Edit Employee</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-body">
                            <div id="alert-container"></div>
                            <form id="editForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name *</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control" id="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password (Leave blank to keep current)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role_id" id="role_id" class="form-control">
                                                <option value="">Select Role</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Designation</label>
                                            <select name="designation_id" id="designation_id" class="form-control">
                                                <option value="">Select Designation</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Employee</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>
    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            if (!PermissionGuard.check('role_management', 'can_edit')) return;
            
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            if(!id) { window.location.href = 'list.php'; return; }

            try {
                const rolesRes = await API.fetchWithAuth('employee/roles');
                if(rolesRes && rolesRes.roles && rolesRes.roles.length > 0) {
                    rolesRes.roles.forEach(r => {
                        if(r.user_type === 'employee') {
                            $('#role_id').append(`<option value="${r.id}">${r.name}</option>`);
                        }
                    });
                }
                
                const desigRes = await API.fetchWithAuth('employee/designations');
                if(desigRes && desigRes.designations && desigRes.designations.length > 0) {
                    desigRes.designations.forEach(d => {
                        $('#designation_id').append(`<option value="${d.id}">${d.name}</option>`);
                    });
                }

                // Load User
                const res = await API.fetchWithAuth(`employee/employees/edit/${id}`);
                const user = res.user;
                if(user) {
                    $('input[name="name"]').val(user.name);
                    $('input[name="email"]').val(user.email);
                    $('input[name="phone"]').val(user.phone);
                    $('#status').val(user.status);
                    $('#role_id').val(user.role_id);
                    $('#designation_id').val(user.designation_id);
                }
            } catch(e) {
                console.error("Failed to load");
            }

            $('#editForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();
                
                const payload = {
                    uid: id,
                    name: $('input[name="name"]').val(),
                    email: $('input[name="email"]').val(),
                    phone: $('input[name="phone"]').val(),
                    status: $('#status').val(),
                    role_id: $('#role_id').val(),
                    designation_id: $('#designation_id').val()
                };
                
                const pass = $('input[name="password"]').val();
                if(pass) payload.password = pass;

                try {
                    const response = await API.fetchWithAuth('employee/employees/update', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success">Updated! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        throw new Error(response.error || 'Failed');
                    }
                } catch (e) {
                    let errObj = typeof e.message === 'object' ? Object.values(e.message).flat().join('<br>') : e.message;
                    $('#alert-container').html(`<div class="alert alert-danger">${errObj}</div>`);
                }
            });
        });
    </script>
</body>
</html>
