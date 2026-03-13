<?php
$pathPrefix = '..';
$activePage = 'affiliates';
$pageTitle = 'Create Affiliate';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Create Affiliate</h1>
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
                                            <label>Phone *</label>
                                            <input type="text" name="phone" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company *</label>
                                            <input type="text" name="company" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <input type="text" name="country" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>User Type</label>
                                            <input type="text" class="form-control" value="Affiliate" disabled>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password *</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password *</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Affiliate</button>
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
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const rolesRes = await API.fetchWithAuth('admin/roles');
                if(rolesRes && rolesRes.length > 0) {
                    rolesRes.forEach(r => {
                        if(r.user_type === 'affiliate') {
                            $('#role_id').append(`<option value="${r.id}">${r.name}</option>`);
                        }
                    });
                }
            } catch(e) { console.error("Failed to load roles"); }

            $('#createForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();
                try {
                    const response = await API.fetchWithAuth('admin/affiliates/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: $('input[name="name"]').val(),
                            email: $('input[name="email"]').val(),
                            phone: $('input[name="phone"]').val(),
                            company: $('input[name="company"]').val(),
                            country: $('input[name="country"]').val(),
                            password: $('input[name="password"]').val(),
                            password_confirmation: $('input[name="password_confirmation"]').val(),
                            user_type: 'affiliate',
                            role_id: $('#role_id').val()
                        })
                    });
                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success">Created! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        throw new Error(response.error || 'Failed');
                    }
                } catch (e) {
                    $('#alert-container').html(`<div class="alert alert-danger">${e.message}</div>`);
                }
            });
        });
    </script>
</body>

</html>