document.addEventListener('DOMContentLoaded', function() {
    // Function to truncate labels
    function truncateLabel(label, maxLength) {
        return label.length > maxLength ? label.substring(0, maxLength) + '...' : label;
    }

    // Truncate labels for the sales chart
    const salesLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].map(label => truncateLabel(label, 3));

    // Sales Chart
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctxSales, {
        type: 'bar', // Vertical bar chart
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Total Sales',
                data: Object.values(monthlySales),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Set to horizontal
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 10 // Font size for x-axis labels
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    min: 0,
                    max: Math.max(...Object.values(monthlySales)), // Set max to the maximum sales value
                    stepSize: 1 // Step size to ensure integers
                },
                y: {
                    ticks: {
                        font: {
                            size: 10 // Font size for y-axis labels
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10 // Font size for legend labels
                        }
                    }
                }
            }
        }
    });

    // Truncate labels for ordered items chart
    const orderedItemsLabels = Object.keys(orderedItemsSales).map(label => truncateLabel(label, 10));

    // Ordered Items Chart
    const ctxOrderedItems = document.getElementById('orderedItemsChart').getContext('2d');
    const orderedItemsChart = new Chart(ctxOrderedItems, {
        type: 'bar', // Vertical bar chart
        data: {
            labels: orderedItemsLabels, // Truncated labels
            datasets: [{
                label: 'Ordered Items Subtotal',
                data: Object.values(orderedItemsSales),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Set to horizontal
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 10 // Font size for x-axis labels
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    min: 0,
                    max: Math.max(...Object.values(orderedItemsSales)), // Set max to the maximum subtotal
                    stepSize: 1
                },
                y: {
                    ticks: {
                        font: {
                            size: 10 // Font size for y-axis labels
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10 // Font size for legend labels
                        }
                    }
                }
            }
        }
    });

    // Truncate labels for quantity chart
    const quantityLabels = Object.keys(quantityPerBundle).map(label => truncateLabel(label, 10));

    // Quantity per Product Bundle Chart
    const ctxQuantity = document.getElementById('quantityChart').getContext('2d');
    const quantityChart = new Chart(ctxQuantity, {
        type: 'bar', // Vertical bar chart
        data: {
            labels: quantityLabels, // Truncated labels
            datasets: [{
                label: 'Quantity per Product Bundle',
                data: Object.values(quantityPerBundle),
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Set to horizontal
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 10 // Font size for x-axis labels
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    min: 0,
                    max: Math.max(...Object.values(quantityPerBundle)), // Set max to the maximum quantity
                    stepSize: 1
                },
                y: {
                    ticks: {
                        font: {
                            size: 10 // Font size for y-axis labels
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10 // Font size for legend labels
                        }
                    }
                }
            }
        }
    });

    // Truncate labels for daily sales chart
    const dailySalesLabels = Object.keys(dailySales).map(label => truncateLabel(label, 10));

    // Daily Sales Chart
    const ctxDailySales = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesChart = new Chart(ctxDailySales, {
        type: 'bar', // Vertical bar chart
        data: {
            labels: dailySalesLabels, // Truncated labels
            datasets: [{
                label: 'Daily Sales Subtotal',
                data: Object.values(dailySales),
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Set to horizontal
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 10 // Font size for x-axis labels
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    min: 0,
                    max: Math.max(...Object.values(dailySales)), // Set max to the maximum daily sales value
                    stepSize: 1 // Step size to ensure integers
                },
                y: {
                    ticks: {
                        font: {
                            size: 10 // Font size for y-axis labels
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10 // Font size for legend labels
                        }
                    }
                }
            }
        }
    });
});
