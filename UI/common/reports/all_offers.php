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
                <h1>Clicks Report</h1>
            </div>

            <!-- Report Table -->
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">All Offers - Click Data</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Offer Name</th>
                                    <th>Total Clicks</th>
                                    <th>Total Conversions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
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
        function formatDecimal(value) {
            if (value === null || value === undefined || value === '') return '0.00';
            return parseFloat(value).toFixed(2);
        }

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
                const response = await API.fetchWithAuth('admin/reports/all-offers');

                let offers = [];
                if (response.data && response.data.data) {
                    offers = response.data.data;
                } else if (Array.isArray(response.data)) {
                    offers = response.data;
                }

                const tbody = $('#reportTableBody');
                tbody.empty();

                if (offers.length > 0) {
                    offers.forEach(offer => {
                        const tr = `
                            <tr>
                                <td><strong>${offer.id}</strong></td>
                                <td>${offer.offer_name}</td>
                                <td><a href="offer_clicks.php?id=${offer.id}" class="text-primary font-weight-bold">${offer.total_clicks}</a></td>
                                <td><a href="offer_conversions.php?id=${offer.id}" class="text-success font-weight-bold">${offer.total_conversions}</a></td>
                                <td>
                                    <a href="../offers/edit.php?id=${offer.id}" class="btn btn-sm btn-outline-primary">Edit Offer</a>
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No data found</td></tr>');
                }

            } catch (error) {
                console.error("Error loading reports:", error);
                $('#reportTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load reports</td></tr>');
            }
        });
    </script>
</body>

</html>