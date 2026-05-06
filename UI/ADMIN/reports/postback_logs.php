<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Affiliate Postback Logs';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Affiliate Postback Logs</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Outbound Firing History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>                                    
                                    <th>Date Fired</th>
                                    <th>Affiliate Name</th>
                                    <th>Conv ID</th>
                                    <th>Status</th>
                                    <th>URL Fired</th>
                                    <th>Response Body</th>
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
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('admin/reports/postback-logs');
                // Since pagination was used in the backend, the data is inside response.data.data
                // Wait, Laravel paginate() returns an object where .data is the array of items.
                // AdminReportService puts this into response.data. So it will be response.data.data
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
                        
                        // Limit URL characters for UI display
                        let shortUrl = log.url_fired.length > 50 ? log.url_fired.substring(0, 50) + '...' : log.url_fired;
                        
                        tbody.append(`
                            <tr>                                
                                <td>${log.created_at || '-'}</td>
                                <td>${log.affiliate_name || '-'}</td>
                                <td><span class="badge badge-info">${log.conversion_id || '-'}</span></td>
                                <td><span class="badge ${statusClass}">${log.http_status || '-'}</span></td>
                                <td class="small" title="${log.url_fired}"><a href="${log.url_fired}" target="_blank">${shortUrl}</a></td>
                                <td class="small text-muted" style="max-width: 250px; overflow-x: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${log.response_body || ''}">${log.response_body || '-'}</td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 50, order: [[0, 'desc']] });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No logs found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>
