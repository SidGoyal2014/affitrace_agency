<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login - Affitrace</title>

    <!-- Custom fonts for this template-->
    <link href="ADMIN/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="ADMIN/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="ADMIN/assets/css/custom.css" rel="stylesheet">
    <link href="ADMIN/assets/css/modern-theme.css" rel="stylesheet">
</head>

<body
    style="background: var(--bg-body); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card-modern" style="overflow: hidden;">
                    <div class="card-body p-5">

                        <!-- Brand -->
                        <div class="text-center mb-4">
                            <i class="fab fa-hive" style="font-size: 3rem; color: var(--primary-main);"></i>
                            <h1 class="h4 mt-3 mb-2" style="font-weight: 600; color: var(--text-primary);">Affitrace</h1>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        <form class="user" id="loginForm">
                            <div class="form-group">
                                <label>Log In As</label>
                                <select name="user_type" class="form-control" id="user_type" required 
                                    style="border-radius: 0.5rem; height: calc(1.5em + 1rem + 2px);">
                                    <option value="" disabled selected>Select User Type</option>
                                    <option value="admin">Administrator</option>
                                    <option value="affiliate">Affiliate</option>
                                    <option value="advertiser">Advertiser</option>
                                    <option value="employee">Employee</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Agency ID</label>
                                <input type="text" name="eai" class="form-control" id="eai"
                                    placeholder="Enter Agency ID" autocomplete="off" data-lpignore="true" required>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Enter your password" required>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" id="customCheck">
                                    <label class="custom-control-label" for="customCheck">Remember Me</label>
                                </div>
                            </div>

                            <div id="alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnLogin">Sign In</button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a class="small" href="#">Forgot Password?</a>
                        </div>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <span class="text-muted small">© 2024 Affitrace. All rights reserved.</span>
                </div>

            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="ADMIN/assets/vendor/jquery/jquery.min.js"></script>
    <script src="ADMIN/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Use ADMIN's api.js -->
    <script src="ADMIN/assets/js/api.js"></script>

    <script>
        $(document).ready(function () {
            
            $('#loginForm').on('submit', async function (e) {
                e.preventDefault();
                $('#btnLogin').prop('disabled', true).text('Signing in...');
                $('#alert-container').empty();

                const formData = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    user_type: $('#user_type').val(),
                    eai: $('#eai').val()
                };

                if (!formData.user_type) {
                     $('#alert-container').html(`<div class="alert alert-warning">Please select a user type.</div>`);
                     $('#btnLogin').prop('disabled', false).text('Sign In');
                     return;
                }

                try {
                    // API.login expects arguments, not object, based on previous file viewing
                    // API.login signature: async (email, password, user_type, eai)
                    const data = await API.login(formData.email, formData.password, formData.user_type, formData.eai);

                    // Check response for redirect logic
                    if (data && data.user) {
                        // Store permissions for sidebar filtering
                        if (data.permissions) {
                            localStorage.setItem('permissions', JSON.stringify(data.permissions));
                        }

                        if (data.user.user_type === 'affiliate') {
                            window.location.href = 'AFFILIATE/dashboard.php';
                        } else if (data.user.user_type === 'advertiser') {
                            window.location.href = 'ADVERTISER/dashboard.php';
                        } else if (data.user.user_type === 'employee') {
                            window.location.href = 'EMPLOYEE/dashboard.php';
                        } else {
                            // Admin goes to ADMIN panel
                            window.location.href = 'ADMIN/dashboard.php';
                        }
                    } else {
                         throw new Error('Login failed: Invalid response');
                    }

                } catch (error) {
                    console.error('Login failed:', error);
                    let msg = 'Invalid credentials. Please try again.';
                    if (error.message) {
                        msg = error.message;
                    }
                    $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                } finally {
                    $('#btnLogin').prop('disabled', false).text('Sign In');
                }
            });
        });
    </script>
</body>

</html>