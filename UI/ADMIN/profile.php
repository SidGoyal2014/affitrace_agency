<?php
$pathPrefix = '.';
$activePage = 'profile';
$pageTitle = 'Profile';
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
                <h1>Profile Settings</h1>
            </div>

            <!-- Profile Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Update Your Information</h6>
                        </div>
                        <div class="card-body">
                            <div id="alert-container"></div>

                            <form name="frmProfile" method="post" action="#">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Your name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Your email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control"
                                                placeholder="Your phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>New Password <small class="text-muted">(leave blank to keep
                                                    current)</small></label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="New password">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            // Fetch Profile Data
            try {
                const data = await API.fetchWithAuth('admin/profile');
                if (data.user) {
                    $('input[name="name"]').val(data.user.name);
                    $('input[name="email"]').val(data.user.email);
                    $('input[name="phone"]').val(data.user.phone);
                }
            } catch (error) {
                console.error("Profile Load Error:", error);
            }

            // Handle Profile Update
            $('form[name="frmProfile"]').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();

                const formData = {
                    name: $('input[name="name"]').val(),
                    email: $('input[name="email"]').val(),
                    phone: $('input[name="phone"]').val(),
                    password: $('input[name="password"]').val()
                };

                try {
                    const response = await API.fetchWithAuth('admin/update_profile', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });

                    if (response.status === 1) {
                        $('#alert-container').html(`<div class="alert alert-success">${response.msg || 'Profile updated successfully'}</div>`);

                        if (user) {
                            user.name = formData.name;
                            localStorage.setItem('user', JSON.stringify(user));
                            $('.user-name').text(user.name);
                        }
                    } else {
                        throw new Error(response.error || 'Update failed');
                    }

                } catch (error) {
                    $('#alert-container').html(`<div class="alert alert-danger">${error.message}</div>`);
                }
            });
        });
    </script>
</body>

</html>