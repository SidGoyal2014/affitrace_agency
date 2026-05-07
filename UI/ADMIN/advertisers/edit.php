<?php
$pathPrefix = '..';
$activePage = 'advertisers';
$pageTitle = 'Edit Advertiser';
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>
<style>
    .divider {
        display: flex;
        align-items: center;
        margin: 24px 0;
        color: #94a3b8;
        font-size: 13px;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .divider span {
        padding: 0 16px;
    }
</style>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Edit Advertiser</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-body">
                            <div id="alert-container"></div>
                            <form id="editForm">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder=" " required>
                                    
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                                </div>

                                <div class="row row-gap">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Company</label>
                                            <input type="text" name="company" id="company" class="form-control" placeholder=" ">                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" name="phone" id="phone" class="form-control" placeholder=" ">                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country" class="form-control select2-country">
                                                <option value="">(All Countries)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="divider"><span>Change Password (Optional)</span></div>

                                <div class="row row-gap">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder=" ">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password</label>
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" class="form-control" placeholder=" ">                                            
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="user_type" value="advertiser">

                                <button type="submit" id="submitBtn" class="btn btn-primary">Save Changes</button>
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
        let advertiserId = null;

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const countrySelect = $('select[name="country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });
            } catch(e) { console.error("Failed to load countries"); }

            // Get ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            advertiserId = urlParams.get('id');

            if (!advertiserId) {
                $('#alert-container').html('<div class="alert alert-danger alert-modern">No advertiser ID provided.</div>');
                return;
            }

            $('#userIdBadge').text('ID: ' + advertiserId);

            // Load data
            try {
                const response = await API.fetchWithAuth(`admin/advertisers/edit/${advertiserId}`);
                const data = response.user;

                if (data) {
                    $('#name').val(data.name || '');
                    $('#email').val(data.email || '');
                    $('#company').val(data.company || '');
                    $('#phone').val(data.phone || '');
                    $('#country').val(data.country || '');
                    $('#role_id').val(data.role_id);
                }
            } catch (error) {
                console.error('Load Error:', error);
                $('#alert-container').html('<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> Failed to load advertiser data.</div>');
            }

            // Handle Submit
            $('#editForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();

                const password = $('#password').val();
                const confirm = $('#password_confirmation').val();

                if (password && password !== confirm) {
                    $('#alert-container').html('<div class="alert alert-danger alert-modern">Passwords do not match.</div>');
                    return;
                }

                const btn = $('#submitBtn');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

                const payload = {
                    uid: advertiserId,
                    name: $('#name').val(),
                    email: $('#email').val(),
                    company: $('#company').val(),
                    phone: $('#phone').val(),
                    country: $('#country').val(),
                    user_type: 'advertiser',
                    role_id: $('#role_id').val(),
                    instant_messenger_name: 'Skype',
                    instant_messenger_value: 'N/A'
                };

                if (password) {
                    payload.password = password;
                    payload.password_confirmation = confirm;
                }

                try {
                    const response = await API.fetchWithAuth('admin/advertisers/update', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success alert-modern"><i class="fas fa-check-circle mr-2"></i> Advertiser updated successfully! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        let errorMsg = response.error;
                        if (typeof response.error === 'object') {
                            errorMsg = Object.values(response.error).flat().join('<br>');
                        }
                        throw new Error(errorMsg || 'Failed to update advertiser.');
                    }
                } catch (e) {
                    $('#alert-container').html(`<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> ${e.message}</div>`);
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
</body>

</html>