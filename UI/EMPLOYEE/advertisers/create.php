<?php
$pathPrefix = '..';
$activePage = 'advertisers';
$pageTitle = 'Create Advertiser';
include $pathPrefix . '/includes/head.php';
?>

<style>
    .form-card {
        background: #6366F1;
        border-radius: 20px;
        padding: 3px;
    }

    .form-card-inner {
        background: #fff;
        border-radius: 18px;
        padding: 40px;
    }

    .form-floating {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-floating input {
        width: 100%;
        padding: 20px 16px 8px;
        border: 2px solid #e0e6ed;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-floating input:focus {
        border-color: #667eea;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-floating label {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
        transition: all 0.2s ease;
        background: transparent;
        padding: 0 4px;
    }

    .form-floating input:focus+label,
    .form-floating input:not(:placeholder-shown)+label {
        top: 0;
        font-size: 12px;
        color: #667eea;
        background: #fff;
        font-weight: 600;
    }

    .form-floating input::placeholder {
        color: transparent;
    }

    .btn-gradient {
        background: #6366F1;
        border: none;
        color: #fff;
        padding: 16px 32px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
        background: #4f46e5;
        color: #fff;
    }

    .btn-gradient:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .header-section {
        text-align: center;
        margin-bottom: 40px;
    }

    .header-section h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .header-section p {
        color: #64748b;
        font-size: 15px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 24px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #667eea;
        text-decoration: none;
    }

    .back-link i {
        margin-right: 8px;
    }

    .alert-modern {
        border-radius: 12px;
        border: none;
        padding: 16px 20px;
    }

    .row-gap {
        gap: 0 24px;
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 24px 0;
        color: #94a3b8;
        font-size: 13px;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .divider span {
        padding: 0 16px;
    }
</style>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-xl-5">

                    <a href="list.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Back to Advertisers
                    </a>

                    <div class="form-card">
                        <div class="form-card-inner">
                            <div class="header-section">
                                <h1><i class="fas fa-user-plus mr-2" style="color: #667eea;"></i> New Advertiser</h1>
                                <p>Create a new advertiser account</p>
                            </div>

                            <div id="alert-container"></div>

                            <form id="createForm">
                                <div class="form-floating">
                                    <input type="text" name="name" id="name" placeholder=" " required>
                                    <label for="name">Full Name</label>
                                </div>

                                <div class="form-floating">
                                    <input type="email" name="email" id="email" placeholder=" " required>
                                    <label for="email">Email Address</label>
                                </div>

                                <div class="row row-gap">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="company" id="company" placeholder=" " required>
                                            <label for="company">Company</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" name="phone" id="phone" placeholder=" " required>
                                            <label for="phone">Phone Number</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-muted" style="font-size: 14px; padding-left: 16px;">Role Assignment</label>
                                    <select name="role_id" id="role_id" class="form-control" style="border-radius:12px; height:58px; padding-left:16px;">
                                        <option value="">Select Role (Optional)</option>
                                    </select>
                                </div>

                                <div class="divider"><span>Security</span></div>

                                <div class="row row-gap">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" name="password" id="password" placeholder=" "
                                                required>
                                            <label for="password">Password</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" placeholder=" " required>
                                            <label for="password_confirmation">Confirm Password</label>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="user_type" value="advertiser">

                                <button type="submit" class="btn-gradient mt-4" id="submitBtn">
                                    <i class="fas fa-check mr-2"></i> Create Advertiser
                                </button>
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
            if (!PermissionGuard.check('advertisers', 'can_create')) return;
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const rolesRes = await API.fetchWithAuth('employee/roles');
                if(rolesRes && rolesRes.length > 0) {
                    rolesRes.forEach(r => {
                        if(r.user_type === 'advertiser') {
                            $('#role_id').append(`<option value="${r.id}">${r.name}</option>`);
                        }
                    });
                }
            } catch(e) { console.error("Failed to load roles"); }

            $('#createForm').on('submit', async function (e) {
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
                    const response = await API.fetchWithAuth('employee/advertisers/save', {
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
                    $('#alert-container').html(`<div class="alert alert-danger alert-modern"><i class="fas fa-exclamation-circle mr-2"></i> ${e.message}</div>`);
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
</body>

</html>