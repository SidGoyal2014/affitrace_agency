<?php
$pathPrefix = '.';
$activePage = 'settings';
$pageTitle = 'Settings';
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
                <h1>Settings</h1>
            </div>

            <!-- Settings Cards Grid -->
            <div class="row">
                <!-- Timezone Settings Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-globe-americas mr-2 text-primary"></i>
                            <h6 class="m-0 font-weight-bold">Timezone Preference</h6>
                        </div>
                        <div class="card-body">
                            <div id="tz-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Set your preferred timezone. All dates and times across the dashboard 
                                (reports, logs, charts) will be displayed in this timezone.
                            </p>

                            <div class="form-group">
                                <label><strong>Your Timezone</strong></label>
                                <select name="timezone" id="timezone" class="form-control">
                                    <option value="">Loading...</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <small class="text-muted" id="tz-current-display">
                                    <i class="fas fa-clock mr-1"></i> Current: <span id="tz-current-label">—</span>
                                </small>
                                <button type="button" class="btn btn-primary" id="btnSaveTimezone">
                                    <i class="fas fa-save mr-1"></i> Save Timezone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-key mr-2 text-warning"></i>
                            <h6 class="m-0 font-weight-bold">Change Password</h6>
                        </div>
                        <div class="card-body">
                            <div id="pw-alert-container"></div>

                            <form id="passwordForm">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" id="current_password" class="form-control" placeholder="Enter current password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" id="new_password" class="form-control" placeholder="At least 6 characters" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" id="new_password_confirmation" class="form-control" placeholder="Confirm new password" required>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-warning" id="btnSavePassword">
                                        <i class="fas fa-save mr-1"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Payout Method settings Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-credit-card mr-2 text-success"></i>
                            <h6 class="m-0 font-weight-bold">Payout Method & Parameters</h6>
                        </div>
                        <div class="card-body">
                            <div id="payout-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Select your preferred payment method and enter details (e.g. PayPal email, bank info, or Crypto wallet address).
                            </p>

                            <form id="payoutForm">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select id="payment_method" class="form-control" required>
                                        <option value="">Select payout method...</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Wire Transfer">Wire Transfer / Bank Details</option>
                                        <option value="USDT (TRC20)">USDT (TRC20 Wallet)</option>
                                        <option value="Payoneer">Payoneer</option>
                                        <option value="WebMoney">WebMoney</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Payment Details</label>
                                    <textarea id="payment_details" class="form-control" rows="4" placeholder="Enter complete payment credentials (e.g. Account Number, IBAN, Swift Code, Wallet Address)" required></textarea>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success" id="btnSavePayout">
                                        <i class="fas fa-save mr-1"></i> Save Payout Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Telegram Notification settings Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fab fa-telegram-plane mr-2 text-info"></i>
                            <h6 class="m-0 font-weight-bold">Telegram Notifications</h6>
                        </div>
                        <div class="card-body">
                            <div id="tg-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Connect your Telegram Bot to receive automated real-time conversion notifications.
                            </p>

                            <form id="telegramForm">
                                <div class="form-group">
                                    <label>Telegram Bot Token</label>
                                    <input type="text" id="telegram_bot_token" class="form-control" placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                                </div>

                                <div class="form-group">
                                    <label>Telegram Chat ID</label>
                                    <input type="text" id="telegram_chat_id" class="form-control" placeholder="987654321">
                                    <small class="form-text text-muted">
                                        Send a message to <strong>@userinfobot</strong> on Telegram to retrieve your Chat ID.
                                    </small>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-info" id="btnSaveTelegram">
                                        <i class="fas fa-save mr-1"></i> Save Telegram Settings
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

    <!-- Timezone data -->
    <script src="<?php echo $pathPrefix; ?>/../common/js/timezones.js"></script>

    <script>
        $(document).ready(async function () {
            console.log('[Settings] Page initialised');

            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user && user.name) {
                $('.user-name').text(user.name);
            }

            // Populate Timezones
            const $tzSelect = $('#timezone');
            $tzSelect.empty().append('<option value="">Select your timezone...</option>');
            Object.entries(TIMEZONE_LIST).forEach(([key, label]) => {
                $tzSelect.append(`<option value="${key}">${label}</option>`);
            });

            if ($.fn.select2) {
                $tzSelect.select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Search for a timezone...' });
            }

            // Fetch Current Profile Settings & Timezone
            try {
                const profileRes = await API.fetchWithAuth('affiliate/profile');
                if (profileRes.user && profileRes.user.timezone) {
                    $tzSelect.val(profileRes.user.timezone).trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST[profileRes.user.timezone] || profileRes.user.timezone);
                } else {
                    $tzSelect.val('UTC').trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST['UTC']);
                }

                if (profileRes.aud) {
                    $('#payment_method').val(profileRes.aud.payment_method || '');
                    $('#payment_details').val(profileRes.aud.payment_details || '');
                    $('#telegram_bot_token').val(profileRes.aud.telegram_bot_token || '');
                    $('#telegram_chat_id').val(profileRes.aud.telegram_chat_id || '');
                }
            } catch (err) {
                console.error('[Settings] Failed to load settings:', err);
            }

            // Save Timezone Preference
            $('#btnSaveTimezone').on('click', async function () {
                const tz = $tzSelect.val();
                if (!tz) {
                    $('#tz-alert-container').html('<div class="alert alert-warning">Please select a timezone.</div>');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                $('#tz-alert-container').empty();

                try {
                    const result = await API.fetchWithAuth('update-timezone', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ timezone: tz })
                    });

                    if (result.timezone) {
                        const user = JSON.parse(localStorage.getItem('user') || '{}');
                        user.timezone = result.timezone;
                        localStorage.setItem('user', JSON.stringify(user));
                        $('#tz-current-label').text(TIMEZONE_LIST[result.timezone] || result.timezone);
                        $('#tz-alert-container').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Timezone updated successfully!</div>');
                    }
                } catch (err) {
                    $('#tz-alert-container').html(`<div class="alert alert-danger">${err.message || 'Failed to update timezone.'}</div>`);
                } finally {
                    $(this).prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Timezone');
                }
            });

            // Password Form submission
            $('#passwordForm').on('submit', async function (e) {
                e.preventDefault();
                $('#pw-alert-container').empty();

                const newPw = $('#new_password').val();
                const confirmPw = $('#new_password_confirmation').val();

                if (newPw.length < 6) {
                    $('#pw-alert-container').html('<div class="alert alert-danger">Password must be at least 6 characters.</div>');
                    return;
                }

                if (newPw !== confirmPw) {
                    $('#pw-alert-container').html('<div class="alert alert-danger">New passwords do not match.</div>');
                    return;
                }

                $('#btnSavePassword').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

                try {
                    const res = await API.fetchWithAuth('affiliate/profile/change-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            current_password: $('#current_password').val(),
                            password: newPw,
                            password_confirmation: confirmPw
                        })
                    });

                    $('#pw-alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
                    $('#passwordForm')[0].reset();
                } catch (err) {
                    let errMsg = err.message || 'Failed to change password.';
                    if (err.responseData && err.responseData.errors) {
                        errMsg = Object.values(err.responseData.errors).flat().join('<br>');
                    }
                    $('#pw-alert-container').html(`<div class="alert alert-danger">${errMsg}</div>`);
                } finally {
                    $('#btnSavePassword').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Password');
                }
            });

            // Payout Details Form submission
            $('#payoutForm').on('submit', async function (e) {
                e.preventDefault();
                $('#payout-alert-container').empty();
                $('#btnSavePayout').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                try {
                    const res = await API.fetchWithAuth('affiliate/payouts/method', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            payment_method: $('#payment_method').val(),
                            payment_details: $('#payment_details').val()
                        })
                    });

                    $('#payout-alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
                } catch (err) {
                    $('#payout-alert-container').html(`<div class="alert alert-danger">${err.message || 'Failed to save payout settings.'}</div>`);
                } finally {
                    $('#btnSavePayout').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Payout Details');
                }
            });

            // Telegram Settings Form submission
            $('#telegramForm').on('submit', async function (e) {
                e.preventDefault();
                $('#tg-alert-container').empty();
                $('#btnSaveTelegram').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                try {
                    const res = await API.fetchWithAuth('affiliate/settings/telegram', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            telegram_bot_token: $('#telegram_bot_token').val(),
                            telegram_chat_id: $('#telegram_chat_id').val()
                        })
                    });

                    $('#tg-alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
                } catch (err) {
                    $('#tg-alert-container').html(`<div class="alert alert-danger">${err.message || 'Failed to save Telegram settings.'}</div>`);
                } finally {
                    $('#btnSaveTelegram').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Telegram Settings');
                }
            });
        });
    </script>
</body>

</html>
