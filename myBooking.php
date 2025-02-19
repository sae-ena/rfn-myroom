<?php
require('helperFunction/InsertRoomData.php');
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['cancelRequest'])) {
    $bookingId = $_POST['booking_id'];
    $query = "UPDATE bookings SET is_active = 0 WHERE booking_id = $bookingId;";
    //isActive 0 means user have caceled his self booking
    $bookingResult1 = InsertRoomData::insertData($query);
    if (strpos($_SERVER['HTTP_REFERER'], "http://localhost:8000/booking_details.php?") !== false) {
        $response = [
            'status' => 'success',
            'message' => 'Successfully Canceled Booking',
            'booking_id' => $bookingId,
        ];
        echo '<script type="text/javascript">
        // Set localStorage to show modal
        localStorage.setItem("showModalRoomCanceled", "true");
        // Redirect back to the referring page
        window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
      </script>';
exit();// Stop further execution
    }
    $successfullyRoomAdded = "Succesfully Canceled Booking";
}
require('helperFunction/userAuth.php');
require('header.php');
?>
<style>
    /* Style the status filter form */
    .status-filter-form {
        margin-bottom: 20px;
        padding: 5px;
        background-color:rgb(255, 141, 2);
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .status-filter-form label {
        font-size: 1rem;
        margin-right: 10px;
        
    }

    .status-filter-form select {
        padding: 8px;
        font-size: 1rem;
        border-radius: 5px;
        border: 1px solid #ddd;
        background-color: whitesmoke;
        color: black;
    }

    .status-filter-form .filter-button {
        padding: 8px 16px;
        font-size: 1rem;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .status-filter-form .filter-button:hover {
        background-color: #0056b3;
    }

    /* Container for the entire booking list */
    .bookings-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        /* Flexible grid */
        gap: 20px;
        padding: 20px;
        margin: 0 auto;
    }

    .booking-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .booking-card:hover {
        transform: translateY(-10px);
        /* Subtle hover effect */
    }

    .room-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-content {
        padding: 15px;
    }

    .room-name {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .room-description {
        height: 150px;
        /* Fixed height */
        overflow: hidden;
        /* Hide overflow */
        text-overflow: ellipsis;
        /* Optional: Add ellipsis if text overflows */
        display: -webkit-box;
        -webkit-line-clamp: 4;
        /* Limit to 4 lines */
        -webkit-box-orient: vertical;
        margin: 6px 0px 9px 0px;
    }


    .booking-date,
    .room-price {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status.confirmed {
        background-color: #28a745;
        color: white;
    }

    .status.pending {
        background-color: #ffc107;
        color: white;
    }

    .status.canceled {
        background-color: #dc3545;
        color: white;
    }

    .view-details {
        display: inline-block;
        padding: 8px 16px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .view-details:hover {
        background-color: #0056b3;
    }

    .parentBtn {
        display: flex;
        justify-content: space-between;
        /* This makes sure space between left and right is maximized */
        align-items: center;
        /* Vertically centers the items */
    }

    /* Style for the cancel button */
    .cancel-button {
        display: inline-block;
        padding: 8px 16px;
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .cancel-button:hover {
        background-color: #c82333;
    }

    .warning-message {
        position: fixed;
        /* Fixed position relative to the viewport */
        top: 50%;
        /* Center vertically */
        left: 50%;
        /* Center horizontally */
        transform: translate(-50%, -50%);
        /* Offset the element by 50% of its width and height */
        padding: 20px;
        background-color: rgb(241, 238, 11);
        /* Light red background for warning */
        color: #721c24;
        /* Dark red color for text */
        border: 1px solid #f5c6cb;
        /* Border to match the warning style */
        border-radius: 5px;
        /* Rounded corners */
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
        z-index: 1000;
        /* Make sure the message is on top of other content */
    }
</style>
<?php


if (isset($_GET['status']) && ($_GET['status'] === "pending" || $_GET['status'] === "confirmed" || $_GET['status'] === "canceled")) {
    $bookingStatus = $_GET['status'];
    $query = "SELECT r.room_id,r.room_status,  r.room_name, r.room_image, b.is_active,r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN rooms r ON b.room_id = r.room_id
        WHERE b.user_id = " . $_SESSION['auth_id'] . " AND b.status = '$bookingStatus' AND b.is_active = 1 ;";

} else {

    $query = "SELECT r.room_id , r.room_status,r.room_name, r.room_image,b.is_active, r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN rooms r ON b.room_id = r.room_id
                WHERE b.user_id = " . $_SESSION['auth_id'] . " AND b.is_active = 1;";
}

$result = $conn->execute_query($query);
?><form action="myBooking.php" method="GET" class="status-filter-form" style=" align-items: flex-start; gap: 15px; padding: 10px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); max-width: 300px; width: 100%; margin: 0 auto;">
<label for="status-select" style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px;">Filter by Status:</label>
<select name="status" id="status-select" onchange="this.form.submit()" style="padding: 10px; font-size: 14px; border-radius: 8px; border: 1px solid #ddd; transition: all 0.3s ease;">
    <option value="" style="color: #777;">All</option>
    <option value="confirmed" <?php if (isset($_GET['status']) && $_GET['status'] == 'confirmed') echo 'selected'; ?> style="">Confirmed</option>
    <option value="pending" <?php if (isset($_GET['status']) && $_GET['status'] == 'pending') echo 'selected'; ?> style="">Pending</option>
    <option value="canceled" <?php if (isset($_GET['status']) && $_GET['status'] == 'canceled') echo 'selected'; ?> style="">Canceled</option>
</select>
</form>

<div class="bookings-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="booking-card">
                <?php
                echo '<img src="admin/' . $row["room_image"] . '" alt="Room Image" class="room-image">';
                ?>
                <div class="card-content">
                    <h3 class="room-name"><?= $row['room_name'] ?></h3>
                    <div class="room-description">
                        <?= (strlen($row['room_description']) > 220) ? substr($row['room_description'], 0, 220) . '...' : $row['room_description'] ?>
                    </div>
                    <p class="booking-date">Booked on: <?= $row['booking_date'] ?></p>
                    <p class="room-price">Price: Rs <?= $row['room_price'] ?></p>
                    <p class="status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></p>

                    <div class="parentBtn">

                    <?php if ($row['room_status'] !== 'inActive' || $row['status'] !== "canceled" ) { ?>
                        <a href="booking_details.php?booking_id=<?= $row['room_id'] ?>" class="view-details">View
                            Details</a>
                            <?php } ?>
                        <div class="right-content">
                            <!-- Cancel button, only visible if booking is not yet canceled -->
                            <?php if ($row['status'] === 'pending') { ?>
                                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                                    <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                                    <button type="submit" name="cancelRequest" class="cancel-button">Cancel Booking</button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <?php
        }
    } else {
        if(isset($_GET['status']) && $_GET['status'] == "pending"){
     echo '<div class="warning-message" style="background-color: #FFEB3B; color: #333; padding: 15px 25px; border-radius: 8px; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 500px; margin: 20px auto; border: 1px solid #FBC02D; text-align: center;">
    <span style="margin-right: 10px; font-size: 20px; color: #F57C00;">⚠️</span>
      You have not booked a room yet. Please make a reservation to proceed.
</div>';
        }else{
            echo '<div class="warning-message" style="background-color:rgb(255, 230, 0); color: #333; padding: 15px 25px; border-radius: 8px; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 500px; margin: 20px auto; border: 1px solid #FBC02D; text-align: center;">
            <span style="margin-right: 10px; font-size: 20px; color: #F57C00;">⚠️</span>
             Currently, there are no rooms available. Please check back later.
        </div>';
        }

    }

    require('helperFunction/SweetAlert.php');
    ?>
</div>