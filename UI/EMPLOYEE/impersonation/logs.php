<?php
$pathPrefix = '..';
$activePage = 'impersonation';
$pageTitle = 'Impersonation Logs';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Impersonation Logs</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Audit Log</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Impersonator</th>
                                    <th>Impersonated User</th>
                                    <th>Type</th>
                                    <th>Started</th>
                                    <th>Ended</th>
                                    <th>IP Address</th>
                                    <th>Notified</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr><td colspan="8" class="text-center py-4 text-muted">Loading...</td></tr>
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
            if (!API.isAuthenticated()) { window.location.href = '../../login.php'; return; }
            if (!PermissionGuard.check('settings', 'can_view')) return;
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('employee/impersonation-logs');
                const logs = response.data || [];
                const tbody = $('#table_body');
                tbody.empty();

                if (logs.length > 0) {
                    logs.forEach(log => {
                        const started = new Date(log.started_at).toLocaleString();
                        const ended = log.ended_at ? new Date(log.ended_at).toLocaleString() : '<span class="badge badge-warning">Active</span>';
                        const notified = log.notified ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                        tbody.append(`
                            <tr>
                                <td>${log.id}</td>
                                <td>${log.impersonator ? log.impersonator.name : '-'}<br><small class="text-muted">${log.impersonator ? log.impersonator.email : ''}</small></td>
                                <td>${log.impersonated ? log.impersonated.name : '-'}<br><small class="text-muted">${log.impersonated ? log.impersonated.email : ''}</small></td>
                                <td>${log.impersonated ? log.impersonated.user_type : '-'}</td>
                                <td><small>${started}</small></td>
                                <td><small>${ended}</small></td>
                                <td><code>${log.ip_address}</code></td>
                                <td>${notified}</td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 25, order: [[0, 'desc']] });
                } else {
                    tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No impersonation logs found</td></tr>');
                }
            } catch (error) {
                console.error('Load Error:', error);
                $('#table_body').html('<tr><td colspan="8" class="text-center text-danger">Failed to load logs</td></tr>');
            }
        });
    </script>
</body>
</html>
