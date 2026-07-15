<?php
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Audit Logs</h1>
            </div>

            <div class="card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="15%">Date Fired</th>
                                    <th width="15%">Actor Name</th>
                                    <th width="15%">Action</th>
                                    <th width="15%">IP Address</th>
                                    <th width="40%">Details</th>

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

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('<?php echo $user_type; ?>/logs/audit_logs');
                // Since pagination was used in the backend, the data is inside response.data.data
                // Wait, Laravel paginate() returns an object where .data is the array of items.
                // <?php echo $user_type; ?>ReportService puts this into response.data. So it will be response.data.data
                let logs = [];
                if (response.data && response.data.data) {
                    logs = response.data.data;
                } else if (Array.isArray(response.data)) {
                    logs = response.data; // Just in case it's not paginated
                }

                const tbody = $('#tableBody');
                tbody.empty();

                if (logs.length > 0) {
                    logs.forEach(log => {

                        let statusClass = 'badge-danger';

                        if (log.http_status && log.http_status.startsWith('2')) {
                            statusClass = 'badge-success';

                        } else if (log.http_status === 'ERR') {
                            statusClass = 'badge-warning';

                        }

                        tbody.append(`
                            <tr>                                
                                <td>${log.created_at}</td>
                                <td>${log.username}</td>
                                <td>${log.action}</td>
                                <td>${log.ip_address}</td>                                
                                <td style="width: 40%; word-break: break-word;">${log.details}</td>
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