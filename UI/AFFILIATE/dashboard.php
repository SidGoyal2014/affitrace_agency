<?php
$pageTitle = 'Affiliate Dashboard';
include 'includes/header.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Stats Grid -->
    <div class="row">
        <!-- Total Offers -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Active Offers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_offers">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthly_revenue">$0.00</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Offers -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Offers Assigned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="offers_assigned">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-7 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All new offers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="offers_list">
                                <!-- Dynamic Content -->
                                <tr>
                                    <td class="text-center">Loading offers...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="col-xl-5 col-lg-6">

            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly revenue collection</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top revenue categories</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small" id="pie_legend">
                        <!-- Dynamic Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include $pathPrefix . '/includes/footer.php'; ?>

<!-- Page level plugins -->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/chart.js/Chart.min.js"></script>

<script>
    $(document).ready(async function () {
        try {
            const data = await API.fetchWithAuth('affiliate/dashboard');

            // Render Charts
            // Area Chart (Revenue)
            // Parse string array "[200, 300, ...]" if needed, or if API sends array directly
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
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
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
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
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


            // Update Stats
            $('#total_offers').text(data.all_offers ? data.all_offers.length : 0);

            let totalRev = 0;
            if (data.strRevenue) {
                totalRev = data.strRevenue.reduce((a, b) => a + parseFloat(b), 0);
            }
            $('#monthly_revenue').text('$' + number_format(totalRev));

            $('#offers_assigned').text(data.offers_assigned ? data.offers_assigned.length : 0);

            // Render Offers List
            let offersHtml = '';
            if (data.all_offers && data.all_offers.length > 0) {
                data.all_offers.slice(0, 10).forEach(offer => { // Show top 10
                    offersHtml += `
                        <tr>
                            <td colspan="6">
                                <table id="border-none" style="width:100%">
                                    <tr>
                                        <td style="width:45%">
                                            <strong>Category: </strong><br />
                                            ${offer.category_name || 'Uncategorized'}
                                        </td>
                                        <td style="width:40%">
                                            <strong>Offer: </strong><br />
                                            ${offer.offer_name}
                                        </td>
                                        <td style="width:15%">
                                            <strong>Payout: </strong><br />
                                            $${offer.offer_price_usd || '0.00'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">${offer.offer_desc || 'No description available.'}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align:right;">
                                            ${data.arr_affiliate_offers.includes(offer.id.toString()) ?
                            '<a href="#" class="btn btn-success btn-sm disabled">Assigned</a>' :
                            `<a href="#" class="btn btn-info btn-sm" onclick="alert('Request feature pending'); return false;">Apply for Approval</a>`
                        }
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    `;
                });
            } else {
                offersHtml = '<tr><td class="text-center">No new offers found.</td></tr>';
            }
            $('#offers_list').html(offersHtml);


        } catch (error) {
            console.error('Dashboard load failed:', error);
            $('#offers_list').html(`<tr><td class="text-center text-danger">Failed to load data: ${error.message}</td></tr>`);
        }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
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
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
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
</script>