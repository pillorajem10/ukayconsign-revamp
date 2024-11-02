document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Total Sales',
                data: Object.values(monthlySales),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Ordered Items Chart
    const ctxOrderedItems = document.getElementById('orderedItemsChart').getContext('2d');
    const orderedItemsChart = new Chart(ctxOrderedItems, {
        type: 'bar',
        data: {
            labels: Object.keys(orderedItemsSales), // Using product_bundle_id as labels
            datasets: [{
                label: 'Ordered Items Subtotal',
                data: Object.values(orderedItemsSales),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
