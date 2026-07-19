<?php
$pathPrefix = '.';
$activePage = 'wallet';
$pageTitle = 'Wallet & Payouts';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h1>Wallet & Payouts</h1>
                <button type="button" class="btn btn-primary" id="btnRequestPayout">
                    <i class="fas fa-hand-holding-usd mr-1"></i> Request Payout
                </button>
            </div>

            <!-- Wallet Overview Row -->
            <div class="row mb-4">
                <!-- Available Balance -->
                <div class="col-md-4">
                    <div class="card-modern bg-gradient-primary text-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-white-50 small font-weight-bold uppercase mb-2">Available Balance</div>
                                    <div class="h2 font-weight-bold mb-0" id="balanceDisplay">$0.00</div>
                                </div>
                                <div class="icon-circle bg-white-20 text-white">
                                    <i class="fas fa-wallet fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Earnings -->
                <div class="col-md-4">
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-muted small font-weight-bold uppercase mb-2">Pending Earnings</div>
                                    <div class="h2 font-weight-bold mb-0 text-warning" id="pendingDisplay">$0.00</div>
                                </div>
                                <div class="icon-circle bg-warning-10 text-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payout Threshold -->
                <div class="col-md-4">
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-muted small font-weight-bold uppercase mb-2">Minimum Threshold</div>
                                    <div class="h2 font-weight-bold mb-0 text-success" id="thresholdDisplay">$50.00</div>
                                </div>
                                <div class="icon-circle bg-success-10 text-success">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout History Card -->
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-history mr-2"></i>Payout Requests History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Week Period</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Date Requested</th>
                                </tr>
                            </thead>
                            <tbody id="payoutHistoryList">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Loading history...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-4">
                        <span class="small text-muted" id="paginationInfo">Showing 0 to 0 of 0 entries</span>
                        <nav aria-label="Payout Page Navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled" id="btnPrev">
                                    <button class="page-link" type="button">Previous</button>
                                </li>
                                <li class="page-item disabled" id="btnNext">
                                    <button class="page-link" type="button">Next</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let currentPage = 1;
        const limitPerPage = 10;

        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = 'login.php';
                return;
            }

            // Initial load
            loadWalletAndHistory(currentPage);

            // Payout Request submit
            $('#btnRequestPayout').on('click', async function () {
                const btn = $(this);
                if (!confirm('Are you sure you want to request a payout of your entire available balance?')) {
                    return;
                }

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Requesting...');

                try {
                    const response = await API.fetchWithAuth('affiliate/payouts/request', {
                        method: 'POST'
                    });

                    Toast.success('Success', response.message || 'Payout request submitted successfully.');
                    loadWalletAndHistory(currentPage);
                } catch (error) {
                    console.error('Payout request failed:', error);
                    Toast.error('Request Failed', error.message || 'Failed to submit payout request.');
                } finally {
                    btn.prop('disabled', false).html('<i class="fas fa-hand-holding-usd mr-1"></i> Request Payout');
                }
            });

            // Pagination handlers
            $('#btnPrev button').on('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    loadWalletAndHistory(currentPage);
                }
            });

            $('#btnNext button').on('click', function () {
                currentPage++;
                loadWalletAndHistory(currentPage);
            });
        });

        async function loadWalletAndHistory(page) {
            try {
                const res = await API.fetchWithAuth(`affiliate/payouts/history?page=${page}&limit=${limitPerPage}`);

                // Update Overview Metrics
                $('#balanceDisplay').text('$' + formatDecimal(res.available_balance));
                $('#pendingDisplay').text('$' + formatDecimal(res.pending_earnings));
                $('#thresholdDisplay').text('$' + formatDecimal(res.payout_threshold));

                // Populate History Table
                const list = $('#payoutHistoryList');
                list.empty();

                const payoutsData = res.payouts;
                if (payoutsData && payoutsData.data && payoutsData.data.length > 0) {
                    payoutsData.data.forEach(payout => {
                        let statusBadge = '<span class="badge badge-secondary">Pending</span>';
                        if (payout.status === 'PAID') {
                            statusBadge = '<span class="badge badge-success">Paid</span>';
                        } else if (payout.status === 'PROCESSING') {
                            statusBadge = '<span class="badge badge-warning">Processing</span>';
                        } else if (payout.status === 'REJECTED') {
                            statusBadge = '<span class="badge badge-danger">Rejected</span>';
                        }

                        // Format created date
                        const reqDate = payout.created_at ? new Date(payout.created_at).toLocaleDateString() : 'N/A';

                        list.append(`
                            <tr>
                                <td>#${payout.id}</td>
                                <td>${payout.week_start} to ${payout.week_end}</td>
                                <td>${payout.entry_type || 'Manual'}</td>
                                <td class="font-weight-bold text-dark">$${formatDecimal(payout.total_amount)}</td>
                                <td>${statusBadge}</td>
                                <td class="small text-muted">${payout.notes || '—'}</td>
                                <td>${reqDate}</td>
                            </tr>
                        `);
                    });

                    // Update Pagination UI
                    const from = payoutsData.from || 0;
                    const to = payoutsData.to || 0;
                    const total = payoutsData.total || 0;
                    $('#paginationInfo').text(`Showing ${from} to ${to} of ${total} entries`);

                    // Prev Button
                    if (payoutsData.prev_page_url) {
                        $('#btnPrev').removeClass('disabled');
                    } else {
                        $('#btnPrev').addClass('disabled');
                    }

                    // Next Button
                    if (payoutsData.next_page_url) {
                        $('#btnNext').removeClass('disabled');
                    } else {
                        $('#btnNext').addClass('disabled');
                    }
                } else {
                    list.html('<tr><td colspan="7" class="text-center py-4 text-muted">No payout requests history found.</td></tr>');
                    $('#paginationInfo').text('Showing 0 to 0 of 0 entries');
                    $('#btnPrev').addClass('disabled');
                    $('#btnNext').addClass('disabled');
                }

            } catch (error) {
                console.error('Failed to load wallet data:', error);
                Toast.error('Load Failed', 'Could not load wallet data.');
                $('#payoutHistoryList').html('<tr><td colspan="7" class="text-center text-danger py-4">Failed to load data.</td></tr>');
            }
        }

        function formatDecimal(val) {
            if (val === null || val === undefined || val === '') return '0.00';
            return parseFloat(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    </script>
</body>

</html>
