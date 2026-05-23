<?php
$pathPrefix = '..';
$activePage = 'employees';
$pageTitle = 'Create Employee';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>
    <main id="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1>Create Employee</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-body">
                            <div id="alert-container"></div>
                            <form id="createForm">
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
                                            <label>Password *</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password *</label>
                                            <input type="password" name="password_confirmation" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role_id" id="role_id" class="form-control">
                                                <option value="">Select Role</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Designation</label>
                                            <select name="designation_id" id="designation_id" class="form-control">
                                                <option value="">Select Designation</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Employee</button>
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
            if (!PermissionGuard.check('role_management', 'can_create')) return;
            
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
            } catch(e) {
                console.error("Failed to load dropdowns");
            }

            $('#createForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();
                try {
                    const response = await API.fetchWithAuth('employee/employees/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: $('input[name="name"]').val(),
                            email: $('input[name="email"]').val(),
                            phone: $('input[name="phone"]').val(),
                            password: $('input[name="password"]').val(),
                            password_confirmation: $('input[name="password_confirmation"]').val(),
                            role_id: $('#role_id').val(),
                            designation_id: $('#designation_id').val()
                        })
                    });
                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success">Created! Redirecting...</div>');
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
