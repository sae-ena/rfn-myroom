<?php
include('leftSidebar.php');

echo'  
        <!-- Main Dashboard Content -->
        
    <div class="dashboard-content">
        <h1>All Added Rooms</h1>
        <table class="room-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Title</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Room Type</th>
                    <th>Facilities</th>
                    <th>Status</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Spacious 2BHK Apartment</td>
                    <td>New York, NY</td>
                    <td>$2500/month</td>
                    <td>2BHK</td>
                    <td>WiFi, AC, Gym</td>
                    <td>Active</td>
                    <td>Unbooked</td>
                    <td>
                        <button class="edit-button">Edit</button>
                        <button class="delete-button">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Cozy Single Room</td>
                    <td>Los Angeles, CA</td>
                    <td>$1200/month</td>
                    <td>Single Room</td>
                    <td>WiFi, Heating</td>
                    <td>Inactive</td>
                    <td>Booked</td>
                    <td>
                        <button class="edit-button">Edit</button>
                        <button class="delete-button">Delete</button>
                    </td>
                </tr>
                <!-- More rows as needed -->
            </tbody>
        </table>
    </div>
    </div>';

