<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Affiliate Signup - Affitrace</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../common/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Stylesheets -->
    <link href="../common/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="../common/assets/css/custom.css" rel="stylesheet">
    <link href="../common/assets/css/modern-theme.css" rel="stylesheet">
</head>

<body style="background: var(--bg-body); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 0;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">

                <div class="card-modern" style="overflow: hidden;">
                    <div class="card-body p-5">

                        <!-- Brand -->
                        <div class="text-center mb-4">
                            <i class="fab fa-hive" style="font-size: 3rem; color: var(--primary-main);"></i>
                            <h1 class="h4 mt-3 mb-2" style="font-weight: 600; color: var(--text-primary);">Create Affiliate Account</h1>
                            <p class="text-muted">Join our high-performance marketing network</p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mb-4" style="height: 6px;">
                            <div id="registrationProgress" class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Step 1: Registration details -->
                        <form id="registerStep1Form">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Full Name</label>
                                    <input type="text" id="name" class="form-control" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email Address</label>
                                    <input type="email" id="email" class="form-control" placeholder="john@example.com" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Password</label>
                                    <input type="password" id="password" class="form-control" placeholder="At least 6 characters" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" id="phone" class="form-control" placeholder="+1 234 567 8900" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Country / Region</label>
                                    <select id="country" class="form-control" required>
                                        <option value="">Select country...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Traffic Source Profile</label>
                                <select id="traffic_source" class="form-control" required>
                                    <option value="">Select primary traffic source...</option>
                                    <option value="Social">Social Media (FB, TikTok, Instagram)</option>
                                    <option value="Search">Search Engine Marketing (PPC)</option>
                                    <option value="SEO">SEO / Organic Blog</option>
                                    <option value="Email">Email Marketing</option>
                                    <option value="Push">Push Notifications</option>
                                    <option value="Pop">Pop Traffic (Popunder, Redirect)</option>
                                    <option value="Native">Native Ads</option>
                                    <option value="Display">Display Banner Ads</option>
                                    <option value="Influencer">Influencer / KOL</option>
                                    <option value="Other">Other / Multi-source</option>
                                </select>
                            </div>

                            <!-- Terms & conditions and Privacy Policy -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" id="terms" required>
                                    <label class="custom-control-label" for="terms">
                                        I accept the <a href="#" data-toggle="modal" data-target="#termsModal">Terms & Conditions</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Google reCAPTCHA Container -->
                            <div class="form-group d-flex justify-content-center">
                                <div id="recaptcha-container"></div>
                            </div>

                            <div id="step1-alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnStep1">Send Verification Code</button>
                        </form>

                        <!-- Step 2: OTP Verification -->
                        <form id="registerStep2Form" style="display: none;">
                            <div class="text-center mb-4">
                                <p class="text-muted">We have sent a 6-digit verification code to <strong id="verificationEmailDisplay"></strong>. It will expire in 10 minutes.</p>
                            </div>

                            <div class="form-group">
                                <label class="text-center d-block">Enter Verification Code</label>
                                <input type="text" id="otp" class="form-control text-center font-weight-bold" placeholder="000000" maxlength="6" style="font-size: 1.5rem; letter-spacing: 0.5rem;" required>
                            </div>

                            <div id="step2-alert-container"></div>

                            <button type="submit" class="btn btn-primary btn-block" id="btnStep2">Verify & Create Account</button>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-link btn-sm" id="btnResend" disabled>Resend Code (<span id="cooldownTimer">60</span>s)</button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <span class="text-muted">Already have an account?</span>
                            <a class="small font-weight-bold ml-1" href="login.php">Sign In</a>
                        </div>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <span class="text-muted small">© 2024 Affitrace. All rights reserved.</span>
                </div>

            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms & Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>By registering on AffiTrace, you agree to comply with all our publisher policies. Spamming, fraudulent traffic generation, and use of restricted creatives will lead to immediate account termination and forfeiture of any pending payouts.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Privacy Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>We respect your privacy. All registration details and payment parameters are securely stored and encrypted. We do not sell your personal data to third parties.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../common/assets/vendor/jquery/jquery.min.js"></script>
    <script src="../common/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../common/assets/js/api.js"></script>

    <script>
        let recaptchaWidgetId = null;
        let agencyId = 'demo'; // Default agency ID or parse from query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('eai')) {
            agencyId = urlParams.get('eai');
        }

        $(document).ready(async function () {
            // Load branding configuration to fetch reCAPTCHA site key
            let siteKey = '';
            try {
                const brandRes = await fetch(`${API.BASE_URL}/branding`, {
                    headers: { 'X-Agency-ID': agencyId }
                });
                const brandData = await brandRes.json();
                if (brandData.status && brandData.branding.recaptcha_site_key) {
                    siteKey = brandData.branding.recaptcha_site_key;
                }
            } catch (e) {
                console.error("Failed to load branding:", e);
            }

            // Load Google reCAPTCHA script
            if (siteKey) {
                const script = document.createElement('script');
                script.src = `https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);

                window.onloadCallback = function () {
                    recaptchaWidgetId = grecaptcha.render('recaptcha-container', {
                        'sitekey': siteKey
                    });
                };
            } else {
                $('#recaptcha-container').html('<p class="text-danger small">reCAPTCHA configuration missing.</p>');
            }

            // Populate countries
            try {
                const countries = await API.getCountries(agencyId);
                const countrySelect = $('#country');
                $.each(countries, function (code, name) {
                    countrySelect.append(new Option(name, name));
                });
            } catch (error) {
                console.error('Failed to load countries:', error);
            }

            // Step 1 Form Submit
            $('#registerStep1Form').on('submit', async function (e) {
                e.preventDefault();
                $('#step1-alert-container').empty();

                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (password.length < 6) {
                    $('#step1-alert-container').html('<div class="alert alert-danger">Password must be at least 6 characters long.</div>');
                    return;
                }

                if (password !== confirmPassword) {
                    $('#step1-alert-container').html('<div class="alert alert-danger">Passwords do not match.</div>');
                    return;
                }

                let recaptchaToken = '';
                if (siteKey && typeof grecaptcha !== 'undefined') {
                    recaptchaToken = grecaptcha.getResponse(recaptchaWidgetId);
                    if (!recaptchaToken) {
                        $('#step1-alert-container').html('<div class="alert alert-danger">Please complete the reCAPTCHA verification.</div>');
                        return;
                    }
                }

                $('#btnStep1').prop('disabled', true).text('Processing...');

                const signupData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    password: password,
                    password_confirmation: confirmPassword,
                    phone: $('#phone').val(),
                    country: $('#country').val(),
                    traffic_source: $('#traffic_source').val(),
                    terms: true,
                    recaptcha_token: recaptchaToken
                };

                try {
                    const result = await API.register(agencyId, signupData);
                    if (result.status === 'otp_sent') {
                        // Move to Step 2
                        $('#registerStep1Form').hide();
                        $('#registerStep2Form').show();
                        $('#registrationProgress').css('width', '100%');
                        $('#verificationEmailDisplay').text(signupData.email);
                        startResendCooldown();
                    }
                } catch (error) {
                    console.error('Registration Step 1 Failed:', error);
                    let errMsg = error.message || 'An error occurred. Please try again.';
                    $('#step1-alert-container').html(`<div class="alert alert-danger">${errMsg}</div>`);
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset(recaptchaWidgetId);
                    }
                } finally {
                    $('#btnStep1').prop('disabled', false).text('Send Verification Code');
                }
            });

            // Step 2 Form Submit
            $('#registerStep2Form').on('submit', async function (e) {
                e.preventDefault();
                $('#step2-alert-container').empty();
                $('#btnStep2').prop('disabled', true).text('Verifying...');

                const email = $('#email').val();
                const otp = $('#otp').val();

                try {
                    const result = await API.verifyEmailOtp(agencyId, email, otp);
                    $('#step2-alert-container').html(`<div class="alert alert-success">${result.message} Redirecting to login...</div>`);
                    setTimeout(() => {
                        window.location.href = `login.php?eai=${agencyId}`;
                    }, 3000);
                } catch (error) {
                    console.error('Registration Step 2 Failed:', error);
                    let errMsg = error.message || 'Verification failed. Please check the code.';
                    $('#step2-alert-container').html(`<div class="alert alert-danger">${errMsg}</div>`);
                } finally {
                    $('#btnStep2').prop('disabled', false).text('Verify & Create Account');
                }
            });

            // Resend Code Cooldown Timer
            let cooldownInterval = null;
            function startResendCooldown() {
                let seconds = 60;
                $('#btnResend').prop('disabled', true).text(`Resend Code (${seconds}s)`);

                if (cooldownInterval) clearInterval(cooldownInterval);

                cooldownInterval = setInterval(() => {
                    seconds--;
                    if (seconds <= 0) {
                        clearInterval(cooldownInterval);
                        $('#btnResend').prop('disabled', false).text('Resend Code');
                    } else {
                        $('#btnResend').text(`Resend Code (${seconds}s)`);
                    }
                }, 1000);
            }

            // Resend Code Button Action
            $('#btnResend').on('click', async function () {
                $('#step2-alert-container').empty();
                $('#btnResend').prop('disabled', true).text('Sending...');

                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                const signupData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    password: password,
                    password_confirmation: confirmPassword,
                    phone: $('#phone').val(),
                    country: $('#country').val(),
                    traffic_source: $('#traffic_source').val(),
                    terms: true,
                    recaptcha_token: 'dummy_bypass_for_resend' // Server caches the original recaptcha validation anyway
                };

                try {
                    await API.register(agencyId, signupData);
                    $('#step2-alert-container').html('<div class="alert alert-success">Verification code resent successfully.</div>');
                    startResendCooldown();
                } catch (error) {
                    $('#step2-alert-container').html(`<div class="alert alert-danger">${error.message}</div>`);
                    $('#btnResend').prop('disabled', false).text('Resend Code');
                }
            });
        });
    </script>
</body>

</html>
