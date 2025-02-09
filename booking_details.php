<?php
require('admin/dbConnect.php');
require('helperFunction/userAuth.php');
require('header.php');

// Fetch booking details
if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    $query = "SELECT r.room_name, r.room_image, r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id, u.user_name
              FROM bookings b
              JOIN users u ON b.user_id = u.user_id
              JOIN rooms r ON b.room_id = r.room_id
              WHERE b.booking_id = $bookingId AND b.is_active = 1 AND b.user_id = " . $_SESSION['auth_id'];

    $result = $conn->execute_query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Handle case where no booking is found
        echo '<div class="warning-message">Booking not found.</div>';
        exit();
    }
} else {
    echo '<div class="warning-message">Invalid booking ID.</div>';
    exit();
}
?>

<style>
    .booking-details-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .room-image-details {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
    }

    .room-details-content {
        padding: 20px;
    }

    .room-name-details {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .room-description-details {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #555;
    }

    .room-price-details {
        font-size: 1.4rem;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 15px;
    }

    .booking-status-details {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 5px;
        background-color: #28a745;
        color: white;
        display: inline-block;
        margin-bottom: 20px;
    }

    .booking-date-details {
        font-size: 1.2rem;
        color: #555;
        margin-bottom: 20px;
    }

    .cancel-button-details {
        padding: 10px 20px;
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        display: inline-block;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    .cancel-button-details:hover {
        background-color: #c82333;
    }
</style>

<div class="booking-details-container">
    <img src="admin/uploads/<?= $row['room_image'] ?>" alt="Room Image" class="room-image-details">
    <div class="room-details-content">
        <h2 class="room-name-details"><?= $row['room_name'] ?></h2>
        <p class="room-description-details"><?= nl2br($row['room_description']) ?></p>
        <p class="room-price-details">Price: $<?= number_format($row['room_price'], 2) ?></p>
        <p class="booking-status-details"><?= ucfirst($row['status']) ?></p>
        <p class="booking-date-details">Booked on: <?= $row['booking_date'] ?></p>
        <p class="booking-description-details"><?= nl2br($row['description']) ?></p>

        <!-- Cancel button if booking is pending -->
        <?php if ($row['status'] === 'pending') { ?>
            <form action="myBooking.php" method="POST">
                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                <button type="submit" name="cancelRequest" class="cancel-button-details">Cancel Booking</button>
            </form>
        <?php } ?>
    </div>
</div>

<?php require('helperFunction/SweetAlert.php'); ?>