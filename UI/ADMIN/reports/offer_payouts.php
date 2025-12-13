<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Offer Payouts Report';
include $pathPrefix . '/includes/head.php';
?>


<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">
                <div class="col-lg-12 mb-8">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Affiliate Name</th>
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
                                            <td><a href="affiliate_payouts.php">John Doe</a></td>
                                            <td>2.50 USD</td>
                                            <td>aff_clk_123</td>
                                            <td>192.168.1.1</td>
                                            <td>Mobile</td>
                                            <td>US</td>
                                            <td>iOS</td>
                                            <td>Safari</td>
                                            <td>sub_1</td>
                                            <td>01-12-2023 10:00:00</td>
                                            <td>Initial</td>
                                        </tr>
                                        <tr>
                                            <td><a href="affiliate_payouts.php">Jane Smith</a></td>
                                            <td>10.00 USD</td>
                                            <td>aff_clk_456</td>
                                            <td>10.0.0.1</td>
                                            <td>Desktop</td>
                                            <td>UK</td>
                                            <td>Windows</td>
                                            <td>Chrome</td>
                                            <td>sub_2</td>
                                            <td>01-12-2023 10:05:00</td>
                                            <td>Purchase</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">
                                <!-- Pagination placeholder -->
                                <nav>
                                    <ul class="pagination">
                                        <li class="page-item disabled" aria-disabled="true"
                                            aria-label="&laquo; Previous">
                                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                        </li>
                                        <li class="page-item active" aria-current="page"><span
                                                class="page-link">1</span></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#" rel="next"
                                                aria-label="Next &raquo;">&rsaquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>


                    </div>
                </div>
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








    <!-- Page level plugins -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "paging": false,
                "info": false
            });
        });
    </script>
</body>

</html>