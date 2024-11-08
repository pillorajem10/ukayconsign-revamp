// sale-breakdown.js
document.addEventListener('DOMContentLoaded', function() {
    // Get the breakdown data from the HTML element
    const breakdownData = JSON.parse(document.getElementById('breakdownData').getAttribute('data-breakdown'));

    // Extract labels and data from the breakdown
    const labels = Object.keys(breakdownData);  // Product Bundle IDs
    const data = Object.values(breakdownData);  // Corresponding Sale Totals

    // Prepare Pie chart data
    const pieChartData = {
        labels: labels,
        datasets: [{
            label: 'Total Sales (₱)',
            data: data,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', 
                '#9966FF', '#FF6F61', '#C2C2C2', '#8B008B', '#A9A9A9'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    };

    // Create the Pie chart
    const ctx = document.getElementById('salesPieChart').getContext('2d');
    const salesPieChart = new Chart(ctx, {
        type: 'pie',
        data: pieChartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ₱' + tooltipItem.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });
});
