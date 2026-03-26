<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Offer Conversions Report';
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
                                <div class="mb-3">
                                    <span>Advertiser Name: <strong>Advertiser One</strong></span>
                                </div>
                                <table class="table table-striped table-hover" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Click Id</th>
                                            <th>Affiliate</th>
                                            <th>Sub1</th>
                                            <th>Country</th>
                                            <th>Device</th>
                                            <th>Os</th>
                                            <th>Browser</th>
                                            <th>Category</th>
                                            <th>Click At</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>1002</td>
                                            <td>
                                                <strong>Click Id:</strong><br /> click_abc_456
                                                <br /><br />
                                                <strong>Aff Click Id:</strong><br /> aff_clk_789
                                            </td>
                                            <td>Jane Smith</td>
                                            <td>sub_2</td>
                                            <td>UK</td>
                                            <td>Desktop</td>
                                            <td>Windows</td>
                                            <td>Chrome</td>
                                            <td>Finance</td>
                                            <td>01-12-2023 10:05:00</td>
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
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
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

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>








    <!-- Page level plugins -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            if (!PermissionGuard.check('reports', 'can_view')) return;
            $('#dataTable').DataTable({
                "paging": false,
                "info": false
            });
        });
    </script>
</body>

</html>