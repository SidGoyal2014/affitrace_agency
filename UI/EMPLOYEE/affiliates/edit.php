<?php
$pathPrefix = '..';
$activePage = 'affiliates';
$pageTitle = 'Edit Affiliate';
include $pathPrefix . '/includes/head.php';
?>


<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><span style='color:#f00;'>* marked values are
                        mandatory</span></h6>
            </div>

            <div class="card-body">

                <form class="user" method='post' action="#">

                    <div class="row">

                        <div class="form-group col-lg-6">
                            <input type="text" name="name" class="form-control form-control" id="exampleInputName"
                                aria-describedby="nameHelp" placeholder="Enter Name" value="John Doe">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="email" name="email" class="form-control form-control" id="exampleInputEmail"
                                aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                value="john@example.com">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <label class="d-block">Status <span style='color:#f00;'>*</span></label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusActive" name="status" class="custom-control-input"
                                    value="active">
                                <label class="custom-control-label" for="statusActive">Active</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusInactive" name="status" class="custom-control-input"
                                    value="inactive">
                                <label class="custom-control-label" for="statusInactive">Inactive</label>
                            </div>
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="password" name="password" class="form-control form-control"
                                id="exampleInputPassword" placeholder="Enter Password">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="password" name="password_confirmation" class="form-control form-control"
                                id="exampleInputConfirmPassword" placeholder="Confirm password">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="text" name="company" class="form-control form-control" id="exampleInputCompany"
                                placeholder="Enter company" value="Doe Inc.">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="text" name="country" class="form-control form-control" id="exampleInputCountry"
                                placeholder="Enter country" value="US">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="text" name="phone" class="form-control form-control" id="exampleInputMobile"
                                placeholder="Enter phone no" value="1234567890">
                            <span style='color:#f00;'></span>
                        </div>

                        <div class="form-group col-lg-6">
                            <label class="d-block">Role</label>
                            <select name="role_id" id="role_id" class="form-control">
                                <option value="">Select Role</option>
                            </select>
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
            if (!PermissionGuard.check('affiliates', 'can_edit')) return;

            // Navbar User Info
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('#userDropdown span').text(user.name);
            }

            try {
                const rolesRes = await API.fetchWithAuth('employee/roles');
                if(rolesRes && rolesRes.length > 0) {
                    rolesRes.forEach(r => {
                        if(r.user_type === 'affiliate') {
                            $('#role_id').append(`<option value="${r.id}">${r.name}</option>`);
                        }
                    });
                }
            } catch(e) { console.error("Failed to load roles"); }

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
                const response = await API.fetchWithAuth(`employee/affiliates/edit/${id}`);
                const data = response.user;

                if (data) {
                    $('input[name="name"]').val(data.name);
                    $('input[name="email"]').val(data.email);
                    $('input[name="company"]').val(data.company);
                    $('input[name="country"]').val(data.country);
                    $('input[name="country"]').val(data.country);
                    $('input[name="phone"]').val(data.phone);
                    $('#role_id').val(data.role_id);

                    // Set Status
                    if (data.status) {
                        $(`input[name="status"][value="${data.status}"]`).prop('checked', true);
                    } else {
                        $('#statusActive').prop('checked', true);
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
                if (!payload.instant_messenger_name) payload.instant_messenger_name = 'Skype';
                if (!payload.instant_messenger_value) payload.instant_messenger_value = 'N/A';

                try {
                    const response = await API.fetchWithAuth('employee/affiliates/update', {
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