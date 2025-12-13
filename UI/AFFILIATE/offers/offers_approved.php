<?php
$pathPrefix = '..';
$pageTitle = 'My Offers (Approved) - Affiliate';
include $pathPrefix . '/includes/header.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Approved Offers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Offer Name</th>
                            <th>Category</th>
                            <th>Payout</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Tracking Link Modal -->
<div class="modal fade" id="trackingLinkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tracking Link & Details</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-loading" class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="modal-content" style="display:none;">
                    <div class="form-group">
                        <label><strong>Tracking Link:</strong></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="tracking_url" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard()">Copy</button>
                            </div>
                        </div>
                        <small class="text-muted">You can add sub parameters like &sub1=... etc.</small>
                    </div>

                    <hr>
                    <h6>Offer Details</h6>
                    <p><strong>Name:</strong> <span id="offer_name"></span></p>
                    <p><strong>Description:</strong> <span id="offer_desc"></span></p>
                    <p><strong>Goals/Events:</strong></p>
                    <ul id="offer_events"></ul>
                </div>
                <div id="modal-error" class="text-danger text-center" style="display:none;">Failed to load details.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php include $pathPrefix . '/includes/footer.php'; ?>

<!-- Page level plugins -->
<script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script>
    $(document).ready(async function () {

        try {
            const response = await API.fetchWithAuth('affiliate/offers/approved');
            const offers = response.offers || [];

            const data = offers.map(offer => [
                offer.id,
                offer.offer_name,
                offer.category_name || 'General', // API might not return category_name directly in getOffersAssignedToAffiliate, check controller
                '$' + (offer.offer_price_usd || '0.00'),
                '<span class="badge badge-success">Active</span>',
                `<button class="btn btn-primary btn-sm" onclick="openTrackingModal(${offer.id})">Get Link</button>`
            ]);

            $('#dataTable').DataTable({
                data: data
            });

        } catch (error) {
            console.error('Failed to load offers:', error);
            $('#dataTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load offers.</td></tr>');
        }
    });

    async function openTrackingModal(oid) {
        $('#trackingLinkModal').modal('show');
        $('#modal-loading').show();
        $('#modal-content').hide();
        $('#modal-error').hide();

        try {
            const data = await API.fetchWithAuth(`affiliate/offers/tracking-url/${oid}`);

            if (data.offer) {
                $('#tracking_url').val(data.offer.tracking_url || 'No URL found');
                $('#offer_name').text(data.offer.offer_name);
                $('#offer_desc').text(data.offer.offer_desc || 'No description');

                let eventsHtml = '';
                if (data.offerEvents && data.offerEvents.length > 0) {
                    data.offerEvents.forEach(evt => {
                        eventsHtml += `<li>${evt.event_name} (Payout: $${evt.payout})</li>`;
                    });
                } else {
                    eventsHtml = '<li>Default Conversion</li>';
                }
                $('#offer_events').html(eventsHtml);

                $('#modal-loading').hide();
                $('#modal-content').show();
            } else {
                throw new Error('No offer data');
            }

        } catch (error) {
            console.error('Failed to load detailed info:', error);
            $('#modal-loading').hide();
            $('#modal-error').show();
        }
    }

    function copyToClipboard() {
        var copyText = document.getElementById("tracking_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Copied the link!");
    }
</script>

</body>

</html>