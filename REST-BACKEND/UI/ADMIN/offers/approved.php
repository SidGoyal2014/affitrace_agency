<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Approved Offers';
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
                <a href="<?php echo $pathPrefix; ?>/offers/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Offer
                </a>
            </div>

            <!-- Offers Table -->
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">All Approved Offers</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Offer</th>
                                    <th>Target Geo</th>
                                    <th>Model</th>
                                    <th>Revenue</th>
                                    <th>Payout</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="offersTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- DataTables -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }

            try {
                const response = await API.fetchWithAuth('admin/offer/approved');
                const tbody = $('#offersTableBody');
                tbody.empty();

                if (response.offers && response.offers.length > 0) {
                    response.offers.forEach(offer => {
                        const tr = `
                            <tr>
                                <td>
                                    <strong>${offer.offer_name}</strong>
                                    <div class="small text-muted">ID: ${offer.id}</div>
                                </td>
                                <td>${offer.target_country || 'Global'}</td>
                                <td><span class="badge badge-info">${offer.name || offer.ad_model}</span></td>
                                <td>${offer.ad_pricing} ${offer.currency}</td>
                                <td>${offer.aff_pricing} ${offer.currency}</td>
                                <td><span class="badge badge-success">${offer.offer_status}</span></td>
                                <td>
                                    <a href="../reports/click_conversion_report.php?offer_id=${offer.id}" class="btn btn-sm btn-outline-primary mr-1">Reports</a>
                                    <a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-secondary mr-1">Edit</a>
                                    <button onclick="deleteOffer(${offer.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });
                    $('#dataTable').DataTable({
                        pageLength: 25,
                        order: [[0, 'asc']]
                    });
                } else {
                    tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">No approved offers found</td></tr>');
                }

            } catch (error) {
                console.error("Load Error:", error);
                $('#offersTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load offers</td></tr>');
            }
        });

        async function deleteOffer(id) {
            if (!confirm("Are you sure you want to delete this offer?")) return;

            try {
                const response = await API.fetchWithAuth(`admin/offer/delete/${id}`, {
                    method: 'DELETE'
                });

                if (response.status === 1) {
                    alert('Offer deleted');
                    location.reload();
                } else {
                    alert(response.error || 'Delete failed');
                }
            } catch (error) {
                alert("Error: " + error.message);
            }
        }
    </script>
</body>

</html>