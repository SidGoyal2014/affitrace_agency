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
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                                <div class="form-group mb-3">
                                    <label><strong>Name:</strong></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>Phone:</strong></label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>Email:</strong></label>
                                    <input type="email" name="email" id="email" class="form-control" readonly>
                                    <small class="form-text text-muted">Email cannot be changed.</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>Company:</strong></label>
                                    <input type="text" name="company" id="company" class="form-control">
                                </div>
                            </div>

                            <!-- IM & Regional -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">IM & Regional Settings</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label><strong>IM Platform:</strong></label>
                                            <input type="text" name="instant_messenger_name" id="instant_messenger_name" class="form-control" placeholder="e.g. Skype">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label><strong>IM Handle:</strong></label>
                                            <input type="text" name="instant_messenger_value" id="instant_messenger_value" class="form-control" placeholder="e.g. live:username">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>Time Zone:</strong></label>
                                    <select name="time_zone" id="time_zone" class="form-control"></select>
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>Preferred Currency:</strong></label>
                                    <select name="currency" id="currency" class="form-control"></select>
                                </div>
                                <div class="form-group mb-3">
                                    <label><strong>New Password:</strong></label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Address Information -->
                            <div class="col-md-12">
                                <h6 class="text-primary border-bottom pb-2">Address Information</h6>
                                <div class="form-group mb-3">
                                    <label><strong>Address:</strong></label>
                                    <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group mb-3">
                                            <label><strong>City:</strong></label>
                                            <input type="text" name="city" id="city" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group mb-3">
                                            <label><strong>State:</strong></label>
                                            <input type="text" name="state" id="state" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group mb-3">
                                            <label><strong>Country:</strong></label>
                                            <select name="country" id="country" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group mb-3">
                                            <label><strong>Zip Code:</strong></label>
                                            <input type="text" name="zipcode" id="zipcode" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSave">Update Profile</button>
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
            const loadProfile = async () => {
                try {
                    const data = await API.fetchWithAuth('advertiser/profile');
                    
                    // Populate Dropdowns
                    populateSelect('time_zone', data.arrTimeZone || {}, data.aud?.time_zone);
                    populateSelect('currency', data.arrCur || {}, data.aud?.currency);
                    populateSelect('country', data.arrCountry || {}, data.user?.country);

                    // Populate User Data
                    if (data.user) {
                        $('#name').val(data.user.name);
                        $('#email').val(data.user.email);
                        $('#phone').val(data.user.phone);
                        $('#company').val(data.user.company);
                        $('#instant_messenger_name').val(data.user.instant_messenger_name);
                        $('#instant_messenger_value').val(data.user.instant_messenger_value);
                        $('#address').val(data.user.address);
                        $('#city').val(data.user.city);
                        $('#state').val(data.user.state);
                        $('#zipcode').val(data.user.zipcode);
                        
                        // Update localStorage user object as well
                        localStorage.setItem('user', JSON.stringify(data.user));
                        $('.user-name').text(data.user.name);
                    }

                } catch (error) {
                    console.error('Failed to load profile:', error);
                    Toast.error('Error', 'Failed to load profile data');
                    $('#alert-container').html(`<div class="alert alert-danger">Failed to load profile data.</div>`);
                }
            };

            await loadProfile();

            // Handle Form Submission
            $('#profileForm').on('submit', async function (e) {
                e.preventDefault();
                $('#btnSave').prop('disabled', true).text('Updating...');
                $('#alert-container').empty();

                const formData = {};
                $(this).find('input, select, textarea').each(function() {
                    if (this.name) {
                        formData[this.name] = this.value;
                    }
                });

                try {
                    const result = await API.fetchWithAuth('advertiser/update_profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    if (result.status == 1) {
                        Toast.success('Success', 'Profile updated successfully');
                        $('#alert-container').html(`<div class="alert alert-success">Profile updated successfully!</div>`);
                        $('#password').val('');
                        
                        // Reload data to ensure everything is in sync
                        await loadProfile();
                        
                        // Scroll to top of form
                        $('html, body').animate({ scrollTop: $(".page-header").offset().top - 20 }, 500);
                    } else {
                        throw new Error(result.error ? JSON.stringify(result.error) : 'Update failed');
                    }

                } catch (error) {
                    console.error('Update failed:', error);
                    let msg = 'Failed to update profile.';
                    
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
                            msg = error.message; 
                        }
                    }
                    
                    $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                    Toast.error('Update Failed', 'Please check the errors above');
                } finally {
                    $('#btnSave').prop('disabled', false).text('Update Profile');
                }
            });
        });
    </script>
</body>

</html>