<?php
require('admin/dbConnect.php');
require('header.php');

// Fetch booking details
if (isset($_GET['booking_id']) && isset($_SESSION['auth_id'])) {
    $bookingId = $_GET['booking_id'];

    $query = "SELECT r.room_name, r.room_id, r.room_image, r.room_description, r.room_price, b.booking_date, b.status, r.room_location, b.description, b.booking_id, b.is_active
              FROM rooms r
              LEFT JOIN bookings b ON b.room_id = r.room_id AND b.user_id = '".$_SESSION['auth_id']."'
              WHERE (r.room_id = $bookingId AND (r.room_status = 'active' OR b.status = 'confirmed'));";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Handle case where no booking is found
        echo '<div class="warning-message">Currently, there are no room available. Please check back later.</div>';
        exit();
    }
}
elseif(isset($_GET['booking_id'])){
    $bookingId = $_GET['booking_id'];
    $query =  "SELECT r.room_name, r.room_image, r.room_description, r.room_price, b.description, b.booking_id, r.room_location, b.is_active
               FROM rooms r
               LEFT JOIN bookings b ON r.room_id = b.room_id
               WHERE r.room_status = 'active' AND r.room_id = '$bookingId';";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Handle case where no booking is found
        echo '<div class="warning-message">Currently, there are no room available. Please check back later.</div>';
        exit();
    }
}
else {
    echo '<div class="warning-message">Invalid booking ID.</div>';
    exit();
}
?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .booking-details-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .booking-details-container:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .room-image-details {
        width: 100%;
        height: 350px;
        object-fit: cover;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .room-image-details:hover {
        transform: scale(1.05);
    }

    .room-details-content {
        padding: 20px 0;
    }

    .room-name-details {
        font-size: 2.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        transition: color 0.3s ease;
    }

    .room-name-details:hover {
        color: #007bff;
    }

    .room-description-details {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #555;
        line-height: 1.6;
    }

    .room-price-details {
        font-size: 1.6rem;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 15px;
        transition: color 0.3s ease;
    }

    .room-price-details:hover {
        color: #007bff;
    }

    .booking-status-details {
        font-weight: bold;
        padding: 12px 20px;
        border-radius: 5px;
        background-color: #28a745;
        color: white;
        display: inline-block;
        margin-bottom: 20px;
    }

    .cancel-button-details {
        padding: 12px 25px;
        background-color: #dc3545;
        font-size: 1.2rem;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        display: inline-block;
        margin-top: 20px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        width: 27%;
    }

    .cancel-button-details:hover {
        background-color: #c82333;
        transform: translateY(-5px);
    }

    .notice-message {
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 8px;
        font-size: 1rem;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-out;
    }

    .warning-message {
        font-size: 1.2rem;
        color: #c82333;
        background-color: #f8d7da;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        max-width: 500px;
        text-align: center;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    #successModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        transition: opacity 0.3s ease;
    }

    #successModal .modal-content {
        position: relative;
        margin: 10% auto;
        background-color: #fff;
        padding: 30px;
        width: 350px;
        border-radius: 8px;
        text-align: center;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        0% {
            transform: translateY(-30px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .success-message {
        font-size: 1.5rem;
        color: #28a745;
    }

    .error-message {
        font-size: 1.5rem;
        color: #dc3545;
    }
</style>

<div class="booking-details-container">
    <?php if (!isset($_SESSION['auth_id'])) {
        echo '<div class="notice-message">Login to book room.</div>';
    } ?>
    <img src="admin/<?= $row['room_image'] ?>" alt="Room Image" class="room-image-details">
    <div class="room-details-content">
        <h2 class="room-name-details"><?= $row['room_name'] ?></h2><p class="room-description-details" style="font-size: 1rem; margin-bottom: 20px; color: #12555; line-height: 1.6;">
    <strong style="font-size: 1.1rem; color: #333;">Description: </strong><?= nl2br($row['room_description']) ?>
</p>

<p class="room-location-details" style="font-size: 1rem; margin-bottom: 20px; color: #555; line-height: 1.6;">
    <strong style="font-size: 1.1rem; color: #333;">Location: </strong><?= nl2br($row['room_location']) ?>
</p>

        <p class="room-price-details">Price: Rs <?= number_format($row['room_price'], 2) ?></p>

        <?php if (isset($row['user_name'])) { ?>
            <p class="user-name-details">Booked by: <?= htmlspecialchars($row['user_name']) ?></p>
        <?php } ?>

        <?php if ((isset($row['status']) && $row['status']) && isset($row['booking_date']) && $row['is_active'] == true) { ?>
            <p class="booking-date-details">Booked on: <?= $row['booking_date'] ?></p>
            <strong>Status:</strong><p class="booking-status-details" style="
    background-color: <?= $row['status'] == 'canceled' ? '#dc3545' : '#28a745' ?>;
    color: white;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    display: inline-block;
    margin-bottom: 20px;">
    <?= ucfirst($row['status']) ?>
</p>


            <?php if ($row['status'] === 'pending' && $row['is_active'] == true) { ?>
                <form action="myBooking.php" method="POST">
                    <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                    <button type="submit" name="cancelRequest" class="cancel-button-details">Cancel Booking</button>
                </form>
            <?php } ?>
        <?php } ?>

        <?php if (isset($_SESSION['auth_id']) && ($row['is_active'] == false || !isset($row['status']))) { ?>
            <form action="roomData.php" method="POST">
                <strong>Remarks :</strong><textarea name="remarks" placeholder="Enter your remarks here..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px; font-size: 1rem; resize: vertical;"></textarea>
                <button type="submit" name="bookNow" class="cancel-button-details" style="background-color:rgb(0, 255, 42);">Book Now</button>
                <input type="hidden" name="room_id" value="<?= $row['room_id'] ?>"> 
            </form>
        <?php } ?>
    </div>
</div>

<div id="successModal">
    <div class="modal-content">
        <h3 class="success-message">Success</h3>
        <hr>
        <h4 id="successMessage"></h4>
    </div>
</div>

<script>
    window.onload = function() {
        if (localStorage.getItem("showModalRoomAdded") === "true") {
            document.getElementById("successModal").style.display = "block";
            document.getElementById("successMessage").innerHTML = "Room successfully Booked.";
            localStorage.removeItem("showModalRoomAdded");

            setTimeout(() => {
                document.getElementById("successModal").style.display = "none";
            }, 2000);
        }

        if (localStorage.getItem("showModalRoomCanceled") === "true") {
            document.getElementById("successModal").style.display = "block";
            document.getElementById("successMessage").innerHTML = "Booking Canceled.";
            document.getElementById("successMessage").classList.add('error-message');
            localStorage.removeItem("showModalRoomCanceled");

            setTimeout(() => {
                document.getElementById("successModal").style.display = "none";
            }, 2000);
        }
    };
</script>

<?php require('helperFunction/SweetAlert.php'); ?>
