<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Ended Offers';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Ended Offers</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Completed/Ended Offers</h6>
                    <a href="<?php echo $pathPrefix; ?>/offers/create.php" class="btn btn-primary clsCreate" style="display: none;">
                        <i class="fas fa-plus mr-2"></i> Create Offer
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Offer</th>
                                    <th>Target Geo</th>
                                    <th>Revenue</th>
                                    <th>Payout</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="offersTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        function formatDecimal(v) { return v ? parseFloat(v).toFixed(2) : '0.00'; }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            if (!PermissionGuard.check('offers')) return;
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const perms = JSON.parse(localStorage.getItem('permissions') || '{}');
            
            if (user.user_type === 'admin' || (perms.offers && perms.offers.can_create)) {
                $('.clsCreate').show();
            }

            if (user.name) {
                $('.user-name').text(user.name);
            }
            
            try {
                const response = await API.fetchWithAuth('employee/offer/ended');
                const tbody = $('#offersTableBody');
                tbody.empty();

                if (response.offers && response.offers.length > 0) {
                    response.offers.forEach(offer => {
                        const perms = JSON.parse(localStorage.getItem('permissions') || '{}');
                        let actionHtml = '';
                        if (perms['offers'] && perms['offers'].can_view) actionHtml += `<a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-primary">View</a>`;

                        tbody.append(`
                            <tr>
                                <td><strong>${offer.offer_name}</strong><div class="small text-muted">ID: ${offer.id}</div></td>
                                <td>${offer.target_country || 'Global'}</td>
                                <td>${formatDecimal(offer.ad_pricing)} ${offer.currency}</td>
                                <td>${formatDecimal(offer.aff_pricing)} ${offer.currency}</td>
                                <td><span class="badge badge-secondary">${offer.offer_status}</span></td>
                                <td>${actionHtml}</td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No ended offers</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#offersTableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>