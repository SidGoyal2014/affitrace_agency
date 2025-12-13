<?php
$pathPrefix = '..';
$pageTitle = 'Payouts Report - Affiliate';
include $pathPrefix . '/includes/header.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payouts (Conversions)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Offer Name</th>
                            <th>Event</th>
                            <th>Amount</th>
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

<?php include $pathPrefix . '/includes/footer.php'; ?>

<!-- Page level plugins -->
<script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script>
    $(document).ready(async function () {

        try {
            const response = await API.fetchWithAuth('affiliate/reports/payouts');
            const payoutsData = response.data || [];

            const data = payoutsData.map(row => {
                return [
                    row.date_updated,
                    row.offer_name,
                    row.event_name || 'Default',
                    '$' + parseFloat(row.price).toFixed(2) + ' ' + (row.ot_currency || 'USD')
                ];
            });

            $('#dataTable').DataTable({
                data: data,
                order: [[0, "desc"]] // Sort by Date descending
            });

        } catch (error) {
            console.error('Failed to load payouts report:', error);
            $('#dataTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load report.</td></tr>');
        }
    });
</script>

</body>

</html>