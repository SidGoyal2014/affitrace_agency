<?php
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Conversions Report</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">All Conversions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Offer</th>
                                    <th>Affiliate</th>
                                    <th>Event</th>
                                    <th>Revenue</th>
                                    <th>Payout</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
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

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>


    <script>
        function formatDecimal(v) { return v ? parseFloat(v).toFixed(2) : '0.00'; }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('admin/reports/conversions');
                let data = response.data?.data || response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                if (data.length > 0) {
                    data.forEach(c => {
                        tbody.append(`
                            <tr>
                                <td>${c.id}</td>
                                <td>${c.offer_name || c.oid}</td>
                                <td>${c.affiliate_name || c.aid}</td>
                                <td>${c.event || '-'}</td>
                                <td>${formatDecimal(c.revenue)}</td>
                                <td>${formatDecimal(c.payout)}</td>
                                <td>${c.created_at || '-'}</td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No conversions found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>