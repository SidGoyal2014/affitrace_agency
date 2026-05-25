<?php
$pathPrefix = '..';
$activePage = 'offers';
$pageTitle = 'Create Offer - Advertiser';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1>Create Offer</h1>
            </div>

            <div class="card-modern">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">New Offer Details</h6>
                </div>
                <div class="card-body">
                    <form id="createOfferForm">
                        <div class="form-group">
                            <label for="offerName">Offer Name</label>
                            <input type="text" class="form-control" id="offerName" placeholder="Enter offer name"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="offerBudget">Budget</label>
                            <input type="number" class="form-control" id="offerBudget" placeholder="Enter budget"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Offer</button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(function () {
            $('#createOfferForm').on('submit', function (e) {
                e.preventDefault();
                // TODO: API integration
                Toast.info('Coming Soon', 'Offer creation feature is under development.');
            });
        });
    </script>
</body>

</html>