<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="table.html">Manage Rooms</a></li>
                <li><a href="#">Add Room</a></li>
                <li><a href="#">Users</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            <header class="dashboard-header">
                <h1>Dashboard Overview</h1>
                <button class="add-room-button">Add New Room</button>
            </header>

            <!-- Room Overview Cards -->
            <div class="room-overview">
                <div class="card">
                    <h3>Total Rooms</h3>
                    <p>56</p>
                </div>
                <div class="card">
                    <h3>Available Rooms</h3>
                    <p>34</p>
                </div>
                <div class="card">
                    <h3>Booked Rooms</h3>
                    <p>22</p>
                </div>
                <div class="card">
                    <h3>Inactive Listings</h3>
                    <p>5</p>
                </div>
            </div>

            <!-- Recent Room Listings -->
            <section class="recent-rooms">
                <h2>Recent Room Listings</h2>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Room Title</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Spacious 2BHK Apartment</td>
                            <td>New York, NY</td>
                            <td>$2500/month</td>
                            <td>Active</td>
                            <td>Unbooked</td>
                        </tr>
                        <tr>
                            <td>Cozy Single Room</td>
                            <td>Los Angeles, CA</td>
                            <td>$1200/month</td>
                            <td>Inactive</td>
                            <td>Booked</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Graphical Data -->
            <section class="dashboard-chart">
                <h2>Room Status Overview</h2>
                <canvas id="roomChart"></canvas>
            </section>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script src="dashboard.js"></script>
</body>
</html>
