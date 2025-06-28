<?php
require_once "leftSidebar.php";
require_once "dbConnect.php";  // Make sure this file contains the correct database connection

// Fetch data from the ROOMS table
$query = "SELECT *,(SELECT COUNT(*) FROM rooms WHERE room_status = 'active') AS activeCount FROM rooms ";
$queryUser = " Count(user_id) FROM users";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $totalRooms = $result->num_rows;
    // Store the result in an array (optional if you need to manipulate later)
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
      
        $activeRooms = $row['activeCount'];
        $rooms[] = $row;
    }
    $inActiveRoom = $totalRooms - $activeRooms;
} 
?>

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            <header class="dashboard-header">
                <h1 style="color:white;font-family: Arial, sans-serif;">Dashboard Overview</h1>
                <button ><a href="form.php" class="add-room-button"> Add New Room</a></button>
            </header>

            <!-- Room Overview Cards -->
            <div class="room-overview">
                <div class="card">
                    <h3>Total Rooms</h3>
                    <p><?php echo $totalRooms ?></p>
                </div>
                <div class="card">
                    <h3>Available Rooms</h3>
                    <p><?php echo $activeRooms?></p>
                </div>
                <div class="card">
                    <h3>Booked Rooms</h3>
                    <p>22</p>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $inActiveRoom?></p>
                </div>
            </div>

            <!-- Recent Room Listings -->
            <section class="recent-rooms">
                <h2 style="color:white;">Recent Room Listings</h2>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Room Title</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Room Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($rooms)){
                            foreach($rooms as $key => $value){
                                if($key <10){

                                    echo' <tr>                                   
                                    <td>'.++$key.'</td>
                                    <td>'.$value['room_name'].'</td>
                                    <td>'.$value['room_description'].'</td>
                                    <td>'.$value['room_location'].'</td>
                                    <td>RS '.$value['room_price'].'/-</td>
                                    <td>'.$value['room_type'].'</td>
                                    </tr>';
                                    }

                            }
                        }else{
                          echo'  <tr>
                            td"No DATA FOUND ."
                            </tr>';
                        }
                        ?>
                        
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
