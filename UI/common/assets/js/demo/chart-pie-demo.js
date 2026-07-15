// Modern Pie/Doughnut Chart - Affitrace
// Updated colors to match design system

Chart.defaults.global.defaultFontFamily = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
Chart.defaults.global.defaultFontColor = '#64748B';

// Modern color palette
const chartColors = [
  '#6366F1', // Primary indigo
  '#10B981', // Emerald
  '#F59E0B', // Amber
  '#EC4899', // Pink
  '#3B82F6', // Blue
  '#8B5CF6', // Violet
  '#14B8A6', // Teal
  '#F97316', // Orange
];

const chartHoverColors = [
  '#4F46E5',
  '#059669',
  '#D97706',
  '#DB2777',
  '#2563EB',
  '#7C3AED',
  '#0D9488',
  '#EA580C',
];

// Sleek Doughnut Chart
window.initPieChart = function (labels, data) {
  var ctx = document.getElementById("myPieChart");

  // Ensure data is an array
  if (typeof data === 'string') {
    try {
      data = JSON.parse(data);
    } catch (e) {
      data = [];
    }
  }

  if (!Array.isArray(data) || data.length === 0) {
    data = [1]; // Placeholder
    labels = ['No Data'];
  }

  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: chartColors.slice(0, data.length),
        hoverBackgroundColor: chartHoverColors.slice(0, data.length),
        borderWidth: 0,
        hoverBorderColor: "#fff",
        hoverBorderWidth: 3,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 70,
      rotation: -0.5 * Math.PI,
      animation: {
        animateRotate: true,
        animateScale: true
      },
      tooltips: {
        enabled: true,
        backgroundColor: "#0F172A",
        titleFontColor: '#fff',
        titleFontSize: 13,
        titleFontStyle: '600',
        bodyFontColor: '#CBD5E1',
        bodyFontSize: 12,
        xPadding: 14,
        yPadding: 12,
        displayColors: true,
        cornerRadius: 8,
        callbacks: {
          label: function (tooltipItem, data) {
            var dataset = data.datasets[tooltipItem.datasetIndex];
            var total = dataset.data.reduce(function (prev, curr) {
              return prev + parseFloat(curr);
            }, 0);
            var current = parseFloat(dataset.data[tooltipItem.index]);
            var percentage = ((current / total) * 100).toFixed(1);
            return ' $' + current.toFixed(2) + ' (' + percentage + '%)';
          }
        }
      },
      legend: {
        display: true,
        position: 'bottom',
        labels: {
          fontColor: '#64748B',
          fontSize: 12,
          padding: 20,
          usePointStyle: true,
          pointStyle: 'circle'
        }
      }
    },
  });
}
