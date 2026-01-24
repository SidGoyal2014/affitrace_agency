// Modern Chart Styling - Affitrace
// Updated font and colors to match design system

Chart.defaults.global.defaultFontFamily = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
Chart.defaults.global.defaultFontColor = '#64748B';

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

// Sleek Area Chart
window.initAreaChart = function (data) {
  var ctx = document.getElementById("myAreaChart");

  // Create gradient
  var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
  gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
  gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

  var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Revenue",
        lineTension: 0.4,
        backgroundColor: gradient,
        borderColor: "#6366F1",
        borderWidth: 3,
        pointRadius: 0,
        pointBackgroundColor: "#6366F1",
        pointBorderColor: "#fff",
        pointHoverRadius: 6,
        pointHoverBackgroundColor: "#6366F1",
        pointHoverBorderColor: "#fff",
        pointHoverBorderWidth: 2,
        pointHitRadius: 20,
        data: data,
        fill: true,
      }],
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
      scales: {
        xAxes: [{
          gridLines: {
            display: false,
            drawBorder: false
          },
          ticks: {
            fontColor: '#94A3B8',
            fontSize: 11,
            padding: 10
          }
        }],
        yAxes: [{
          ticks: {
            maxTicksLimit: 5,
            padding: 15,
            fontColor: '#94A3B8',
            fontSize: 11,
            callback: function (value) {
              return '$' + number_format(value);
            }
          },
          gridLines: {
            color: "rgba(226, 232, 240, 0.5)",
            zeroLineColor: "rgba(226, 232, 240, 0.5)",
            drawBorder: false,
            borderDash: [4, 4]
          }
        }],
      },
      legend: {
        display: false
      },
      tooltips: {
        enabled: true,
        backgroundColor: "#0F172A",
        titleFontColor: '#fff',
        titleFontSize: 13,
        titleFontStyle: '600',
        bodyFontColor: '#CBD5E1',
        bodyFontSize: 12,
        borderColor: 'transparent',
        borderWidth: 0,
        xPadding: 14,
        yPadding: 12,
        displayColors: false,
        intersect: false,
        mode: 'index',
        cornerRadius: 8,
        callbacks: {
          title: function (tooltipItems) {
            return tooltipItems[0].xLabel + ' 2024';
          },
          label: function (tooltipItem, chart) {
            return '$' + number_format(tooltipItem.yLabel, 2);
          }
        }
      },
      hover: {
        mode: 'index',
        intersect: false
      }
    }
  });
}
