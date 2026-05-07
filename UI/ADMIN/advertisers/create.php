<?php
$pathPrefix = '..';
$activePage = 'advertisers';
$pageTitle = 'Create Advertiser';
include $pathPrefix . '/includes/head.php';
?>
<script src="../../common/js/countries.js"></script>

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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name *</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder=" " required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone *</label>
                                            <input type="text" name="phone" id="phone" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company *</label>
                                            <input type="text" name="company" id="company" placeholder=" " class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country" class="form-control select2-country">
                                                <option value="">(All Countries)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password *</label>
                                            <input type="password" name="password" id="password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password *</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="submitBtn" class="btn btn-primary">Create Advertiser</button>
                            </form>
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
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const countrySelect = $('select[name="country"]');
                Object.entries(COUNTRIES).forEach(([code, name]) => {
                    countrySelect.append(new Option(name, code));
                });
            } catch(e) { console.error("Failed to load countries"); }

            $('#createForm').on('submit', async function (e) {
                console.log("Helllllllooooooooooooo");
                e.preventDefault();
                $('#alert-container').empty();

                const password = $('#password').val();
                const confirm = $('#password_confirmation').val();

                if (password !== confirm) {
                    $('#alert-container').html('<div class="alert alert-danger alert-modern">Passwords do not match.</div>');
                    return;
                }

                const btn = $('#submitBtn');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Creating...');

                try {
                    const response = await API.fetchWithAuth('admin/advertisers/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: $('#name').val(),
                            email: $('#email').val(),
                            company: $('#company').val(),
                            phone: $('#phone').val(),
                            password: password,
                            password_confirmation: confirm,
                            user_type: 'advertiser',
                            role_id: $('#role_id').val()
                        })
                    });

                    if (response.status === 1) {
                        $('#alert-container').html('<div class="alert alert-success alert-modern"><i class="fas fa-check-circle mr-2"></i> Advertiser created successfully! Redirecting...</div>');
                        setTimeout(() => window.location.href = 'list.php', 1500);
                    } else {
                        let errorMsg = response.error;
                        if (typeof response.error === 'object') {
                            errorMsg = Object.values(response.error).flat().join('<br>');
                        }
                        throw new Error(errorMsg || 'Failed to create advertiser.');
                    }
                } catch (e) {
                    /*
                    $('#alert-container').html(`<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> ${e.message}</div>`);
                    btn.prop('disabled', false).html(originalText);
                    */
                    let errorMsg = e.message;
                    if (e.responseData && e.responseData.error) {
                        const errData = e.responseData.error;
                        errorMsg = typeof errData === 'object'
                            ? Object.values(errData).flat().join('<br>')
                            : errData;
                    }
                    $('#alert-container').html(`<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> ${errorMsg}</div>`);
                    btn.prop('disabled', false).html(originalText);


                }
            });
        });
    </script>
</body>

</html>