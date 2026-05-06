<?php
$pathPrefix = '..';
$activePage = 'employees';
$pageTitle = 'Employees';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1>All Employees</h1>
                <a href="<?php echo $pathPrefix; ?>/employees/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Employee
                </a>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Employee List</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>                                    
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Designation</th>
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

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- DataTables -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
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
                const response = await API.fetchWithAuth('admin/employees/list');
                const data = response.data;
                const tbody = $('#table_body');
                tbody.empty();

                if (data && data.length > 0) {
                    data.forEach(emp => {
                        const row = `
                            <tr>                                
                                <td>${emp.name}</td>
                                <td>${emp.email}</td>
                                <td>${emp.phone || '-'}</td>
                                <td>${emp.role_name || '<span class="text-warning">Unassigned</span>'}</td>
                                <td>${emp.designation_name || '-'}</td>
                                <td>
                                    <a href="edit.php?id=${emp.id}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                    $('#dataTable').DataTable({ pageLength: 25 });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No employees found</td></tr>');
                }

            } catch (error) {
                console.error("Load Error:", error);
                $('#table_body').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load employees</td></tr>');
            }
        });
    </script>
</body>
</html>
