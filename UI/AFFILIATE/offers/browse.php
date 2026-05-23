<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Browse Offers - Affiliate';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header d-flex justify-content-between align-items-center">
                <h1>Browse Offers</h1>
            </div>

            <div class="card-modern mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Available Offers</h6>
                    <!--<span class="badge badge-primary" id="offerCount">0</span>-->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="browseOffersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Offer Name</th>
                                    <th>Category</th>
                                    <th>Model</th>
                                    <th>Payout</th>
                                    <th>CAP</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Country</th>
                                    <th>Request Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Loading offers...</td>
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

    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        function normalizeStatus(status) {
            if (!status) return 'Not Requested';
            return status;
        }

        function statusBadge(status) {
            const normalized = normalizeStatus(status);

            if (normalized === 'Accepted') {
                return '<span class="badge badge-success">Approved</span>';
            }
            if (normalized === 'Pending') {
                return '<span class="badge badge-warning">Pending</span>';
            }
            if (normalized === 'Rejected') {
                return '<span class="badge badge-danger">Rejected</span>';
            }

            return '<span class="badge badge-secondary">Not Requested</span>';
        }

        function actionButton(offer) {
            const status = normalizeStatus(offer.request_status);

            if (status === 'Accepted') {
                return '<span class="badge badge-success">Already Approved</span>';
            }

            if (status === 'Pending') {
                return '<button class="badge badge-warning" disabled>Pending Approval</button>';
            }

            const btnText = status === 'Rejected' ? 'Request Again' : 'Request Access';
            return `<button class="btn btn-primary btn-sm" onclick="requestAccess(${offer.id})">${btnText}</button>`;
        }

        async function loadOffers() {
            try {
                const response = await API.fetchWithAuth('affiliate/offers/browse');
                const offers = response.offers || [];

                $('#offerCount').text(offers.length);

                const rows = offers.map(offer => [
                    offer.offer_name || 'N/A',
                    offer.category_name || 'General',
                    offer.model_name,
                    offer.currency +" " +(offer.aff_pricing || '0.00'),
                    " Clicks: " + (offer.clicks_cap || 'N/A') +" Conversions: " +(offer.conversions_cap || 'N/A'),
                    offer.target_city || 'N/A',
                    offer.target_state || 'N/A',
                    offer.target_country || 'N/A',
                    statusBadge(offer.request_status),
                    actionButton(offer)
                ]);

                if ($.fn.DataTable.isDataTable('#browseOffersTable')) {
                    const table = $('#browseOffersTable').DataTable();
                    table.clear();
                    table.rows.add(rows);
                    table.draw();
                } else {
                    $('#browseOffersTable').DataTable({
                        data: rows,
                        order: [[0, 'desc']]
                    });
                }
            } catch (error) {
                console.error('Failed to load browse offers:', error);
                $('#browseOffersTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load offers.</td></tr>');
            }
        }

        async function requestAccess(offerId) {
            try {
                const response = await API.fetchWithAuth(`affiliate/offers/request/${offerId}`, { method: 'POST' });
                Toast.success('Request Sent', response.message || 'Access request sent successfully');
                await loadOffers();
            } catch (error) {
                Toast.error('Request Failed', error.message || 'Could not send access request');
            }
        }

        $(document).ready(async function () {
            await loadOffers();
        });
    </script>
</body>

</html>
