<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Approved Offers - Advertiser';
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
                <h1>Approved Offers</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Approved List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="approvedTableBody">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Page level plugins -->
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Sample data
            const offers = [
                { id: 1, name: 'Mobile Game Install', budget: '$10,000', status: 'Active' }
            ];

            let html = '';
            offers.forEach(offer => {
                html += `<tr>
                    <td>${offer.id}</td>
                    <td>${offer.name}</td>
                    <td>${offer.budget}</td>
                    <td><span class="badge badge-success">${offer.status}</span></td>
                </tr>`;
            });
            $('#approvedTableBody').html(html);
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>