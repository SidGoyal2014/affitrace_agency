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

            <!-- Profile Card -->
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Edit Profile</h6>
                </div>
                <div class="card-body">

                    <form id="profileForm" method="post" action="#">

                        <div id="alert-container"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Name:</strong></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Phone:</strong></label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Email:</strong></label>
                                    <input type="email" name="email" id="email" class="form-control" readonly>
                                    <small class="form-text text-muted">Email cannot be changed directly.</small>
                                </div>

                                <div class="form-group">
                                    <label><strong>New Password:</strong></label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Leave blank to keep current password">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="btnSave">Save Changes</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {

            // Load Profile Data
            try {
                const data = await API.fetchWithAuth('affiliate/profile');
                if (data.user) {
                    $('#name').val(data.user.name);
                    $('#email').val(data.user.email);
                }
                if (data.aud) {
                    $('#phone').val(data.aud.phone);
                }

            } catch (error) {
                console.error('Failed to load profile:', error);
                $('#alert-container').html(`<div class="alert alert-danger">Failed to load profile data.</div>`);
            }


            // Handle Form Submission
            $('#profileForm').on('submit', async function (e) {
                e.preventDefault();
                $('#btnSave').prop('disabled', true).text('Saving...');
                $('#alert-container').empty();

                const formData = {
                    name: $('#name').val(),
                    phone: $('#phone').val(),
                    password: $('#password').val()
                };

                try {
                    const response = await API.fetchWithAuth('affiliate/update_profile', {
                        method: 'POST',
                        body: JSON.stringify(formData)
                    });

                    $('#alert-container').html(`<div class="alert alert-success">Profile updated successfully!</div>`);

                    // Clear password field
                    $('#password').val('');

                } catch (error) {
                    console.error('Update failed:', error);
                    let msg = 'Failed to update profile.';
                    if (error.responseJSON && error.responseJSON.error) {
                        msg = error.responseJSON.error;
                    }
                    $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                } finally {
                    $('#btnSave').prop('disabled', false).text('Save Changes');
                }
            });

        });
    </script>
</body>

</html>