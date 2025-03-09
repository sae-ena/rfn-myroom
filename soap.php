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

// If the IP is not found, insert it as inactive
if ($result->num_rows == 0) {

    echo"*************New Soap Client Request Handling****************";
    echo "<br>";
    echo"*************New Soap Client Request Handling****************";
    // Insert the IP address as inactive
    $username = $_SERVER['HTTP_USER_AGENT'];
    $sql_insert = "INSERT INTO ip_address_info (ip_address, status,username) VALUES (?, 'inactive',?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $user_ip,$username);
    
    if ($stmt_insert->execute()) {
        
    } else {
        echo "Error: " . $stmt_insert->error;
    }
    $stmt_insert->close();
}
$stmt->close();
$conn->close(); 
echo "<hr>";
echo "<hr>";
$fake_operations = [
    "Initializing security check...",
    "Scanning IP address for threats...",
    "Performing packet analysis...",
    "Verifying system integrity...",
    "Scanning for unauthorized access...",
    "Checking firewall status...",
    "Analyzing recent login attempts...",
    "Cross-checking access permissions...",
    "Ensuring secure protocol compliance...",
    "Verifying network vulnerabilities...",
    "Checking authentication methods...",
    "Scanning for open ports...",
    "Identifying suspicious activity...",
    "Performing packet sniffing analysis...",
    "Verifying encryption strength...",
    "Checking system configuration...",
    "Performing security audit...",
    "Verifying user credentials...",
    "Checking for outdated software...",
    "Scanning for malware...",
    "Performing access control validation...",
    "Scanning system logs for errors...",
    "Analyzing user access patterns...",
    "Performing brute force detection...",
    "Checking database security settings...",
    "Verifying data integrity...",
    "Scanning for malicious code...",
    "Evaluating system security policies...",
    "Checking for file integrity...",
    "Performing network analysis...",
    "Validating cryptographic algorithms...",
    "Scanning for SQL injection attempts...",
    "Analyzing authentication tokens...",
    "Verifying multi-factor authentication status...",
    "Checking DNS configurations...",
    "Scanning for rogue devices...",
    "Verifying system updates...",
    "Checking network traffic for anomalies...",
    "Analyzing user agent behavior...",
    "Performing intrusion detection system check...",
    "Scanning for system misconfigurations...",
    "Checking security patch levels...",
    "Scanning for cross-site scripting attacks...",
    "Verifying SSL/TLS connections...",
    "Checking session management...",
    "Monitoring user login attempts...",
    "Verifying vulnerability patching...",
    "Checking security certificate expiration...",
    "Scanning for backdoor access...",
    "Analyzing firewall rules...",
    "Checking for weak passwords...",
    "Verifying content security policies...",
    "Scanning for privilege escalation attempts...",
    "Ensuring secure file transfers...",
    "Analyzing system performance...",
    "Checking application security...",
    "Verifying compliance with security standards...",
    "Scanning for DNS poisoning attempts...",
    "Performing threat intelligence analysis...",
    "Validating system backup integrity...",
    "Checking server authentication methods...",
    "Analyzing cryptographic key strength...",
    "Verifying user activity logging...",
    "Checking for DNS hijacking attempts...",
    "Scanning for XSS vulnerabilities...",
    "Ensuring secure communication channels...",
    "Verifying network segmentation...",
    "Performing deep packet inspection...",
    "Validating compliance with security protocols...",
    "Checking access control lists...",
    "Scanning for zero-day vulnerabilities...",
    "Verifying software license compliance...",
    "Checking for compromised credentials...",
    "Scanning for privilege misconfigurations...",
    "Analyzing network routing for security flaws...",
    "Checking for unsecured APIs...",
    "Verifying data transmission encryption...",
    "Performing user permission review...",
    "Analyzing system boot configurations...",
    "Checking for rogue administrator accounts...",
    "Scanning for remote code execution risks...",
    "Verifying file access permissions...",
    "Scanning for broken authentication vulnerabilities...",
    "Checking for clickjacking risks...",
    "Ensuring secure cloud storage configurations...",
    "Verifying network access control settings...",
    "Checking for file system vulnerabilities...",
    "Analyzing system patch levels...",
    "Performing cryptographic hash validation...",
    "Checking for weak authentication tokens...",
    "Scanning for insecure login mechanisms...",
    "Verifying server hardening status...",
    "Monitoring for suspicious IP addresses...",
    "Scanning for session hijacking risks...",
    "Checking for data leakage risks...",
    "Analyzing access to sensitive files...",
    "Verifying compliance with encryption standards...",
    "Performing API security review...",
    "Scanning for unauthorized system changes...",
    "Verifying the security of third-party integrations..."
];

// Randomly display a fake operation from the list
$count = 1;
foreach ($_SERVER as $key => $value) {
    $random_message = $fake_operations[array_rand($fake_operations)];
    $count++;
    if($count > 6){
        echo $random_message;
    }
    echo ' => ' . $value . "----------------";
}

echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";


exit();
?>