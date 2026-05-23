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
        async function loadSubscriptions() {
            try {
                const response = await API.fetchWithAuth('admin/payments/show-subscriptions');
                let subs = response.data || [];
                const tbody = $('#tableBody');
                tbody.empty();

                if (subs.length > 0) {
                    subs.forEach(s => {
                        const actionHtml = (obj) => {
                            if (obj.status === "Deleted") {
                                return `<span class="btn btn-danger">Deleted</span><div>&nbsp;</div> <button onclick="togglePaymentDetails('${obj.sub_id}')" class="btn btn-outline-info">Payment Details</button>`;
                            }

                            let buttons = '';

                            if (obj.status === "Created" || obj.status === "Resumed") {
                                buttons += `<button type="button" class="btn btn-outline-warning subscription-action" data-action="pause" data-id="${obj.sub_id}">Pause</button> `;
                            } else if (obj.status === "Paused") {
                                buttons += `<button type="button" class="btn btn-primary subscription-action" data-action="resume" data-id="${obj.sub_id}">Resume</button> `;
                            }

                            buttons += `<button type="button" class="btn btn-outline-danger subscription-action" data-action="delete" data-id="${obj.sub_id}">Delete</button>`;
                            buttons += `<div>&nbsp;</div>`;
                            buttons += ` <button onclick="togglePaymentDetails('${obj.sub_id}')" class="btn btn-outline-info">Payment Details</button>`;
                            return buttons;
                        };

                        tbody.append(`
                            <tr>
                                <td>
                                    <strong>Stripe PlanId:</strong><br />${s.stripe_plan_id}<br />
                                    <strong>Plan Name:</strong>${s.plan_name}
                                </td>
                                <td>
                                    <strong>Customer Id:</strong><br />${s.pg_customer_id}<br /><br />
                                    <strong>Subscription Id:</strong><br />${s.stripe_subscription_id}
                                </td>
                                <td>
                                    <strong>Amount:</strong>${s.subscription_amount}<br />
                                    <strong>Start date:</strong>${s.subscription_start_date}<br />
                                    <strong>End date:</strong>${s.expires_at}
                                </td>
                                <td width="20%">${actionHtml(s)}</td>
                            </tr>
                        `);
                        tbody.append(`
                            <tr id="pmt-row-${s.sub_id}" class="d-none">
                                <td colspan="4" id="pmt-details-${s.sub_id}"><em>Loading...</em></td>
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
        }

        $(document).ready(async function () {
            if (!API.isAuthenticated()) { window.location.href = '../login.php'; return; }
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) $('.user-name').text(user.name);

            await loadSubscriptions();
        });

        $(document).on('click', '.subscription-action', async function () {
            const $btn = $(this);
            const action = $btn.data('action');
            const id = $btn.data('id');

            if (!action || !id) {
                return;
            }

            if (action === 'delete' && !window.confirm('Are you sure you want to delete this subscription?')) {
                return;
            }

            let endpoint = '';
            if (action === 'pause') {
                endpoint = 'admin/payments/pause-subscription/' + id;
            } else if (action === 'resume') {
                endpoint = 'admin/payments/resume-subscription/' + id;
            } else if (action === 'delete') {
                endpoint = 'admin/payments/delete-subscription/' + id;
            } else {
                return;
            }

            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Please wait...');

            try {
                const res = await API.fetchWithAuth(endpoint);
                const successMsg = (res && (res.message || res.msg)) ? (res.message || res.msg) : 'Subscription updated successfully';
                window.alert(successMsg);
                await loadSubscriptions();
            } catch (error) {
                console.error(error);
                window.alert(error?.message || 'Action failed. Please try again.');
            } finally {
                $btn.prop('disabled', false).text(originalText);
            }
        });

        async function togglePaymentDetails(sub_id) {
            const row = $(`#pmt-row-${sub_id}`);
            const cell = $(`#pmt-details-${sub_id}`);

            if (!row.hasClass('d-none')) {
                row.addClass('d-none');
                return;
            }

            row.removeClass('d-none');

            if (cell.data('loaded')) return; // already fetched

            try {
                const res = await API.fetchWithAuth('admin/reports/subPmtDetails/' + sub_id);
                const pmts = res.data || [];

                if (pmts.length === 0) {
                    cell.html('<em class="text-muted">No payment records found.</em>');
                } else {
                    const rows = pmts.map(p => `
                        <tr>
                            <td>
                                <strong>Plan: </strong>${p.plan_name || 'N/A'} <br> 
                                <strong>Type: </strong>${p.plan_type || p.activity || 'N/A'} <br> 
                                <strong>Cost: </strong>${p.subscription_amount || p.transaction_amount || 'N/A'}
                            </td>
                            <td>
                                <strong>Billing: </strong>${p.billing || 'N/A'} <br> 
                                <strong>Reason: </strong>${p.billing_reason || 'N/A'} <br> 
                                <strong>Charge: </strong>${p.billing_charge || 'N/A'}
                            </td>
                            <td>
                                <strong>Trans Date: </strong>${p.transaction_date || p.activity_date || 'N/A'} <br>
                                <strong>Activity: </strong>${p.activity || 'N/A'}
                            </td>
                        </tr>
                    `).join('');
                    cell.html(`<table class="table table-sm table-bordered mb-0"><tbody>${rows}</tbody></table>`);
                }

                


                
                cell.data('loaded', true);
            } catch (e) {
                cell.html('<span class="text-danger">Failed to load payment details.</span>');
            }
        }

    </script>
</body>

</html>