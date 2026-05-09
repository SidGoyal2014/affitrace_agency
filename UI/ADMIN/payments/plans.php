<?php
$pathPrefix = '..';
$activePage = 'settings';
$pageTitle = 'Payment Plans';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Plan Details</h1>
            </div>

            <div class="row mb-4">

                <div class="col-6">
                    <div class="card-modern">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Plan name</th>
                                            <th>Price</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>    

                
                </div>
                <div class="col-6">
                    <div class="card-modern">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Attribute</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyPlanDetails">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>                        
                    
                </div>
            </div>



        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        function formatDecimal(v) { return v ? parseFloat(v).toFixed(2) : '0.00'; }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('admin/payments/plan-details');
                let plan_details = response.settings || [];
                let plans = response.dataPlans || [];
                
                const tbodyPlanDetails = $('#tableBodyPlanDetails');
                tbodyPlanDetails.empty();

                const tbody = $('#tableBody');
                tbody.empty();



                tbodyPlanDetails.append(`
                    <tr>
                        <td><strong>Allowed Affiliates:</strong></td>
                        <td>${plan_details.num_affiliates}</td>
                    </tr>
                    <tr>
                        <td><strong>Allowed Offers:</strong></td>
                        <td>${plan_details.num_offers}</td>
                    </tr>
                    <tr>
                        <td><strong>Allowed Clicks (Per month):</strong></td>
                        <td>${plan_details.num_clicks}</td>
                    </tr>
                    <tr>
                        <td><strong>Allowed Conversion (Per month):</strong></td>
                        <td>${plan_details.num_conversions}</td>
                    </tr>                    
                `);


                if (plans.length > 0) {
                    plans.forEach(p => {

                        let actionHtml = '';

                        if (p.id == plan_details.current_plan_id) {
                            actionHtml = `<span class="btn btn-outline-primary">Current Plan</span>`;
                        } else {
                            if (p.price > plan_details.current_plan_price) {
                                actionHtml = `<a href="#" class="btn btn-outline-warning">Upgrade</a>`;
                            }
                        }


                        tbody.append(`
                            <tr>
                                <td><strong>${p.plan_name}</strong></td>
                                <td>$${formatDecimal(p.price)}</td>
                                <td>
                                    ${actionHtml}
                                </td>                                
                            </tr>
                        `);
                    });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No plans found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>