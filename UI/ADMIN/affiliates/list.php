<?php
$pathPrefix = '..';
$activePage = 'affiliates';
$pageTitle = 'Affiliates';
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
                <h1>All Affiliates</h1>
                <a href="<?php echo $pathPrefix; ?>/affiliates/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Affiliate
                </a>
            </div>

            <!-- Affiliates Table -->
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Affiliate List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Company</th>
                                    <th>Country</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
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

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- DataTables -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        // Helper function to format numbers to 2 decimal places
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
                const response = await API.fetchWithAuth('admin/affiliates/list');
                const data = response.data;
                const tbody = $('#table_body');
                tbody.empty();

                if (data && data.length > 0) {
                    data.forEach(aff => {
                        const row = `
                            <tr>
                                <td>${aff.name}</td>
                                <td>${aff.email}</td>
                                <td>${aff.phone || '-'}</td>
                                <td>${aff.company || '-'}</td>
                                <td>${aff.country || '-'}</td>
                                <td>
                                    <a href="edit.php?id=${aff.uid}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
                                    <button onclick="impersonateUser(${aff.uid})" class="btn btn-sm btn-outline-warning mr-1">Login As</button>
                                    <a href="../reports/payouts.php?aid=${aff.uid}" class="btn btn-sm btn-outline-secondary">Reports</a>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No affiliates found</td></tr>');
                }

            } catch (error) {
                console.error("Load Error:", error);
                $('#table_body').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load affiliates</td></tr>');
            }
        });

        async function impersonateUser(id) {
            if(!confirm("Are you sure you want to log in as this affiliate?")) return;
            try {
                const res = await API.fetchWithAuth(`admin/impersonate/${id}`, { method: 'POST' });
                
                // Save admin state
                localStorage.setItem('admin_access_token', localStorage.getItem('access_token'));
                localStorage.setItem('admin_user', localStorage.getItem('user'));
                
                // Set impersonated state
                localStorage.setItem('access_token', res.token);
                localStorage.setItem('user', JSON.stringify(res.user));
                localStorage.setItem('is_impersonating', 'true');
                localStorage.setItem('impersonator', JSON.stringify(res.impersonator));
                
                window.location.href = '../../AFFILIATE/dashboard.php';
            } catch (e) {
                alert('Failed to start impersonation: ' + (typeof e.message === 'object' ? Object.values(e.message).flat().join(' ') : e.message));
            }
        }
    </script>
</body>

</html>