<?php
$pageTitle = 'Profile';
include 'includes/header.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="card-body">

        <form id="profileForm" method="post" action="#">

            <div id="alert-container"></div>

            <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="form-group row">
                        <div class="col-sm-12 mb-6 mb-sm-0">
                            <label for><strong>Name:</strong></label>
                            <input type="text" name="name" id="name" class="form-control form-control-user" required>
                        </div>

                        <div class="col-sm-12 mb-6 mb-sm-0">
                            <br>
                            <label><strong>Phone:</strong></label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-user">
                        </div>

                    </div>
                </div>
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="form-group row">
                        <div class="col-sm-12 mb-6 mb-sm-0">
                            <label><strong>Email:</strong></label>
                            <input type="email" name="email" id="email" class="form-control form-control-user" readonly>
                            <small class="form-text text-muted">Email cannot be changed directly.</small>
                        </div>
                        <div class="col-sm-12 mb-6 mb-sm-0">
                            <br>
                            <label><strong>New Password:</strong></label>
                            <input type="password" name="password" id="password" class="form-control form-control-user"
                                placeholder="Leave blank to keep current password">
                        </div>
                        <div class="col-sm-12 mb-6 mb-sm-0">
                            <br>
                            <button type="submit" class="btn btn-primary" id="btnSave">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>

</div>
<!-- /.container-fluid -->

<?php include 'includes/footer.php'; ?>

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