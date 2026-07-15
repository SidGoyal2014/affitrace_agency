// Revenue vs Payout Chart - Affitrace Admin Dashboard
// Chart.js v2 — mirrors chart-area-demo.js patterns

window.revenuePayoutChartInstance = null;

window.initRevenuePayoutChart = function (labels, revenueData, payoutData) {

    // Destroy previous instance before re-draw (period toggle support)
    if (window.revenuePayoutChartInstance) {
        window.revenuePayoutChartInstance.destroy();
        window.revenuePayoutChartInstance = null;
    }

    var ctx = document.getElementById('revenuePayoutChart');
    if (!ctx) return;

    var ctxContext = ctx.getContext('2d');

    // Gradient fill for Revenue line (indigo)
    var revenueGradient = ctxContext.createLinearGradient(0, 0, 0, 300);
    revenueGradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
    revenueGradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    // Gradient fill for Payout line (green)
    var payoutGradient = ctxContext.createLinearGradient(0, 0, 0, 300);
    payoutGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    payoutGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    window.revenuePayoutChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    lineTension: 0.4,
                    backgroundColor: revenueGradient,
                    borderColor: '#6366F1',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#6366F1',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366F1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    pointHitRadius: 20,
                    data: revenueData,
                    fill: true,
                },
                {
                    label: 'Payout',
                    lineTension: 0.4,
                    backgroundColor: payoutGradient,
                    borderColor: '#10B981',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10B981',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    pointHitRadius: 20,
                    data: payoutData,
                    fill: true,
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 20,
                    bottom: 10
                }
            },
            legend: {
                display: true,
                position: 'top',
                labels: {
                    fontColor: '#64748B',
                    fontSize: 12,
                    usePointStyle: true,
                    padding: 20
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        fontColor: '#94A3B8',
                        fontSize: 11,
                        padding: 10,
                        maxTicksLimit: 12,
                        maxRotation: 45
                    }
                }],
                yAxes: [{
                    gridLines: {
                        color: 'rgba(100,116,139,0.08)',
                        drawBorder: false,
                        borderDash: [4, 4]
                    },
                    ticks: {
                        fontColor: '#94A3B8',
                        fontSize: 11,
                        padding: 15,
                        maxTicksLimit: 6,
                        callback: function (value) {
                            return '$' + number_format(value, 0);
                        }
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                backgroundColor: '#1E293B',
                titleFontColor: '#F8FAFC',
                titleFontSize: 12,
                bodyFontColor: '#CBD5E1',
                bodyFontSize: 12,
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                xPadding: 12,
                yPadding: 10,
                callbacks: {
                    label: function (tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        return ' ' + dataset.label + ': $' + number_format(tooltipItem.yLabel, 2);
                    }
                }
            }
        }
    });
};
