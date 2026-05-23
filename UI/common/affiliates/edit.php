<?php
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>
<script src="../../common/js/timezones.js"></script>
<script src="../../common/js/currencies.js"></script>
<script src="../../common/js/pmt_methods.js"></script>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="page-header">
                <h1>Edit Affiliate updated</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>


            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary"><span style='color:#f00;'>* marked values are mandatory</span></h6>
                </div>


                <div class="card-body">

                    <form class="user" method='post' action="#">

                        <div class="form-group row">

                            <div class="col-lg-6">
                                <label for="inputName">Name <span style='color:#f00;'>*</span></label>
                                <input type="text" name="name" class="form-control form-control" id="inputName"
                                    aria-describedby="nameHelp" placeholder="Enter Name" value="">
                                <span style='color:#f00;'></span>
                            </div>
                            <div class="col-lg-6">
                                <label for="idInputPhone">Phone</label>
                                <input type="text" name="phone" class="form-control form-control" id="idInputPhone"
                                    placeholder="Enter phone no" value="">
                                <span style='color:#f00;'></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label for="exampleInputEmail">Email <span style='color:#f00;'>*</span></label>
                                <input type="email" name="email" class="form-control form-control" id="exampleInputEmail"
                                    aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                    value="">
                                <span style='color:#f00;'></span>
                            </div>
                            <div class="col-lg-6">
                                <label>Status <span style='color:#f00;'>*</span></label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="statusActive" name="status" class="custom-control-input"
                                        value="active">
                                    <label class="custom-control-label" for="statusActive">Active</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="statusInactive" name="status" class="custom-control-input"
                                        value="suspended">
                                    <label class="custom-control-label" for="statusInactive">Inactive</label>
                                </div>
                                <span style='color:#f00;'></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                 <label for="inputPassword">Password <span style='color:#f00;'>*</span></label>
                                <input type="password" name="password" class="form-control"
                                    id="inputPassword" placeholder="Enter Password">
                                <span style='color:#f00;'></span>
                            </div>
                            <div class="col-lg-6">
                                <label for="inputConfirmPassword">Confirm Password <span style='color:#f00;'>*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    id="inputConfirmPassword" placeholder="Confirm password">
                                <span style='color:#f00;'></span>
                            </div>
                        </div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Company Name:</strong><span style="color:#f00;">*</span></label>
								<input type="text" name="company" value="" class="form-control">
								<span style="color:#f00;"></span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Job Title:</strong><span style="color:#f00;">*</span></label>
								<input type="text" name="job_title" value="" class="form-control">
								<span style="color:#f00;"></span>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Instant Messenger Name:</strong><span style="color:#f00;">*</span></label>
								<input type="text" name="instant_messenger_name" value="" class="form-control">
								<span style="color:#f00;"></span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Instant Messenger Value:</strong><span style="color:#f00;">*</span></label>
								<input type="text" name="instant_messenger_value" value="" class="form-control">
								<span style="color:#f00;"></span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Address:</strong></label>
								<input type="text" name="address" value="aswdc asd" class="form-control">
								<span style="color:#f00;"></span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>City:</strong></label>
								<input type="text" name="city" value="AMBALA" class="form-control">
								<span style="color:#f00;"></span>
							</div>
						</div>



                        <div class="form-group row">
                            <div class="col-lg-6">
								<label><strong>State</strong></label>
								<input type="text" name="state" value="Haryana" class="form-control">
								<span style="color:#f00;"></span>
							</div>
                            <div class="col-lg-6">
                                <label for="idInputCountry">Country</label>
                                <select name="country" class="form-control select2-country" id="idInputCountry">
                                    <option value="">(All Countries)</option>
                                </select>
                                <span style='color:#f00;'></span>
                            </div>
                        </div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Zip Code</strong></label>
								<input type="text" name="zipcode" value="134003" class="form-control">
								<span style="color:#f00;"></span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Currency</strong></label>
								<select name="currency" class="form-control">
                                    <option value="">Select Currency</option>
                                </select>
								<span style="color:#f00;"></span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Additional Tracking URL Tokens</strong></label>
								<input type="text" name="additional_tracking_url_tokens" value="" class="form-control">

								<span style="color:#f00;"></span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Time Zone</strong></label>
								<select name="time_zone" class="form-control">
                                    <option value="">Select Timezone</option>
								</select>
								<span style="color:#f00;"></span>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-sm-1"><label><strong>Payment</strong></label></div>
							<div class="col-sm-5">
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label>Method</label>
										<select name="payment_method" class="form-control">
                                            <option value="">Select Options</option></select>
										<span style="color:#f00;"></span>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label>Details</label>
										<textarea name="payment_details" rows="5" class="form-control"></textarea>
										<span style="color:#f00;"></span>
									</div>
								</div>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Billing Terms</strong></label>
								<textarea name="billing_terms" rows="8" class="form-control"></textarea>
								<span style="color:#f00;"></span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-1"><label><strong>Tax details</strong></label></div>

							<div class="col-sm-11">
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Add Tax Basic Name (GST/VAT)</label>
										<input type="text" name="add_tax_basic_name" value="" class="form-control">
										<span style="color:#f00;"></span>
									</div>

									<div class="col-sm-3">
										<label>Add Tax Type (IGST/VAT)</label>
										<input type="text" name="add_tax_type" value="" class="form-control">
										<span style="color:#f00;"></span>
									</div>
									<div class="col-sm-3">
										<label>Add Tax Number</label>
										<input type="text" name="add_tax_number" value="" class="form-control">
										<span style="color:#f00;"></span>
									</div>
									<div class="col-sm-3">
										<label>Add Tax % (18.00)</label>
										<input type="text" name="add_tax" value="" class="form-control">
										<span style="color:#f00;"></span>
									</div>

								</div>
							</div>

						</div>


                        <div class="row">
                            <div class="form-group col-lg-12">
                                <input type="submit" value="Update" class="btn btn-primary" style="float:right;">
                            </div>
                        </div>

                        <input type="hidden" name="user_type" value="affiliate">
                    </form>

                </div>

            </div>

        </div>
        <!-- /.container-fluid -->
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>







    <script>
        $(document).ready(async function () {
            // Check auth
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            // Navbar User Info
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('#userDropdown span').text(user.name);
            }

            /*try {
                const rolesRes = await API.fetchWithAuth('admin/roles');
                if(rolesRes && rolesRes.length > 0) {
                    rolesRes.forEach(r => {
                        if(r.user_type === 'affiliate') {
                            $('#role_id').append(`<option value="${r.id}">${r.name}</option>`);
                        }
                    });
                }
            } catch(e) { console.error("Failed to load roles"); }*/

            // Get ID
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');

            if (!id) {
                Toast.error('Error', 'No ID provided');
                window.location.href = 'list.php';
                return;
            }

            // Fetch
            try {
                const response = await API.fetchWithAuth(`<?php echo $api_link_to_edit_affiliate; ?>/${id}`);
                const data = response.user;
                const dataAud = response.aud;

                if (data) {
                    $('input[name="name"]').val(data.name);
                    $('input[name="phone"]').val(data.phone);                    
                    $('input[name="email"]').val(data.email);
                    $('input[name="status"]').val(data.status);                    
                    $('input[name="company"]').val(data.company);

                    $('input[name="job_title"]').val(data.job_title);
                    $('input[name="instant_messenger_name"]').val(data.instant_messenger_name);
                    $('input[name="instant_messenger_value"]').val(data.instant_messenger_value);
                    $('input[name="address"]').val(data.address);
                    $('input[name="city"]').val(data.city);
                    $('input[name="state"]').val(data.state);
                    $('select[name="country"]').val(data.country);
                    $('select[name="zipcode"]').val(data.zipcode);
                    
                    $('input[name="additional_tracking_url_tokens"]').val(dataAud.additional_tracking_url_tokens);
                    $('select[name="time_zone"]').val(dataAud.time_zone);
                    $('select[name="currency"]').val(dataAud.currency);
                    $('select[name="payment_method"]').val(dataAud.payment_method);
                    $('textarea[name="payment_details"]').val(dataAud.payment_details);
                    $('textarea[name="billing_terms"]').val(dataAud.billing_terms);
                    $('input[name="add_tax_basic_name"]').val(dataAud.add_tax_basic_name);
                    $('input[name="add_tax_type"]').val(dataAud.add_tax_type);
                    $('input[name="add_tax_number"]').val(dataAud.add_tax_number);
                    $('input[name="add_tax"]').val(dataAud.add_tax);


                    // Set Status
                    if (data.status) {
                        let statusVal = (typeof data.status === 'object') ? data.status.value : data.status;
                        if (statusVal === 'rejected') statusVal = 'suspended'; // Map rejected to suspended/inactive UI
                        $(`input[name="status"][value="${statusVal}"]`).prop('checked', true);
                    } else {
                        $('#statusActive').prop('checked', true);
                    }

                    // Countries
                    const countrySelect = $('select[name="country"]');
                    Object.entries(COUNTRIES).forEach(([code, name]) => {
                        countrySelect.append(new Option(name, code));
                    });

                    // Pre-select values
                    if (data.country) {
                        let val = data.country;
                        if (val.includes(',')) {
                            val = val.split(',')[0].trim();
                        }
                        countrySelect.val(val);
                    } else {
                        countrySelect.val("");
                    }


                    // Timezone 
                    const timezoneSelect = $('select[name="time_zone"]');
                    Object.entries(TIMEZONE_LIST).forEach(([code, name]) => {
                        timezoneSelect.append(new Option(name, code));
                    });

                    // Pre-select values
                    if (dataAud.time_zone) {
                        let val = dataAud.time_zone;
                        if (val.includes(',')) {
                            val = val.split(',')[0].trim();
                        }
                        timezoneSelect.val(val);
                    } else {
                        timezoneSelect.val("");
                    }




                    // Currency
                    const currencySelect = $('select[name="currency"]');
                    Object.entries(CURRENCIES).forEach(([code, name]) => {
                        currencySelect.append(new Option(name, code));
                    });

                    // Pre-select values
                    if (dataAud.currency) {
                        let val = dataAud.currency;
                        if (val.includes(',')) {
                            val = val.split(',')[0].trim();
                        }

                        currencySelect.val(val);
                    } else {
                        currencySelect.val("");
                        
                    }


                    // Payment Methods
                    const paymentMethodSelect = $('select[name="payment_method"]');
                    Object.entries(PMT_METHODS).forEach(([code, name]) => {
                        paymentMethodSelect.append(new Option(name, code));
                    });

                    // Pre-select values
                    if (dataAud.payment_method) {
                        let val = dataAud.payment_method;
                        if (val.includes(',')) {
                            val = val.split(',')[0].trim();
                        }
                        paymentMethodSelect.val(val);
                    } else {
                        paymentMethodSelect.val("");
                    }

                }
            } catch (error) {
                console.error("Load Error:", error);
                Toast.error('Load Failed', 'Failed to load affiliate data');
            }

            // Submit
            $('.user').on('submit', async function (e) {
                e.preventDefault();
                $('.alert').remove();
                $('span[style="color:#f00;"]').text('');

                const form = $(this);
                const formData = new FormData(form[0]);
                const payload = Object.fromEntries(formData.entries());

                // Requirements
                payload.uid = id;
                if (!payload.job_title) payload.job_title = 'Affiliate';

                try {
                    const response = await API.fetchWithAuth(`<?php echo $api_link_to_update_affiliate; ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (response.status === 1) {
                        Toast.success('Updated', response.msg || 'Affiliate updated successfully');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        if (response.error && typeof response.error === 'object') {
                            for (const field in response.error) {
                                const input = form.find(`[name="${field}"]`);
                                if (input.length) {
                                    input.closest('.form-group').find('span[style="color:#f00;"]').text(response.error[field][0]);
                                } else {
                                    // Show general error if field not found
                                    const alertHtml = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        ${field}: ${response.error[field][0]}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>`;
                                    $('.card-body').prepend(alertHtml);
                                }
                            }
                        } else {
                            throw new Error(response.error || 'Update failed');
                        }
                    }

                } catch (error) {
                    const alertHtml = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${error.message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`;
                    $('.card-body').prepend(alertHtml);
                }
            });
        });
    </script>

</body>

</html>