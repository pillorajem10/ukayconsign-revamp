document.addEventListener('DOMContentLoaded', function() {
    // Get the breakdown data from the HTML element
    const breakdownData = JSON.parse(document.getElementById('breakdownData').getAttribute('data-breakdown'));

    console.log('breakdownData', breakdownData);

    // Extract labels and total sales data for the pie chart
    const labels = Object.keys(breakdownData);  // Product Bundle IDs
    const totalSales = labels.map(bundleId => breakdownData[bundleId].total);  // Corresponding Sale Totals (₱)
    const quantities = labels.map(bundleId => breakdownData[bundleId].quantity); // Corresponding Quantities (Optional for other charts)
    const totalProfit = labels.map(bundleId => breakdownData[bundleId].total_profit); // Corresponding Profit (₱)

    // Prepare Pie chart data for Total Sales
    const pieChartDataSales = {
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

    // Prepare Pie chart data for Total Profit
    const pieChartDataProfit = {
        labels: labels,
        datasets: [{
            label: 'Total Profit (₱)',
            data: totalProfit,  // Using the total profit values
            backgroundColor: [
                '#8B0000', '#006400', '#00008B', '#800080', '#FF8C00', 
                '#654321', '#A9A9A9', '#9B870C', '#008B8B', '#8B008B', 
                '#556B2F', '#483D8B', '#00CED1', '#9400D3', '#8FBC8F', 
                '#191970', '#B22222', '#D2691E', '#8B4513', '#CD5C5C', 
                '#228B22', '#708090', '#BDB76B', '#00CED1', '#8E4585',
                '#9932CC', '#E9967A', '#C71585', '#2F4F4F', '#FF1493',
                '#B8860B', '#BA55D3', '#4169E1', '#6495ED', '#00CED1', 
                '#2E8B57', '#6A0DAD', '#32CD32', '#7B68EE', '#483D8B',
                '#5F9EA0', '#6A5ACD', '#008080', '#0047AB'
            ],                                 
            borderColor: '#fff',
            borderWidth: 1
        }]
    };

    // Create the Pie chart for Total Sales
    const ctxSales = document.getElementById('salesPieChart').getContext('2d');
    const salesPieChart = new Chart(ctxSales, {
        type: 'pie',
        data: pieChartDataSales,
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

    // Create the Pie chart for Total Profit
    const ctxProfit = document.getElementById('profitPieChart').getContext('2d');
    const profitPieChart = new Chart(ctxProfit, {
        type: 'pie',
        data: pieChartDataProfit,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ₱' + tooltipItem.raw.toFixed(2);  // Display the total profit (₱) in the tooltip
                        }
                    }
                }
            }
        }
    });
});
