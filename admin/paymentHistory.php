<?php
require_once "leftSidebar.php";
require "dbConnect.php";
// Payment & Booking History for Admin
// Build filter query
$where = [];
if (!empty($_GET['payment_method'])) {
    $method = $conn->real_escape_string($_GET['payment_method']);
    $where[] = "payment_method = '$method'";
}
if (!empty($_GET['payment_status'])) {
    $status = $conn->real_escape_string($_GET['payment_status']);
    $where[] = "payment_status = '$status'";
}
$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
$query = "SELECT b.booking_id, b.user_id, u.user_name, u.user_email, u.user_number, b.room_id, r.room_name, r.room_location, b.booking_date, b.payment_method, b.payment_status, b.payment_txn_id, b.room_price FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN rooms r ON b.room_id = r.room_id $whereSql ORDER BY b.booking_date DESC LIMIT 100";
$result = $conn->query($query);
?>
<div class="dashboard-content">
    <header class="dashboard-header">
        <h1 class="for-heading">Payment & Booking History</h1>
    </header>
    <div class="container" style="display: flex; flex-direction: column; align-items: center;">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <label>Payment Method:
                    <select name="payment_method">
                        <option value="">All</option>
                        <option value="cash" <?php if(isset($_GET['payment_method']) && $_GET['payment_method']==='cash') echo 'selected'; ?>>Cash on Hand</option>
                        <option value="khalti" <?php if(isset($_GET['payment_method']) && $_GET['payment_method']==='khalti') echo 'selected'; ?>>Khalti</option>
                        <option value="esewa" <?php if(isset($_GET['payment_method']) && $_GET['payment_method']==='esewa') echo 'selected'; ?>>eSewa</option>
                    </select>
                </label>
                <label>Status:
                    <select name="payment_status">
                        <option value="">All</option>
                        <option value="paid" <?php if(isset($_GET['payment_status']) && $_GET['payment_status']==='paid') echo 'selected'; ?>>Paid</option>
                        <option value="pending" <?php if(isset($_GET['payment_status']) && $_GET['payment_status']==='pending') echo 'selected'; ?>>Pending</option>
                        <option value="failed" <?php if(isset($_GET['payment_status']) && $_GET['payment_status']==='failed') echo 'selected'; ?>>Failed</option>
                    </select>
                </label>
            </div>
            <button type="submit">Filter</button>
        </form>
        <style>
        .filter-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }
        .filter-row {
            display: flex;
            flex-direction: row;
            gap: 18px;
            align-items: flex-end;
            justify-content: center;
        }
        .filter-form label {
            display: flex;
            flex-direction: column;
            font-weight: 500;
            min-width: 160px;
        }
        .filter-form select {
            padding: 7px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 4px;
            min-width: 140px;
        }
        .filter-form button[type="submit"] {
            padding: 9px 24px;
            background: #3737bc;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            min-width: 110px;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .filter-form label, .filter-form button[type="submit"] {
                min-width: 0;
                width: 100%;
            }
        }
        </style>
        <table class="room-table" style="width:100%;margin-top:20px;">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Txn/Ref ID</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_location']); ?></td>
                    <td>Rs <?php echo number_format($row['room_price']); ?></td>
                    <td><?php echo ucfirst($row['payment_method']); ?></td>
                    <td style="color:<?php echo $row['payment_status']==='paid'?'green':'#c82333'; ?>;font-weight:bold;">
                        <?php echo ucfirst($row['payment_status']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['payment_txn_id']); ?>
                       
                    </td>
                    <td><?php echo $row['booking_date']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<style>
.verify-btn {
    background: #5C2D91;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 4px 12px;
    font-size: 0.98rem;
    font-weight: 500;
    cursor: pointer;
    margin-left: 2px;
    transition: background 0.2s;
}
.verify-btn:hover {
    background: #47207a;
}
</style>
