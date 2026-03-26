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
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">All Transactions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
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

    <script>
        function formatDecimal(v) { return v ? parseFloat(v).toFixed(2) : '0.00'; }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            if (!PermissionGuard.check('settings', 'can_view')) return;
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('employee/payments/transactions');
                let txns = response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                if (txns.length > 0) {
                    txns.forEach(t => {
                        tbody.append(`
                            <tr>
                                <td>${t.id}</td>
                                <td>${t.user_name || t.user_id}</td>
                                <td><strong>$${formatDecimal(t.amount)}</strong></td>
                                <td><span class="badge badge-info">${t.type || '-'}</span></td>
                                <td><span class="badge badge-${t.status === 'completed' ? 'success' : 'warning'}">${t.status}</span></td>
                                <td>${t.created_at || '-'}</td>
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