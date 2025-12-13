</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Affitrace 2023</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Bootstrap core JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo $pathPrefix; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo $pathPrefix; ?>/assets/js/sb-admin-2.min.js"></script>

<!-- API scripts -->
<script src="<?php echo $pathPrefix; ?>/../ADMIN/assets/js/api.js"></script>

<script>
    // Check auth for affiliate pages
    $(document).ready(function () {
        if (window.location.pathname.endsWith('login.php') || window.location.pathname.endsWith('login.html')) return;

        if (!API.isAuthenticated()) {
            window.location.href = '<?php echo $pathPrefix; ?>/../ADMIN/login.php';
        } else {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user && user.user_type !== 'affiliate') {
                alert('Unauthorized access');
                API.logout();
            }
            if (user) {
                $('.mr-2.d-none.d-lg-inline.text-gray-600.small').text(user.name);
            }
        }
    });
</script>