<?PHP
    require('admin/dbConnect.php');
if (isset($_SESSION['user_email']) || isset( $_SESSION['user_type']) ) {
    header("Location:index.php");
    exit(); // Ensure no further code is executed after the redirection
}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}


$user_ip = getUserIP();

// Check if the IP exists in the database
$sql_check_ip = "SELECT * FROM ip_address_info WHERE ip_address = ?";
$stmt = $conn->prepare($sql_check_ip);
$stmt->bind_param("s", $user_ip);
$stmt->execute();
$result = $stmt->get_result();
echo "<hr>";
echo "<hr>";
echo "<pre>";
foreach ($_SERVER as $key => $value) {
    echo $key . ' => ' . $value . "<br>";
}
echo "</pre>";

echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";

// If the IP is not found, insert it as inactive
if ($result->num_rows == 0) {

    echo"New Soap Client Request Handling";
    // Insert the IP address as inactive
    $username = $_SERVER['HTTP_USER_AGENT'];
    $sql_insert = "INSERT INTO ip_address_info (ip_address, status,username) VALUES (?, 'inactive',?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $user_ip,$username);
    
    if ($stmt_insert->execute()) {
        
    } else {
        echo "Error: " . $stmt_insert->error;
    }
}
$stmt->close();
$stmt_insert->close();
$conn->close(); 
exit();
?>