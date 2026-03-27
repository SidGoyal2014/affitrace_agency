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
                        <input type="hidden" name="uid" id="uid">
                        <div id="alert-container"></div>

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                                <div class="form-group">
                                    <label><strong>Name:</strong></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label><strong>Phone:</strong></label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label><strong>Email:</strong></label>
                                    <input type="email" name="email" id="email" class="form-control" readonly>
                                    <small class="form-text text-muted">Email cannot be changed.</small>
                                </div>
                                <div class="form-group">
                                    <label><strong>Company:</strong></label>
                                    <input type="text" name="company" id="company" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label><strong>Job Title:</strong></label>
                                    <input type="text" name="job_title" id="job_title" class="form-control">
                                </div>
                            </div>

                            <!-- IM & Credentials -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">IM & Credentials</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>IM Platform:</strong></label>
                                            <input type="text" name="instant_messenger_name" id="instant_messenger_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>IM Handle:</strong></label>
                                            <input type="text" name="instant_messenger_value" id="instant_messenger_value" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>New Password:</strong></label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
                                </div>
                                <div class="form-group">
                                    <label><strong>Preferred Currency:</strong></label>
                                    <select name="currency" id="currency" class="form-control"></select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Time Zone:</strong></label>
                                    <select name="time_zone" id="time_zone" class="form-control"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Tax Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">Tax Information</h6>
                                <div class="form-group">
                                    <label><strong>Tax Display Name:</strong></label>
                                    <input type="text" name="add_tax_basic_name" id="add_tax_basic_name" class="form-control" placeholder="e.g. GST/VAT">
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>Tax Type:</strong></label>
                                            <input type="text" name="add_tax_type" id="add_tax_type" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>Tax Number:</strong></label>
                                            <input type="text" name="add_tax_number" id="add_tax_number" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>Tax Percentage (%):</strong></label>
                                    <input type="number" name="add_tax" id="add_tax" class="form-control" step="0.01" min="0">
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">Address Information</h6>
                                <div class="form-group">
                                    <label><strong>Address:</strong></label>
                                    <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>City:</strong></label>
                                            <input type="text" name="city" id="city" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>State:</strong></label>
                                            <input type="text" name="state" id="state" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>Country:</strong></label>
                                            <select name="country" id="country" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><strong>Zip Code:</strong></label>
                                            <input type="text" name="zipcode" id="zipcode" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Payment Details -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">Payment Details</h6>
                                <div class="form-group">
                                    <label><strong>Payment Method:</strong></label>
                                    <select name="payment_method" id="payment_method" class="form-control"></select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Payment Details (Bank/Wallet):</strong></label>
                                    <textarea name="payment_details" id="payment_details" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSave">Save Profile Changes</button>
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
            // Dropdown Helpers
            const populateSelect = (id, data, selectedValue) => {
                const $select = $(`#${id}`);
                $select.empty().append('<option value="">Select...</option>');
                Object.entries(data).forEach(([key, val]) => {
                    $select.append(`<option value="${key}" ${key == selectedValue ? 'selected' : ''}>${val}</option>`);
                });
            };

            // Load Profile Data
            try {
                const data = await API.fetchWithAuth('affiliate/profile');
                
                // Populate Dropdowns
                populateSelect('time_zone', data.arrTimeZone || {}, data.aud?.time_zone);
                populateSelect('currency', data.arrCur || {}, data.aud?.currency);
                populateSelect('country', data.arrCountry || {}, data.user?.country);
                populateSelect('payment_method', data.pmtArray || {}, data.aud?.payment_method);

                // Populate User Data
                if (data.user) {
                    $('#uid').val(data.user.id);
                    $('#name').val(data.user.name);
                    $('#email').val(data.user.email);
                    $('#phone').val(data.user.phone);
                    $('#company').val(data.user.company);
                    $('#job_title').val(data.user.job_title);
                    $('#instant_messenger_name').val(data.user.instant_messenger_name);
                    $('#instant_messenger_value').val(data.user.instant_messenger_value);
                    $('#address').val(data.user.address);
                    $('#city').val(data.user.city);
                    $('#state').val(data.user.state);
                    $('#zipcode').val(data.user.zipcode);
                }

                // Populate Detail Data
                if (data.aud) {
                    $('#payment_details').val(data.aud.payment_details);
                    $('#add_tax_basic_name').val(data.aud.add_tax_basic_name);
                    $('#add_tax_type').val(data.aud.add_tax_type);
                    $('#add_tax_number').val(data.aud.add_tax_number);
                    $('#add_tax').val(data.aud.add_tax);
                }

            } catch (error) {
                console.error('Failed to load profile:', error);
                $('#alert-container').html(`<div class="alert alert-danger">Failed to load profile data.</div>`);
            }

            // Handle Form Submission
            $('#profileForm').on('submit', async function (e) {
                e.preventDefault();
                $('#btnSave').prop('disabled', true).text('Saving Changes...');
                $('#alert-container').empty();

                // Collect all form data robustly
                const formData = {};
                $(this).find('input, select, textarea').each(function() {
                    if (this.name) {
                        formData[this.name] = this.value;
                    }
                });
                console.log('Sending Profile Update:', formData);

                try {
                    const result = await API.fetchWithAuth('affiliate/profile', {
                        method: 'POST',
                        body: JSON.stringify(formData)
                    });

                    if (result.status == 1) {
                        $('#alert-container').html(`<div class="alert alert-success">Profile updated successfully!</div>`);
                        $('#password').val('');
                        // Scroll to top of form
                        $('html, body').animate({ scrollTop: $("#profileForm").offset().top - 100 }, 500);
                    } else {
                        throw new Error(result.error ? JSON.stringify(result.error) : 'Update failed');
                    }

                } catch (error) {
                    console.error('Update failed:', error);
                    let msg = 'Failed to update profile.';
                    
                    // Try to extract server error message
                    if (error.message) {
                        try {
                            const errData = JSON.parse(error.message);
                            if (typeof errData === 'object') {
                                msg = '<strong>Validation Errors:</strong><ul>';
                                Object.values(errData).forEach(errs => {
                                    msg += `<li>${errs[0]}</li>`;
                                });
                                msg += '</ul>';
                            } else {
                                msg = errData;
                            }
                        } catch(e) { 
                            // If not JSON, use the message directly
                            msg = error.message; 
                        }
                    } else if (error.statusText) {
                        msg = `Server Error: ${error.statusText}`;
                    }
                    
                    $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                } finally {
                    $('#btnSave').prop('disabled', false).text('Save Profile Changes');
                }
            });
        });
    </script>
</body>
</html>
</body>

</html>