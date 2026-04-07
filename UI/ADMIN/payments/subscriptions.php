<?php
$pathPrefix = '..';
$activePage = 'settings';
$pageTitle = 'Subscriptions';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Subscriptions</h1>
            </div>

            <div class="card-modern">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Active Subscriptions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Subscription</th>
                                    <th>Subscription Details</th>
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
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            try {
                const response = await API.fetchWithAuth('admin/payments/show-subscriptions');
                let subs = response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                if (subs.length > 0) {
                    subs.forEach(s => {


                        const actionHtml = (obj) => {
                        if (obj.status === "Deleted") {
                            return `<span class="btn btn-danger">Deleted</span>`;
                        }

                        let buttons = '';

                        if (obj.status === "Created" || obj.status === "Resumed") {
                            buttons += `<a href="/admin/payments/pause_subscription/${obj.ssdid}" class="btn btn-outline-warning">Pause</a> `;
                        } else if (obj.status === "Paused") {
                            buttons += `<a href="/admin/payments/resume_subscription/${obj.ssdid}" class="btn btn-primary">Resume</a> `;
                        }

                        buttons += `<a href="/admin/payments/delete_subscription/${obj.ssdid}" class="btn btn-outline-danger">Delete</a>`;

                        return buttons;
                        };



                        tbody.append(`
                            <tr>
                                <td>
                                    <strong>Stripe PlanId:</strong><br />${s.stripe_plan_id}}<br />
                                    <strong>Plan Name:</strong>${s.plan_name}
                                </td>
                                <td>
                                    <strong>Customer Id:</strong><br />${s.pg_customer_id}<br /><br />
                                    <strong>Subscription Id:</strong><br />${s.stripe_subscription_id}
                                </td>
                                <td>
                                    <strong>Amount:</strong>${s.subscription_amount}<br />
                                    <strong>Start date:</strong>${s.subscription_start_date }<br />
                                    <strong>End date:</strong>${s.expires_at }
                                </td>
                                <td width="20%">${actionHtml(s)}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No subscriptions found</td></tr>');
                }
            } catch (error) {
                console.error(error);
                $('#tableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load</td></tr>');
            }
        });
    </script>
</body>

</html>