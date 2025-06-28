<?php
require "admin/dbConnect.php";

$result = $conn->query("SELECT name, value FROM backend_settings WHERE status = 1");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Name: '" . $row['name'] . "', Value: '" . $row['value'] . "'\n";
    }
} else {
    echo "No settings found\n";
}

$conn->close();
?> 