<?php
$pathPrefix = '..';
$activePage = 'advertisers';
$pageTitle = 'Create Advertiser';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Create Advertiser</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-body">
                            <div id="alert-container"></div>
                            <form id="createForm">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Company</label>
                                    <input type="text" name="company" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Advertiser</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <span>© 2024 Affitrace. All rights reserved.</span>
    </footer>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            $('#createForm').on('submit', async function (e) {
                e.preventDefault();
                $('#alert-container').empty();
                try {
                    const response = await API.fetchWithAuth('admin/advertisers/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: $('input[name="name"]').val(),
                            email: $('input[name="email"]').val(),
                            company: $('input[name="company"]').val(),
                            password: $('input[name="password"]').val()
                        })
                    });
                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success">Created! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        throw new Error(response.error || 'Failed');
                    }
                } catch (e) {
                    $('#alert-container').html(`<div class="alert alert-danger">${e.message}</div>`);
                }
            });
        });
    </script>
</body>

</html>