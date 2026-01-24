<!-- Bootstrap core JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo $pathPrefix; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo $pathPrefix; ?>/assets/js/sb-admin-2.min.js"></script>

<!-- API Integration -->
<script src="<?php echo $pathPrefix; ?>/assets/js/api.js"></script>

<!-- Toast Notifications -->
<script src="<?php echo $pathPrefix; ?>/assets/js/toast.js"></script>

<script>
    // Check auth for affiliate pages
    $(document).ready(function () {
        if (window.location.pathname.endsWith('login.php') || window.location.pathname.endsWith('login.html')) return;

        if (!API.isAuthenticated()) {
            window.location.href = '<?php echo $pathPrefix; ?>/../login.php';
        } else {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user && user.user_type !== 'affiliate') {
                Toast.error('Unauthorized', 'You do not have access to this area');
                setTimeout(() => API.logout(), 2000);
            }
            if (user) {
                $('.user-name').text(user.name);
            }
        }
    });
</script>