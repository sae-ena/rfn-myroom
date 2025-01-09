<?php
require "leftSidebar.php";
require "dbConnect.php";


// Fetch data from the ROOMS table
$query = "SELECT   u.user_name,   u.user_email, r.room_location,  r.room_name,  r.room_image,      r.room_price,   b.booking_date,   b.status, b.booking_id
        FROM 
            bookings b
        JOIN 
            users u ON b.user_id = u.user_id
        JOIN 
            rooms r ON b.room_id = r.room_id
        WHERE 
            r.room_status ='inActive' AND b.status = 'confirmed';";
$stmt = $conn->prepare($query); // "i" denotes integer type for user_id
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Store the result in an array (optional if you need to manipulate later)
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

// Close connection
// $stmt->close();

?>
<?php if (isset($successfullyApprove)): ?>
    <div class="success-notify">
        <span><?php echo $successfullyApprove; ?></span>
    </div>
<?php endif; ?>


<div class="dashboard-content">
    <h1 class="roomH1" style="color:white">All Booked Room Records</h1>
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="">
            <div class="form-row">
                <!-- Status Filter -->
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">All</option>
                    <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo isset($_GET['status']) && $_GET['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <!-- Search Filter -->
                <label for="search">Search:</label>
                <input type="number" name="search" id="search"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    placeholder="Search...">

                <!-- Filter Button -->
                <button type="submit">Filter</button>
            </div>
        </form>
    </div>
    <table class="room-table">
        <thead>
            <tr>
                <th>UID</th>
                <th>Room Image</th>
                <th>Room Title</th>
                <th>Location</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>Booked Date</th>
                <th colspan="2" style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>


            <?php
            foreach ($rooms as $key => $room) {
                echo "<tr>
                                         <td>" . ++$key . "</td>
                                         <td> <img src='uploads/" . $room['room_image'] . "' alt='room image' style='width: 100px; height: 100px;'></td>
                                         <td>" . $room['room_name'] . "</td>
                                         <td>" . $room['room_location'] . "</td>
                                         <td>" . $room['user_name'] . "</td>
                                         <td>" . $room['user_email'] . "</td>
                                         <td>" . $room['booking_date'] . "</td>
                                         <td>
                                             <a href='/admin/approveView.php?booking_id=" . $room['booking_id'] . "'><button class='edit-button' style='left: 16px; top: 8px;'>View</button> </a>
                                             </td>
                                             <td>
                                             
                                         </td>
                                     </tr>";
            }
            ?>

            <!-- More rows as needed -->
        </tbody>
    </table>
</div>
</div>
</div>

</div>