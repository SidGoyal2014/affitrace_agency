<?php
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>All Advertisers</h1>
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Advertiser
                </a>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Advertiser List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
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
    <?php
    /*
     * DataTables scripts are now included in the common scripts.php to ensure they're available on all

    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    */
    ?>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('<?php echo $api_link_to_list_advertisers;?>');
                const data = response.data || [];
                const tbody = $('#table_body');
                tbody.empty();

                if (data.length > 0) {
                    data.forEach((adv, index) => {
                        tbody.append(`
                            <tr>
                                <td>${adv.name}</td>
                                <td>${adv.email}</td>
                                <td>${adv.company || '-'}</td>
                                <td>
                                    <a href="edit.php?id=${adv.uid}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
                                    <button onclick="impersonateUser(${adv.uid})" class="btn btn-sm btn-outline-warning mr-1">Login As</button>
                                </td>
                            </tr>
                        `);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No advertisers found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#table_body').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });

        async function impersonateUser(id) {
            if(!confirm("Are you sure you want to log in as this advertiser?")) return;
            try {
                const res = await API.fetchWithAuth(`<?php echo $api_link_for_impersonate;?>/${id}`, { method: 'POST' });
                
                // Save admin state
                localStorage.setItem('admin_access_token', localStorage.getItem('access_token'));
                localStorage.setItem('admin_user', localStorage.getItem('user'));
                
                // Set impersonated state
                localStorage.setItem('access_token', res.token);
                localStorage.setItem('user', JSON.stringify(res.user));
                localStorage.setItem('is_impersonating', 'true');
                localStorage.setItem('impersonator', JSON.stringify(res.impersonator));
                
                window.location.href = '../../ADVERTISER/dashboard.php';
            } catch (e) {
                alert('Failed to start impersonation: ' + (typeof e.message === 'object' ? Object.values(e.message).flat().join(' ') : e.message));
            }
        }
    </script>
</body>

</html>