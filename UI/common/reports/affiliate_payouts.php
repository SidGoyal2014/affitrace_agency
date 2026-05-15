<?php
include $pathPrefix . '/includes/head.php';
?>


<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <div class="card-body">
                <div class="table-responsive">

                    <!-- Search Form Placeholder -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form class="form-inline">
                                <label class="sr-only" for="inlineFormInputName2">Name</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2"
                                    placeholder="Search...">
                                <button type="submit" class="btn btn-primary mb-2">Search</button>
                            </form>
                        </div>
                    </div>


                    <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Offer Name</th>
                                <th>Payout</th>
                                <th>Affiliate Click Id</th>
                                <th>IP</th>
                                <th>Device</th>
                                <th>Country</th>
                                <th>Os</th>
                                <th>Browser</th>
                                <th>Sub1</th>
                                <th>Conversion Date</th>
                                <th>Event Name</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td><a href="#">Sample Offer 1</a></td>
                                <td>2.00 USD</td>
                                <td>aff_click_123</td>
                                <td>192.168.1.1</td>
                                <td>iPhone</td>
                                <td>US</td>
                                <td>iOS</td>
                                <td>Safari</td>
                                <td>sub_val_1</td>
                                <td>06-12-2023 10:00:00</td>
                                <td>Install</td>
                            </tr>
                            <tr>
                                <td><a href="#">Sample Offer 2</a></td>
                                <td>12.00 USD</td>
                                <td>aff_click_456</td>
                                <td>10.0.0.1</td>
                                <td>Desktop</td>
                                <td>UK</td>
                                <td>Windows</td>
                                <td>Chrome</td>
                                <td>sub_val_2</td>
                                <td>06-12-2023 11:30:00</td>
                                <td>Purchase</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>









    <!-- Page level plugins -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>

</body>

</html>