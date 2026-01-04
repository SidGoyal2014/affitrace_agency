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
                <h1>Profile</h1>
            </div>

            <div class="card-modern">
                <div class="card-body">

                    <form name="frmProfile" method="post" action="#">

                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-6 mb-sm-0">
                                        <label for><strong>Name:</strong></label>
                                        <input type="text" name="name" id="profile_name" value=""
                                            class="form-control form-control-user">
                                    </div>

                                    <div class="col-sm-12 mb-6 mb-sm-0">
                                        <br>
                                        <label><strong>Phone:</strong></label>
                                        <input type="text" name="phone" id="profile_phone" value=""
                                            class="form-control form-control-user">
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-6 mb-sm-0">
                                        <label><strong>Email:</strong></label>
                                        <input type="text" name="email" id="profile_email" value=""
                                            class="form-control form-control-user" readonly>
                                    </div>
                                    <div class="col-sm-12 mb-6 mb-sm-0">
                                        <br>
                                        <label><strong>New Password:</strong></label>
                                        <input type="password" name="password" id="profile_password" value=""
                                            class="form-control form-control-user"
                                            placeholder="Leave blank to keep current">
                                    </div>
                                    <div class="col-sm-12 mb-6 mb-sm-0">
                                        <br>
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(function () {
            // Load user from localStorage
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('#profile_name').val(user.name || '');
                $('#profile_email').val(user.email || '');
                $('#profile_phone').val(user.phone || '');
            }
        });
    </script>
</body>

</html>