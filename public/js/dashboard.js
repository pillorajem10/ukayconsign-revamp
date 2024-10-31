document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const promoModal = document.getElementById('promoModal');
    const closeModal = document.getElementById('closeModal');
    const showPromosButton = document.getElementById('showPromosButton');

    document.getElementById('storeSelect').addEventListener('change', function() {
        const storeId = this.value;
    
        // Optionally, you can submit a form or fetch data using AJAX to get updated monthly data
        // For simplicity, let's assume you're refreshing the page with the selected store
        window.location.href = `?store_id=${storeId}`; // Redirect with the selected store ID
    });
    

    // Show the loading overlay when the page starts loading
    loadingOverlay.style.display = 'flex'; // Ensure it's visible and centered

    // Hide the loading overlay after a slight delay
    setTimeout(() => {
        loadingOverlay.style.display = 'none'; // Hide overlay
    
        // Check if the promo modal has been shown before
        const promoData = sessionStorage.getItem('promoData');
    
        if (!promoData || (Date.now() - JSON.parse(promoData).timestamp > 900000)) {
            promoModal.style.display = 'flex'; // Show the modal
    
            // Hide the promo modal after 3 seconds
            setTimeout(() => {
                promoModal.style.display = 'none'; // Hide the modal
            }, 3000); // 3000 milliseconds = 3 seconds
    
            // Set flag in session storage with a timestamp
            sessionStorage.setItem('promoData', JSON.stringify({ timestamp: Date.now() }));
        }
    
        // Apply slide-in classes to dashboard containers
        const dashboardContainers = document.querySelectorAll('.dashboard-container');
        dashboardContainers.forEach((container, index) => {
            if (index % 2 === 0) {
                container.classList.add('slide-in-left');
            } else {
                container.classList.add('slide-in-right');
            }
        });
    }, 100);
    

    // Close modal when the close button is clicked
    /*
    closeModal.addEventListener('click', function() {
        promoModal.style.display = 'none'; // Hide the modal immediately
    });

    // Close modal when clicking outside of it
    promoModal.addEventListener('click', function(event) {
        if (event.target === promoModal) {
            promoModal.style.display = 'none'; // Hide the modal if clicking outside
        }
    });

    // Show the promo modal when the button is clicked
    showPromosButton.addEventListener('click', function() {
        promoModal.style.display = 'flex'; // Show the modal
    })
    */

    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    console.log('MONTHLY DATAAA', monthlyData);
    const monthlySalesChart = new Chart(ctx, {
        type: 'bar', // or 'line' for a line chart
        data: {
            labels: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 
                'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 
                'Nov', 'Dec'
            ],
            datasets: [{
                label: 'Total Sales (₱)',
                data: monthlyData.map(value => parseFloat(value).toFixed(2)),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sales (₱)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }
            }
        }
    });
});
