<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'All Offers - Advertiser';
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
                <h1>All Offers</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Offers List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Budget</th>
                                    <th>Conversions</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="offersTableBody">
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
            // TODO: Load offers from API
            // Sample data for now
            const offers = [
                { id: 1, name: 'Mobile Game Install', budget: '$10,000', conversions: 500, status: 'Active' },
                { id: 2, name: 'Dating App Signup', budget: '$5,000', conversions: 200, status: 'Paused' }
            ];

            let html = '';
            offers.forEach(offer => {
                const badgeClass = offer.status === 'Active' ? 'badge-success' : 'badge-warning';
                html += `<tr>
                    <td>${offer.id}</td>
                    <td>${offer.name}</td>
                    <td>${offer.budget}</td>
                    <td>${offer.conversions}</td>
                    <td><span class="badge ${badgeClass}">${offer.status}</span></td>
                </tr>`;
            });
            $('#offersTableBody').html(html);
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>