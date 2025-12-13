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

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 order-last order-md-first">
                    <div class="copyright text-center text-md-start">
                        <p class="text-sm">
                            Designed and Developed by <a href="#" class="">AffiTrace</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="terms d-flex justify-content-center justify-content-md-end">
                        <a href="#" class="text-sm">Term & Conditions</a>
                        <a href="#" class="text-sm">Privacy & Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

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

            // Get ID
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');

            if (!id) {
                alert("No ID provided");
                window.location.href = 'list.php';
                return;
            }

            // Fetch
            try {
                const response = await API.fetchWithAuth(`admin/affiliates/edit/${id}`);
                const data = response.user;

                if (data) {
                    $('input[name="name"]').val(data.name);
                    $('input[name="email"]').val(data.email);
                    $('input[name="company"]').val(data.company);
                    $('input[name="country"]').val(data.country);
                    $('input[name="phone"]').val(data.phone);
                }
            } catch (error) {
                console.error("Load Error:", error);
                alert("Failed to load data");
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
                    const response = await API.fetchWithAuth('admin/update_affiliate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (response.status === 1) {
                        alert(response.msg || 'Updated successfully');
                        window.location.href = 'list.php';
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