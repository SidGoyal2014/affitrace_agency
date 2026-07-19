<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Password Recovery - Affitrace</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../common/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Stylesheets -->
    <link href="../common/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="../common/assets/css/custom.css" rel="stylesheet">
    <link href="../common/assets/css/modern-theme.css" rel="stylesheet">
</head>

<body style="background: var(--bg-body); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card-modern" style="overflow: hidden;">
                    <div class="card-body p-5">

                        <!-- Brand -->
                        <div class="text-center mb-4">
                            <i class="fab fa-hive" style="font-size: 3rem; color: var(--primary-main);"></i>
                            <h1 class="h4 mt-3 mb-2" style="font-weight: 600; color: var(--text-primary);" id="pageTitle">Password Recovery</h1>
                            <p class="text-muted" id="pageSubtitle">Reset your account password securely</p>
                        </div>

                        <!-- Form 1: Request Password Reset Link -->
                        <form id="forgotForm">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
                            </div>

                            <div id="forgot-alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnForgot">Send Reset Link</button>
                        </form>

                        <!-- Form 2: Reset Password (loaded when token & email are in query params) -->
                        <form id="resetForm" style="display: none;">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" id="new_password" class="form-control" placeholder="At least 6 characters" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" id="confirm_password" class="form-control" placeholder="Confirm new password" required>
                            </div>

                            <div id="reset-alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnReset">Reset Password</button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a class="small font-weight-bold" href="login.php">Back to Sign In</a>
                        </div>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <span class="text-muted small">© 2024 Affitrace. All rights reserved.</span>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../common/assets/vendor/jquery/jquery.min.js"></script>
    <script src="../common/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../common/assets/js/api.js"></script>

    <script>
        let agencyId = 'demo';
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('eai')) {
            agencyId = urlParams.get('eai');
        }

        const token = urlParams.get('token');
        const resetEmail = urlParams.get('email');

        $(document).ready(function () {
            if (token && resetEmail) {
                // Show reset form
                $('#forgotForm').hide();
                $('#resetForm').show();
                $('#pageTitle').text('Set New Password');
                $('#pageSubtitle').text('Enter your new credentials below');

                $('#resetForm').on('submit', async function (e) {
                    e.preventDefault();
                    $('#reset-alert-container').empty();

                    const newPassword = $('#new_password').val();
                    const confirmPassword = $('#confirm_password').val();

                    if (newPassword.length < 6) {
                        $('#reset-alert-container').html('<div class="alert alert-danger">Password must be at least 6 characters long.</div>');
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        $('#reset-alert-container').html('<div class="alert alert-danger">Passwords do not match.</div>');
                        return;
                    }

                    $('#btnReset').prop('disabled', true).text('Updating...');

                    try {
                        const payload = {
                            email: resetEmail,
                            token: token,
                            password: newPassword,
                            password_confirmation: confirmPassword
                        };
                        const response = await API.resetPassword(agencyId, payload);
                        $('#reset-alert-container').html(`<div class="alert alert-success">${response.message} Redirecting to login...</div>`);
                        setTimeout(() => {
                            window.location.href = `login.php?eai=${agencyId}`;
                        }, 3000);
                    } catch (error) {
                        console.error('Password reset failed:', error);
                        $('#reset-alert-container').html(`<div class="alert alert-danger">${error.message}</div>`);
                    } finally {
                        $('#btnReset').prop('disabled', false).text('Reset Password');
                    }
                });
            } else {
                // Show forgot form
                $('#forgotForm').on('submit', async function (e) {
                    e.preventDefault();
                    $('#forgot-alert-container').empty();
                    $('#btnForgot').prop('disabled', true).text('Sending...');

                    const email = $('#email').val();

                    try {
                        const response = await API.forgotPassword(agencyId, email);
                        $('#forgot-alert-container').html(`<div class="alert alert-success">${response.message}</div>`);
                    } catch (error) {
                        console.error('Forgot password link request failed:', error);
                        $('#forgot-alert-container').html(`<div class="alert alert-danger">${error.message}</div>`);
                    } finally {
                        $('#btnForgot').prop('disabled', false).text('Send Reset Link');
                    }
                });
            }
        });
    </script>
</body>

</html>
