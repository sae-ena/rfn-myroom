// Example Chart for Room Status Overview
const ctx = document.getElementById('roomChart').getContext('2d');
const roomChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Available Rooms', 'Booked Rooms', 'Inactive Listings'],
        datasets: [{
            label: 'Room Status Overview',
            data: [34, 22, 5],
            backgroundColor: [
                '#4CAF50',  // Available Rooms
                '#ff6600',  // Booked Rooms
                '#f44336'   // Inactive Listings
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
