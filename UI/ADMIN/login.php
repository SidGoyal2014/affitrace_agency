<?php $pathPrefix = '.'; ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <!-- Custom fonts for this template-->
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="assets/css/modern-theme.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <div class="container">

        <!-- Page Heading -->


        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-4">

                <div class="card o-hidden border-0 my-5">
                    <div class="card-body p-0" style="border:1px solid #ccc">

                        <!-- Nested Row within Card Body -->
                        <div class="row">


                            <div class="col-lg-12">
                                <div class="p-5">


                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>

                                    <form class="user" method='post' action="dashboard.php">
                                        <div class="form-group">
                                            <select name="user_type" class="form-control">
                                                <option value="">LOGIN AS</option>
                                                <option value="admin">Administrator</option>
                                                <option value="affiliate">Affiliate</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="eai" class="form-control" id="exampleInputAgency"
                                                placeholder="Enter Agency ID..." value="demo">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" id="exampleInputEmail"
                                                aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control"
                                                id="exampleInputPassword" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <input type="submit" value="Login" class="btn btn-primary btn-user btn-block">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <!-- <div class="row">
        <div class="col-lg-12" style="text-align:center;">
            <span>Copyright &copy; Affitrace 2023</span>
        </div>
    </div>
    </div> -->

    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>
    <script src="assets/js/api.js"></script>

    <script>
        $(document).ready(function () {
            $('form.user').on('submit', async function (e) {
                e.preventDefault();

                // Clear previous errors
                $('.alert-danger').remove();

                const email = $('input[name="email"]').val();
                const password = $('input[name="password"]').val();
                const user_type = $('select[name="user_type"]').val();
                const eai = $('input[name="eai"]').val();

                if (!email || !password || !user_type) {
                    alert('Please fill in all fields');
                    return;
                }

                try {
                    const data = await API.login(email, password, user_type, eai);

                    if (data.user.user_type === 'affiliate') {
                        window.location.href = '../AFFILIATE/dashboard.php';
                    } else if (data.user.user_type === 'advertiser') {
                        window.location.href = '../ADVERTISER/dashboard.php';
                    } else {
                        window.location.href = 'dashboard.php';
                    }
                } catch (error) {
                    const errorMsg = error.message || 'Login failed';
                    const alertHtml = `<div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">x</button> 
                        <strong>${errorMsg}</strong>
                    </div>`;
                    $('.text-center').first().after(alertHtml);
                }
            });
        });
    </script>

</body>

</html>