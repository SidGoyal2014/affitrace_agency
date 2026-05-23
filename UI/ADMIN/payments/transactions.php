<?php
$pathPrefix = '..';
$activePage = 'settings';
$pageTitle = 'Transactions';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Transactions</h1>
            </div>

            <div class="card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From - To</th>
                                    <th>Code</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Loading...</td>
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
                const response = await API.fetchWithAuth('admin/reports/transactions');
                console.log(response);
                let txns = response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                console.log(txns);


                if (txns.length > 0) {
                    txns.forEach(t => {
                        tbody.append(`
                            <tr>
                                <td>
                                    <strong>Type: </strong>${ t.payment_type } <br> 
                                    <strong>Mode: </strong>${ t.payment_mode } <br>                                     
                                </td>
                                <td>
                                    <strong>From: </strong>${t.payment_from } <br> 
                                    <strong>To: </strong>${t.payment_to } <br> 
                                    <strong>Amount: </strong>${t.transaction_amount }
                                </td>
                                <td>
                                    <strong>Transaction Code: </strong>${t.transaction_code } <br>
                                    <strong>Manual Invoice No: </strong>${t.manual_invoice_no }
                                </td>                                
                            </tr>
                        `);
                    });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No transactions found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>