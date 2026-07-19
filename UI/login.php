<?php
$pathCommonPrefix = 'common';
?>
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
    <link href="<?php echo $pathCommonPrefix; ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/custom.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/modern-theme.css" rel="stylesheet">
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
                            <h1 class="h4 mt-3 mb-2" style="font-weight: 600; color: var(--text-primary);">Affitrace
                            </h1>
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

                            <div id="recaptcha-container" class="form-group d-none">
                                <div id="login-recaptcha" style="display: flex; justify-content: center; margin-bottom: 15px;"></div>
                            </div>

                            <div id="alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnLogin">Sign In</button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a class="small" href="#" id="lnkForgotPassword">Forgot Password?</a>
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
    <script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Use ADMIN's api.js -->
    <script src="<?php echo $pathCommonPrefix; ?>/assets/js/api.js"></script>

    <script>
        $(document).ready(function () {
            let recaptchaWidgetId = null;

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

                // Check reCAPTCHA verification if visible
                let recaptchaToken = null;
                if ($('#recaptcha-container').is(':visible') && typeof grecaptcha !== 'undefined' && recaptchaWidgetId !== null) {
                    recaptchaToken = grecaptcha.getResponse(recaptchaWidgetId);
                    if (!recaptchaToken) {
                        $('#alert-container').html(`<div class="alert alert-warning">Please complete the reCAPTCHA verification.</div>`);
                        $('#btnLogin').prop('disabled', false).text('Sign In');
                        return;
                    }
                }

                try {
                    const data = await API.login(formData.email, formData.password, formData.user_type, formData.eai, recaptchaToken);

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

                        // Load branding details on login response to cache them
                        if (data.branding) {
                            localStorage.setItem('branding_settings', JSON.stringify(data.branding));
                        } else {
                            // Backup: load branding from API
                            await loadBranding(formData.eai);
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
                    if (typeof grecaptcha !== 'undefined' && recaptchaWidgetId !== null) {
                        grecaptcha.reset(recaptchaWidgetId);
                    }
                }
            });

            // Dynamic White-Label Settings Application
            async function loadBranding(agencyId) {
                if (!agencyId) return;
                try {
                    const response = await fetch(`${API_BASE_URL}/branding`, {
                        headers: {
                            'X-Agency-ID': agencyId,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        const data = await response.json();
                        if (data && data.status === 1 && data.branding) {
                            localStorage.setItem('branding_settings', JSON.stringify(data.branding));
                            applyBranding(data.branding);
                        }
                    }
                } catch (e) {
                    console.error('Failed to load branding:', e);
                }
            }

            function applyBranding(branding) {
                const name = branding.platform_name || 'Affitrace';
                const logo = branding.platform_logo;
                const primary = branding.primary_color || '#6366F1';
                const secondary = branding.secondary_color || '#1E293B';
                const accent = branding.accent_color || '#10B981';
                const favicon = branding.platform_favicon;
                const recaptchaSiteKey = branding.is_recaptcha_enabled ? branding.recaptcha_site_key : null;

                document.title = `Login - ${name}`;

                // Handle Google reCAPTCHA
                if (recaptchaSiteKey) {
                    $('#recaptcha-container').removeClass('d-none');
                    if (typeof grecaptcha === 'undefined') {
                        if (!$('script[src*="recaptcha/api.js"]').length) {
                            const script = document.createElement('script');
                            script.src = 'https://www.google.com/recaptcha/api.js?onload=onLoginRecaptchaLoad&render=explicit';
                            script.async = true;
                            script.defer = true;
                            document.head.appendChild(script);

                            window.onLoginRecaptchaLoad = function () {
                                renderRecaptcha(recaptchaSiteKey);
                            };
                        }
                    } else {
                        renderRecaptcha(recaptchaSiteKey);
                    }
                } else {
                    $('#recaptcha-container').addClass('d-none');
                    if (recaptchaWidgetId !== null) {
                        try {
                            grecaptcha.reset(recaptchaWidgetId);
                        } catch (e) {}
                    }
                    $('#login-recaptcha').empty();
                    recaptchaWidgetId = null;
                }

                const brandContainer = $('.text-center.mb-4');
                if (brandContainer.length > 0) {
                    if (logo) {
                        const logoUrl = `${API_BASE_URL}/../storage/branding/${logo}`;
                        brandContainer.html(`
                        <img src="${logoUrl}" alt="${name}" style="max-height: 80px; width: auto; max-width: 250px; margin-bottom: 1rem;">
                        <p class="text-muted">Sign in to your account</p>
                    `);
                    } else {
                        brandContainer.html(`
                        <i class="fab fa-hive" style="font-size: 3rem; color: var(--primary-main);"></i>
                        <h1 class="h4 mt-3 mb-2" style="font-weight: 600; color: var(--text-primary);">${name}</h1>
                        <p class="text-muted">Sign in to your account</p>
                    `);
                    }
                }

                let style = $('#dynamic-branding-styles');
                if (style.length === 0) {
                    style = $('<style id="dynamic-branding-styles"></style>').appendTo('head');
                }
                style.html(`
                :root {
                    --primary-main: ${primary} !important;
                    --primary-light: ${primary}ee !important;
                    --primary-dark: ${primary}dd !important;
                    --secondary-main: ${secondary} !important;
                    --secondary-light: ${secondary}ee !important;
                    --secondary-dark: ${secondary}dd !important;
                    --success-main: ${accent} !important;
                }
                .btn-primary {
                    background-color: ${primary} !important;
                    border-color: ${primary} !important;
                }
                .btn-primary:hover {
                    background-color: ${primary}ee !important;
                    border-color: ${primary}ee !important;
                }
            `);

                if (favicon) {
                    const faviconUrl = `${API_BASE_URL}/../storage/branding/${favicon}`;
                    let link = $("link[rel~='icon']");
                    if (link.length === 0) {
                        link = $('<link rel="icon">').appendTo('head');
                    }
                    link.attr('href', faviconUrl);
                }

                $('.text-center.mt-4 span').text(`© ${new Date().getFullYear()} ${branding.custom_copyright ?? (name + '. All rights reserved.')}`);
            }

            function renderRecaptcha(siteKey) {
                if (typeof grecaptcha !== 'undefined' && $('#login-recaptcha').is(':empty')) {
                    recaptchaWidgetId = grecaptcha.render('login-recaptcha', {
                        'sitekey': siteKey,
                        'theme': 'light'
                    });
                }
            }

            // Listen for Agency ID changes
            $('#eai').on('change blur keyup', function () {
                const eai = $(this).val().trim();
                if (eai.length >= 2) {
                    loadBranding(eai);
                }
            });

            // Initialize branding if agency ID is cached in localStorage
            const cachedEai = localStorage.getItem('agency_id');
            if (cachedEai) {
                $('#eai').val(cachedEai);
                loadBranding(cachedEai);
            }

            // Listen for Forgot Password click
            $('#lnkForgotPassword').on('click', function (e) {
                e.preventDefault();
                const eai = $('#eai').val().trim();
                window.location.href = eai ? `forgot_password.php?eai=${encodeURIComponent(eai)}` : 'forgot_password.php';
            });
        });





    </script>
</body>

</html>