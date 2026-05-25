<?php
$pathPrefix = '.';
$pathCommonPrefix = $pathPrefix . '/../common';
$activePage = 'dashboard';
$pageTitle = 'Dashboard';
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
                <h1>Dashboard</h1>
            </div>

            <!-- Employee Welcome Section -->
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card-modern shadow-sm border-0">
                        <div class="card-body py-5 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-3x text-primary"></i>
                            </div>
                            <h4 class="text-primary mb-3">Welcome to the Employee Dashboard</h4>
                            <p class="text-muted mb-0">Employee-specific metrics and tasks will be displayed here.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('.user-name').text(user.name);
            }
        });
    </script>
</body>

</html>