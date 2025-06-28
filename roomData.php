<?php
require_once(__DIR__ . '/helperFunction/RoomFetchForWebsite.php');
require_once(__DIR__ . '/helperFunction/InsertRoomData.php');


if (isset($_POST['room_id']) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $auth_id = $_SESSION['auth_id'];
    $room_id = $_POST['room_id'];
    $time = date("Y-m-d H:i:s");
    $remarks = $_POST['remarks'] ?? null;
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $customer_name = $_POST['payment_customer_name'] ?? ($_SESSION['auth_name'] ?? '');
    $customer_email = $_POST['payment_customer_email'] ?? ($_SESSION['auth_email'] ?? '');
    $customer_number = $_POST['payment_customer_number'] ?? ($_SESSION['auth_number'] ?? '');
    $room_price = $_POST['room_price'] ?? '';

    // If payment method is Khalti or eSewa, redirect to payment gateway
    if ($payment_method === 'khalti') {
        // Store booking intent in session for after payment
        $_SESSION['pending_booking'] = [
            'user_id' => $auth_id,
            'room_id' => $room_id,
            'remarks' => $remarks,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_number' => $customer_number,
            'room_price' => $room_price,
            'payment_method' => 'khalti',
        ];
        // Use POST redirect to Khalti payment
        echo '<form id="khaltiForm" method="POST" action="khalti.php" style="display:none;">'
            .'</form>';
        echo '<script>document.getElementById("khaltiForm").submit();</script>';
        exit();
    } elseif ($payment_method === 'esewa') {
        // Store booking intent in session for after payment
        $_SESSION['pending_booking'] = [
            'user_id' => $auth_id,
            'room_id' => $room_id,
            'remarks' => $remarks,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_number' => $customer_number,
            'room_price' => $room_price,
            'payment_method' => 'esewa',
        ];
        // Use POST redirect to eSewa payment
        echo '<form id="esewaForm" method="POST" action="esewa.php" style="display:none;">'
            .'</form>';
        echo '<script>document.getElementById("esewaForm").submit();</script>';
        exit();
    }
    // ...existing code for cash on hand booking...
    // Add payment_method, customer_name, customer_email, customer_number, room_price to booking insert/update
    $query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 1 ;";
    // Ensure RoomFetchForWebsite class is loaded and available
    $bookingResult = RoomFetchForWebsite::fetchBookingData($query);
    if ($bookingResult == "No Booking Found") {
        $check_query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 0 ";
        $existingBooking = RoomFetchForWebsite::fetchExistingData($check_query);
        if (is_array($existingBooking)) {
            if(isset($remarks) && is_string($remarks)){
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, description = '$remarks', payment_method = '$payment_method', customer_name = '$customer_name', customer_email = '$customer_email', customer_number = '$customer_number', room_price = '$room_price' WHERE user_id = '$auth_id' AND room_id = '$room_id'";
            }else{
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, payment_method = '$payment_method', customer_name = '$customer_name', customer_email = '$customer_email', customer_number = '$customer_number', room_price = '$room_price' WHERE user_id = '$auth_id' AND room_id = '$room_id'";
            }
        } else {
            $query = "INSERT INTO bookings (user_id, room_id, description, booking_date, payment_method, customer_name, customer_email, customer_number, room_price) VALUES ('$auth_id', '$room_id','$remarks', '$time', '$payment_method', '$customer_name', '$customer_email', '$customer_number', '$room_price')";
        }
        $bookingResult1 = InsertRoomData::insertData($query);
        // Debug: log the referer
        error_log('HTTP_REFERER: ' . ($_SERVER['HTTP_REFERER'] ?? 'NOT SET'));
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'booking_details.php?booking_id=') !== false) {
            echo '<!DOCTYPE html><html><head><script type="text/javascript">
            localStorage.setItem("showModalRoomAdded", "true");
            window.location.href = "' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES) . '";
            </script></head><body></body></html>';
            exit();
        }
        $successfullyRoomAdded = $bookingResult1;
        $auth_id = $room_id = $time = $remarks = "";
    } else {
        $form_error = $bookingResult['message'];
    }
}

if(isset($_SESSION['auth_id']) && $_SESSION['user_type'] == "user"){
    $userID = $_SESSION['auth_id'];
    $query = "SELECT r.room_id,r.room_status,r.room_location,  r.room_name, r.room_image, b.is_active,r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id ,b.user_id FROM  rooms r LEFT JOIN  bookings b ON b.room_id = r.room_id AND b.user_id = '$userID'  WHERE ( r.room_status ='active') ORDER BY r.created_at DESC  LIMIT 26;";
}else{
    $query = "SELECT room_id, room_status, room_name, room_location, room_image, room_description, room_price
              FROM rooms
              WHERE room_status = 'active'
              ORDER BY created_at DESC
              LIMIT 26;";
}
$rooms = RoomFetchForWebsite::fetchRoomData($query);

// Ensure $rooms is an array before using foreach
if (!is_array($rooms)) {
    $rooms = [];
}



if (isset($searchResult) && is_array($searchResult)) {
  ?>
  <div class="search-results-container">
    <h2 id="roomsTitleSearch">Search Results</h2>
    <div class="search-results-list">
      <?php foreach ($searchResult as $room) : ?>
        <div class="search-result-item">
          <div class="search-result-image">
            <a href="booking_details.php?booking_id=<?php echo htmlspecialchars($room['room_id']); ?>">
              <img src="admin/<?php echo htmlspecialchars($room["room_image"]); ?>" alt="Room image" />
            </a>
          </div>
          <div class="search-result-details">
            <div class="search-result-header">
              <h5 class="search-result-title"><a href="booking_details.php?booking_id=<?php echo htmlspecialchars($room['room_id']); ?>"><?php echo htmlspecialchars($room["room_name"]); ?></a></h5>
              <span class="search-result-price">RS <b><?php echo htmlspecialchars($room["room_price"]); ?></b>/Month</span>
            </div>
            <div class="search-result-location">
              <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($room["room_location"]); ?>
            </div>
            <div class="search-result-description">
              <p><?php echo htmlspecialchars(substr($room["room_description"], 0, 150)); ?>...</p>
            </div>
            <div class="search-result-actions">
              <a href="booking_details.php?booking_id=<?php echo htmlspecialchars($room['room_id']); ?>" class="btn">View Room</a>
              <?php if (isset($_SESSION['auth_id'])) : ?>
                <button class="btn book-now-btn" type="button" onclick="openPaymentModal('<?php echo $room['room_id']; ?>', '<?php echo $room['room_price']; ?>', '<?php echo htmlspecialchars($_SESSION['auth_name'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($_SESSION['auth_email'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($_SESSION['auth_number'] ?? '', ENT_QUOTES); ?>')">Book Now</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
}
?>

<div class="container rooms-container">
    
    
    <?php if (! empty($rooms)) : ?>
        <h2 id="roomsTitle">Explore Rooms</h2>
    <?php if (!isset($_SESSION['auth_id'])) {
        echo '<div class="login-warning">Please log in to book a room.</div>';
    } ?>
    <?php else : ?>
        <div class="gallery-grid">
            <?php foreach ($rooms as $room) : ?>
                <?php
                $currentRoomId = $room['room_id'];
                if (isset($previousRoomId) && $previousRoomId == $currentRoomId) {
                    continue;
                }
                ?>
                <div class="room-card">
                    <?php if (
                        isset($_SESSION['auth_id'], $room['user_id'], $room['status'], $room['is_active']) &&
                        ($_SESSION['auth_id'] == $room['user_id']) &&
                        $_SESSION['user_type'] == "user" &&
                        $room['status'] === "pending" &&
                        $room['is_active'] == true
                    ) : ?>
                        <div class="badge-status badge-booked"><strong>BOOKED</strong></div>
                    <?php endif; ?>
                    <?php if (
                        isset($_SESSION['auth_id'], $room['status'], $room['is_active']) &&
                        $_SESSION['user_type'] == "user" &&
                        $room['status'] === "canceled" &&
                        $room['is_active'] == true
                    ) : ?>
                        <div class="badge-status badge-rejected"><strong>REJECTED</strong></div>
                    <?php endif; ?>

                    <div class="image-container">
                        <img src="admin/<?php echo htmlspecialchars($room['room_image']); ?>" class="custom-room-image" alt="Room image" />
                    </div>
                    <div class="card-body">
                        <h5 class="custom-title-room-details"><?php echo htmlspecialchars($room["room_name"]); ?></h5>
                        <div class="card-text">
                            <p><?php echo htmlspecialchars(substr($room["room_description"], 0, 100)); ?>...</p>
                        </div>
                        <div class="room-price-location">
                            <span class="custom-price">RS <b><?php echo htmlspecialchars($room["room_price"]); ?></b>/Month</span>
                            <span class="custom-location-room-details"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($room["room_location"]); ?></b></span>
                        </div>
                        <div class="room-book-buttons-container">
                            <form action="booking_details.php" method="GET" style="flex:1;">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                                <button class="btn view-btn-12" type="submit">View Room</button>
                            </form>
                            <?php if (isset($_SESSION['auth_id']) && (!isset($room['is_active']) || $room['is_active'] == false)) : ?>
                                <div style="flex:1;">
                                    <button class="btn book-now-btn-12" type="button" onclick="openPaymentModal('<?php echo $room['room_id']; ?>', '<?php echo $room['room_price']; ?>', '<?php echo htmlspecialchars($_SESSION['auth_name'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($_SESSION['auth_email'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($_SESSION['auth_number'] ?? '', ENT_QUOTES); ?>')">Book Now</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="payment-modal-<?php echo $room['room_id']; ?>" class="payment-modal" style="display:none;">
                    <div class="payment-modal-content">
                        <span class="close-modal" onclick="closePaymentModal('<?php echo $room['room_id']; ?>')">&times;</span>
                        <h4>Select Payment Method</h4>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                            <input type="hidden" name="room_price" value="<?php echo $room['room_price']; ?>">
                            <input type="hidden" name="payment_customer_name" id="payment_customer_name_<?php echo $room['room_id']; ?>" value="<?php echo htmlspecialchars($_SESSION['auth_name'] ?? '', ENT_QUOTES); ?>">
                            <input type="hidden" name="payment_customer_email" id="payment_customer_email_<?php echo $room['room_id']; ?>" value="<?php echo htmlspecialchars($_SESSION['auth_email'] ?? '', ENT_QUOTES); ?>">
                            <input type="hidden" name="payment_customer_number" id="payment_customer_number_<?php echo $room['room_id']; ?>" value="<?php echo htmlspecialchars($_SESSION['auth_number'] ?? '', ENT_QUOTES); ?>">
                            <label><input type="radio" name="payment_method" value="cash" checked> Cash on Hand</label>
                            <label><input type="radio" name="payment_method" value="khalti"> Khalti</label>
                            <label><input type="radio" name="payment_method" value="esewa"> eSewa</label>
                            <div class="payment-modal-actions">
                                <button type="submit" class="btn book-now-btn-12">Proceed</button>
                                <button type="button" onclick="closePaymentModal('<?php echo $room['room_id']; ?>')" class="btn payment-cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php $previousRoomId = $room['room_id']; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function openPaymentModal(roomId, price, name, email, number) {
    document.getElementById('payment-modal-' + roomId).style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if(document.getElementById('payment_customer_name_' + roomId)) document.getElementById('payment_customer_name_' + roomId).value = name;
    if(document.getElementById('payment_customer_email_' + roomId)) document.getElementById('payment_customer_email_' + roomId).value = email;
    if(document.getElementById('payment_customer_number_' + roomId)) document.getElementById('payment_customer_number_' + roomId).value = number;
}
function closePaymentModal(roomId) {
    document.getElementById('payment-modal-' + roomId).style.display = 'none';
    document.body.style.overflow = '';
}
</script>
