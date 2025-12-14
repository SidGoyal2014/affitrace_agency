<?php
$pathPrefix = '.';
$activePage = 'dashboard';
$pageTitle = 'Affiliate Dashboard';
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
                <h1>Dashboard</h1>
            </div>

            <!-- Stats Grid -->
            <div class="row">
                <!-- Total Active Offers -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Active Offers</div>
                                <div class="stat-value" id="total_offers">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Monthly Revenue</div>
                                <div class="stat-value" id="monthly_revenue">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offers Assigned -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-modern h-100">
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Offers Assigned</div>
                                <div class="stat-value" id="offers_assigned">...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Offers Row -->
            <div class="row">
                <!-- All New Offers -->
                <div class="col-xl-7 col-lg-7 mb-4">
                    <div class="card-modern h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">All New Offers</h6>
                            <span class="badge badge-primary" id="offer-count">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush mb-0">
                                    <thead>
                                        <tr>
                                            <th>Offer Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="offers_list">
                                        <tr>
                                            <td class="text-center py-4 text-muted">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Column -->
                <div class="col-xl-5 col-lg-5 mb-4">
                    <!-- Pie Chart -->
                    <div class="card-modern mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Top Revenue Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="myPieChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small" id="pie_legend">
                                <!-- Dynamic Content -->
                            </div>
                        </div>
                    </div>

                    <!-- Area Chart -->
                    <div class="card-modern">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Monthly Revenue</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <!-- Page level plugins -->
    <script src="<?php echo $pathPrefix; ?>/assets/vendor/chart.js/Chart.min.js"></script>

    <script>
        // Format numbers to 2 decimal places
        function formatDecimal(value) {
            if (value === null || value === undefined || value === '') return '0.00';
            return parseFloat(value).toFixed(2);
        }

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        $(document).ready(async function () {
            try {
                const data = await API.fetchWithAuth('affiliate/dashboard');

                // Update Stats
                $('#total_offers').text(data.all_offers ? data.all_offers.length : 0);

                let totalRev = 0;
                if (data.strRevenue) {
                    totalRev = data.strRevenue.reduce((a, b) => a + parseFloat(b), 0);
                }
                $('#monthly_revenue').text('$' + number_format(totalRev));

                $('#offers_assigned').text(data.offers_assigned ? data.offers_assigned.length : 0);

                // Render Area Chart (Revenue)
                let revenueData = [];
                try {
                    revenueData = JSON.parse(data.arrMonthlyRevenue);
                } catch (e) { revenueData = []; }

                var ctx = document.getElementById("myAreaChart");
                var myLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: "Earnings",
                            lineTension: 0.3,
                            backgroundColor: "rgba(99, 102, 241, 0.05)",
                            borderColor: "rgba(99, 102, 241, 1)",
                            pointRadius: 3,
                            pointBackgroundColor: "rgba(99, 102, 241, 1)",
                            pointBorderColor: "rgba(99, 102, 241, 1)",
                            pointHoverRadius: 3,
                            pointHoverBackgroundColor: "rgba(99, 102, 241, 1)",
                            pointHoverBorderColor: "rgba(99, 102, 241, 1)",
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: revenueData,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                        scales: {
                            xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                            yAxes: [{ ticks: { maxTicksLimit: 5, padding: 10, callback: function (value) { return '$' + number_format(value); } }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
                        },
                        legend: { display: false },
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            titleMarginBottom: 10,
                            titleFontColor: '#6e707e',
                            titleFontSize: 14,
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: { label: function (tooltipItem, chart) { var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || ''; return datasetLabel + ': $' + number_format(tooltipItem.yLabel); } }
                        }
                    }
                });

                // Pie Chart
                var ctxPie = document.getElementById("myPieChart");
                var myPieChart = new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: data.labelCategories,
                        datasets: [{
                            data: data.strRevenue,
                            backgroundColor: ['#6366F1', '#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
                            hoverBackgroundColor: ['#4F46E5', '#059669', '#2563EB', '#D97706', '#DC2626'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            caretPadding: 10,
                        },
                        legend: { display: false },
                        cutoutPercentage: 80,
                    },
                });

                // Populate Legend
                let legendHtml = '';
                const colors = ['text-primary', 'text-success', 'text-info', 'text-warning', 'text-danger'];
                if (data.labelCategories) {
                    data.labelCategories.forEach((cat, index) => {
                        legendHtml += `<span class="mr-2"><i class="fas fa-circle ${colors[index % colors.length]}"></i> ${cat}</span>`;
                    });
                }
                $('#pie_legend').html(legendHtml);

                // Render Offers List
                let offersHtml = '';
                if (data.all_offers && data.all_offers.length > 0) {
                    $('#offer-count').text(data.all_offers.length);
                    data.all_offers.slice(0, 10).forEach(offer => {
                        offersHtml += `
                            <tr>
                                <td>
                                    <div class="py-2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="text-primary">${offer.offer_name}</strong>
                                            <span class="badge badge-success">$${offer.offer_price_usd || '0.00'}</span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            Category: <strong>${offer.category_name || 'Uncategorized'}</strong>
                                        </div>
                                        <div class="small text-secondary mb-2">${offer.offer_desc || 'No description available.'}</div>
                                        <div class="text-right">
                                            ${data.arr_affiliate_offers.includes(offer.id.toString()) ?
                                '<span class="badge badge-success">Assigned</span>' :
                                `<button class="btn btn-sm btn-primary" onclick="alert('Request feature pending'); return false;">Apply for Approval</button>`
                            }
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    offersHtml = '<tr><td class="text-center py-4 text-muted">No new offers found.</td></tr>';
                }
                $('#offers_list').html(offersHtml);


            } catch (error) {
                console.error('Dashboard load failed:', error);
                $('#offers_list').html(`<tr><td class="text-center text-danger">Failed to load data: ${error.message}</td></tr>`);
            }
        });
    </script>
</body>

</html>