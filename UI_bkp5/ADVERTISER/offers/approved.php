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
        $(document).ready(async function () {
            try {
                const data = await API.fetchWithAuth('advertiser/offers/approved');

                let html = '';
                if (data.offers && data.offers.length > 0) {
                    data.offers.forEach(offer => {
                        html += `<tr>
                            <td>${offer.id || offer.oid}</td>
                            <td>${offer.offer_name}</td>
                            <td>$${Number(offer.offer_budget || 0).toLocaleString()}</td>
                            <td><span class="badge badge-success">${offer.offer_status || 'Approved'}</span></td>
                        </tr>`;
                    });
                }
                $('#approvedTableBody').html(html);
                $('#dataTable').DataTable();
            } catch (error) {
                console.error('Approved offers load failed:', error);
                $('#approvedTableBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load offers: ' + error.message + '</td></tr>');
            }
        });
    </script>
</body>

</html>