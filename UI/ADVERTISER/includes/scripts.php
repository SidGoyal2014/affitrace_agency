<?php
$pathCommonPrefix = $pathPrefix . '/../common';
?>
<!-- Bootstrap core JavaScript-->
<script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo $pathCommonPrefix; ?>/assets/js/sb-admin-2.min.js"></script>

<!-- DataTables -->
<script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $pathCommonPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- API Integration -->
<script src="<?php echo $pathCommonPrefix; ?>/assets/js/api.js"></script>

<!-- Toast Notifications -->
<script src="<?php echo $pathCommonPrefix; ?>/assets/js/toast.js"></script>

<!-- White-Label Branding Component -->
<script src="<?php echo $pathCommonPrefix; ?>/assets/js/branding.js"></script>

<!-- Report Filters -->
<script src="<?php echo $pathCommonPrefix; ?>/js/report-date-presets.js"></script>

<script>
    // Check auth for advertiser pages
    $(document).ready(function () {
        if (window.location.pathname.endsWith('login.php') || window.location.pathname.endsWith('login.html')) return;

        if (!API.isAuthenticated()) {
            window.location.href = '<?php echo $pathPrefix; ?>/../login.php';
        } else {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user && user.user_type !== 'advertiser') {
                Toast.error('Unauthorized', 'You do not have access to this area');
                setTimeout(() => API.logout(), 2000);
            }
            if (user) {
                $('.user-name').text(user.name);
            }
        }
    });
</script>