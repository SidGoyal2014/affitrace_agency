<?php
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
                <a href="<?php echo $pathPrefix; ?>/offers/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Offer
                </a>
            </div>
            <!-- Offers Table -->
            <div class="card-modern">
                <!-- <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">All Approved Offers</h6>
                </div> -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Offer</th>
                                    <th>Advertiser</th>
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
                const response = await API.fetchWithAuth('<?php echo $user_type; ?>/offer/index');
                const tbody = $('#offersTableBody');


                tbody.empty();

                if (response.offers && response.offers.length > 0) {
                    response.offers.forEach(offer => {


                        let actions = `<a href="../reports/click_conversion_report.php?offer_id=${offer.id}" class="btn btn-sm btn-outline-primary mr-1">Reports</a>`;

                        let status = offer.offer_status;
                        let status_class = 'badge-success';

                        if (offer.deleted_at != null) {
                            status = "trash";
                            status_class = 'badge badge-danger';

                        } else if (status == 'pending') {
                            status_class = 'badge badge-warning';

                        } else if (status == 'ended') {
                            status_class = 'badge badge-info';

                        } else if (status == 'approve') {
                            status_class = 'badge badge-success';

                        } else if (status == 'rejected') {
                            status_class = 'badge badge-danger';

                        }

                        switch (status) {
                            case 'pending':
                                actions = `<a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-secondary mr-1">Edit</a>`;
                                break;

                            case 'approve':
                                actions += `<a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-secondary mr-1">Edit</a>
                                <button onclick="cloneOffer(${offer.id})" class="btn btn-sm btn-outline-warning mr-1">Clone</button>
                                <button onclick="deleteOffer(${offer.id})" class="btn btn-sm btn-outline-danger">Delete</button>`;
                                break;

                            case 'trash':
                                actions += `<button onclick="restoreOffer(${offer.id})" class="btn btn-sm btn-outline-success">Restore</button>`;
                                break;

                            case 'ended':
                                actions += `<a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-primary">View</a>`;
                                break;

                            default:
                                actions += `<a href="edit.php?id=${offer.id}" class="btn btn-sm btn-outline-secondary mr-1">Edit</a>`;
                        }


                        const tr = `
                            <tr>
                                <td>
                                    <strong>${offer.offer_name}</strong>
                                    <div class="small text-muted">ID: ${offer.id}</div>
                                </td>
                                <td>${offer.advertiser_name}</td>
                                <td>${offer.target_country || 'Global'}</td>
                                <td><span class="badge badge-info">${offer.name || offer.ad_model}</span></td>
                                <td>${offer.ad_pricing} ${offer.currency}</td>
                                <td>${offer.aff_pricing} ${offer.currency}</td>
                                <td><span class="${status_class}">${status}</span></td>
                                <td>
                                    ${actions}
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
                const response = await API.fetchWithAuth(`<?php echo $user_type; ?>/offer/delete/${id}`, {
                    method: 'DELETE'
                });

                if (response.status === 1) {
                    Toast.success('Deleted', 'Offer has been deleted');
                    location.reload();
                } else {
                    Toast.error('Delete Failed', response.error || 'Failed to delete offer');
                }
            } catch (error) {
                Toast.error('Error', error.message);
            }
        }

        async function cloneOffer(id) {
            if (!confirm("Are you sure you want to clone this offer?")) return;

            try {
                const response = await API.fetchWithAuth(`<?php echo $user_type; ?>/offer/clone/${id}`, {
                    method: 'POST'
                });

                if (response.status === 1) {
                    Toast.success('Cloned', 'Offer has been cloned');
                    location.reload();
                } else {
                    Toast.error('Clone Failed', response.error || 'Failed to clone offer');
                }
            } catch (error) {
                Toast.error('Error', error.message);
            }
        }
    </script>
</body>

</html>