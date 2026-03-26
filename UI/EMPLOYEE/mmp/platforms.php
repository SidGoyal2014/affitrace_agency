<?php
$pathPrefix = '..';
$activePage = 'settings';
$pageTitle = 'MMP Platforms';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>MMP Platforms</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Configured Platforms</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Platform Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Loading...</td>
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
            if (!PermissionGuard.check('settings', 'can_view')) return;
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('employee/mmp/platforms');
                console.log(response.platforms);
                let platforms = response.platforms || [];
                const tbody = $('#tableBody');
                tbody.empty();

                console.log(platforms);

                if (platforms.length > 0) {
                    platforms.forEach(p => {
                        tbody.append(`
                            <tr>
                                <td>${p.id}</td>
                                <td><strong>${p.name}</strong></td>
                                <td><span class="badge badge-${p.active === true ? 'success' : 'secondary'}">${p.active === true ? 'Active' : 'Inactive'}</span></td>
                                <td>
                                    <button onclick="showConfig(${p.id}, '${p.name}')" class="btn btn-sm btn-outline-primary">Configure</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">No platforms configured</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="4" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });

        function showConfig(id, name) {
            Toast.info('Integration Instructions', `To integrate with ${name}, configure the Postback URL in your ${name} dashboard. Format: https://{domain}.com/api/postback?aff_click_id={click_id}&event_id={event_id}`, { duration: 10000 });
        }
    </script>
</body>

</html>