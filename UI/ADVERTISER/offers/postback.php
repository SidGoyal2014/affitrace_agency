<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Postback - Advertiser';
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
                <h1>Postback Integration</h1>
            </div>

            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Integration Settings</h6>
                </div>
                <div class="card-body">
                    <p>Use the following postback URL to notify us of conversions:</p>
                    <code>http://agency.affitrace.com/track/conversion?click_id={click_id}&amount={amount}</code>
                    <br /><br />
                    <p>Ensure you replace <code>{click_id}</code> with the click ID passed to you, and
                        <code>{amount}</code> with the conversion value.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>
</body>

</html>