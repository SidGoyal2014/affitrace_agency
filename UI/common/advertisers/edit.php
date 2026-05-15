<?php
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/timezones.js"></script>
<script src="../../common/js/currencies.js"></script>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password *</label>
                                            <input type="password" name="password" id="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password *</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="divider"><span>Other details</span></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name </label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder=" " required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone *</label>
                                            <input type="text" name="phone" id="phone" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Instant Messenger Name </label>
                                            <input type="text" name="instant_messenger_name" id="instant_messenger_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Instant Messenger Value</label>
                                            <input type="text" name="instant_messenger_value" id="instant_messenger_value" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company </label>
                                            <input type="text" name="company" id="company" class="form-control" placeholder=" " required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" name="address" id="address" class="form-control" placeholder=" " >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>City </label>
                                            <input type="text" name="city" id="city" class="form-control" placeholder=" " >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>State</label>
                                            <input type="text" name="state" id="state" class="form-control" placeholder=" " >
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ZipCode </label>
                                            <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder=" " >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country *</label>
                                            <select name="country" id="country" class="form-control select2-country">
                                                <option value="">(All Countries)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency </label>
                                            <select name="currency" id="currency" class="form-control">
                                                <option value="">Select Currency</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Time Zone</label>
                                            <select name="time_zone" id="time_zone" class="form-control">
                                                <option value="">Select Timezone</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Affiliate Block </label>
                                            <input type="text" name="affiliate_block" id="affiliate_block" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Affiliate whitelist</label>
                                            <input type="text" name="affiliate_whitelist" id="affiliate_whitelist" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Private Note </label>
                                            <textarea name="private_note" id="private_note" class="form-control" placeholder=""></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Additional Info</label>
                                            <textarea name="additional_info" id="additional_info" class="form-control" placeholder=""></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Postback IP Whitelist </label>
                                            <input type="text" name="postback_ip_whitelist" id="postback_ip_whitelist" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tax Details</label>
                                            <input type="text" name="tax_details" id="tax_details" class="form-control" placeholder="">
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

            try {
                const currencySelect = $('select[name="currency"]');
                Object.entries(CURRENCIES).forEach(([code, name]) => {
                    currencySelect.append(new Option(name, code));
                });
            } catch(e) { console.error("Failed to load currencies"); }

            try {
                const timeZoneSelect = $('select[name="time_zone"]');
                Object.entries(TIMEZONE_LIST).forEach(([code, name]) => {
                    timeZoneSelect.append(new Option(name, code));
                });

            } catch(e) { console.error("Failed to load time zones"); }

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
                const response = await API.fetchWithAuth(`<?php echo $api_link_to_edit_advertiser;?>/${advertiserId}`);
                const data = response.user;
                const dataAdud = response.adud;

                console.log(response)


                if (data) {
                    $('#name').val(data.name || '');
                    $('#email').val(data.email || '');

                    $('#instant_messenger_name').val(data.instant_messenger_name || '');
                    $('#instant_messenger_value').val(data.instant_messenger_value || '');
                    $('#company').val(data.company || '');

                    $('#address').val(data.address || '');
                    $('#city').val(data.city || '');
                    $('#state').val(data.state || '');
                    $('#zipcode').val(data.zipcode || '');


                    $('#phone').val(data.phone || '');
                    $('#country').val(data.country || '');
                    $('#currency').val(dataAdud.currency || '');
                    $('#time_zone').val(dataAdud.time_zone || '');
                    $('#affiliate_block').val(dataAdud.affiliate_block || '');
                    $('#affiliate_whitelist').val(dataAdud.affiliate_whitelist || '');
                    $('#private_note').val(dataAdud.private_note || '');
                    $('#additional_info').val(dataAdud.additional_info || '');
                    $('#postback_ip_whitelist').val(dataAdud.postback_ip_whitelist || '');
                    $('#tax_details').val(dataAdud.tax_details || '');

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
                    instant_messenger_name:$('#instant_messenger_name').val(),
                    instant_messenger_value: $('#instant_messenger_value').val(),
                    company: $('#company').val(),
                    address: $('#address').val(),
                    city: $('#city').val(),
                    state: $('#state').val(),
                    zipcode: $('#zipcode').val(),                    
                    phone: $('#phone').val(),
                    country: $('#country').val(),
                    user_type: 'advertiser',
                    currency: $('#currency').val(),
                    time_zone: $('#time_zone').val(),
                    affiliate_block: $('#affiliate_block').val(),
                    affiliate_whitelist: $('#affiliate_whitelist').val(),
                    private_note: $('#private_note').val(),
                    additional_info: $('#additional_info').val(),
                    postback_ip_whitelist: $('#postback_ip_whitelist').val(),
                    tax_details: $('#tax_details').val(),
                    instant_messenger_name: $('#instant_messenger_name').val(),
                    instant_messenger_value: $('#instant_messenger_value').val(),
                };

                if (password) {
                    payload.password = password;
                    payload.password_confirmation = confirm;
                }

                try {
                    const response = await API.fetchWithAuth(`<?php echo $api_link_to_update_advertiser;?>`, {
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