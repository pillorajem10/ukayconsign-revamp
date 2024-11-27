document.addEventListener('DOMContentLoaded', function() {
    // Get the breakdown data from the HTML element
    const breakdownData = JSON.parse(document.getElementById('breakdownData').getAttribute('data-breakdown'));

    console.log('breakdownData', breakdownData);

    // Extract labels and total sales data for the pie chart
    const labels = Object.keys(breakdownData);  // Product Bundle IDs
    const totalSales = labels.map(bundleId => breakdownData[bundleId].total);  // Corresponding Sale Totals (₱)
    const quantities = labels.map(bundleId => breakdownData[bundleId].quantity); // Corresponding Quantities (Optional for other charts)

    // Prepare Pie chart data
    const pieChartData = {
        labels: labels,
        datasets: [{
            label: 'Total Sales (₱)',
            data: totalSales,  // Using the total sales values
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', 
                '#9966FF', '#FF6F61', '#C2C2C2', '#8B008B', '#A9A9A9', 
                '#00FF7F', '#FFD700', '#F08080', '#D2691E', '#4682B4', 
                '#8A2BE2', '#FF4500', '#2E8B57', '#A52A2A', '#20B2AA', 
                '#0000FF', '#FF1493', '#FFD700', '#8B4513', '#C71585', 
                '#20B2AA', '#7B68EE', '#FF6347', '#DDA0DD', '#ADFF2F',
                '#FF8C00', '#3CB371', '#B22222', '#FF69B4', '#9400D3',
                '#00FA9A', '#008B8B', '#A52A2A', '#800080', '#00BFFF'
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
                            return tooltipItem.label + ': ₱' + tooltipItem.raw.toFixed(2);  // Display the total sales (₱) in the tooltip
                        }
                    }
                }
            }
        }
    });
});
