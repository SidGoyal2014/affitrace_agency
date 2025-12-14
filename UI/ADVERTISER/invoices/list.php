<?php
$pathPrefix = '..';
$activePage = 'invoices';
$pageTitle = 'Invoices - Advertiser';
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
                <h1>Invoices</h1>
            </div>

            <!-- DataTable -->
            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Invoice History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTableBody">
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
            // Sample data
            const invoices = [
                { id: 'INV-001', date: '2023-10-01', amount: '$500.00', status: 'Paid', action: 'Download' },
                { id: 'INV-002', date: '2023-11-01', amount: '$600.00', status: 'Unpaid', action: 'Pay Now' }
            ];

            let html = '';
            invoices.forEach(inv => {
                const badgeClass = inv.status === 'Paid' ? 'badge-success' : 'badge-warning';
                const btnClass = inv.status === 'Paid' ? 'btn-info' : 'btn-primary';
                html += `<tr>
                    <td>${inv.id}</td>
                    <td>${inv.date}</td>
                    <td>${inv.amount}</td>
                    <td><span class="badge ${badgeClass}">${inv.status}</span></td>
                    <td><a href="#" class="btn ${btnClass} btn-sm">${inv.action}</a></td>
                </tr>`;
            });
            $('#invoicesTableBody').html(html);
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>