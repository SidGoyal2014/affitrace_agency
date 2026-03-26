<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'System Logs';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>System Logs</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Recent Activity</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Details</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
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

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            if (!PermissionGuard.check('reports', 'can_view')) return;
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('employee/reports/logs');
                let logs = response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                if (logs.length > 0) {
                    logs.forEach(log => {
                        tbody.append(`
                            <tr>
                                <td>${log.id}</td>
                                <td><span class="badge badge-info">${log.action || '-'}</span></td>
                                <td>${log.user_name || log.user_id || '-'}</td>
                                <td class="small">${log.details || '-'}</td>
                                <td>${log.created_at || '-'}</td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 50, order: [[0, 'desc']] });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No logs found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>