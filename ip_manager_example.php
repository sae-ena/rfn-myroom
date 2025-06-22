<?php
require_once('IPAddressManager.php');

// Initialize the IP Address Manager
$ipManager = new IPAddressManager($conn);

// Example 1: Handle new IP registration (replaces your current soap.php logic)
$result = $ipManager->handleNewIP();
if ($result['status'] === 'registered') {
    echo "New IP registered: " . $result['ip'];
} else {
    echo "IP already exists: " . $result['ip'];
}

// Example 2: Activate an IP address
$user_ip = $ipManager->getUserIP();
if ($ipManager->activateIP($user_ip)) {
    echo "IP activated successfully: " . $user_ip;
} else {
    echo "Failed to activate IP: " . $user_ip;
}

// Example 3: Check IP status
$status = $ipManager->getIPStatus($user_ip);
echo "Current status: " . ($status ? $status : 'Not found');

// Example 4: Get all IP addresses
$all_ips = $ipManager->getAllIPs();
echo "Total IPs in database: " . count($all_ips);

// Example 5: Deactivate an IP
// $ipManager->deactivateIP($user_ip);

// Example 6: Delete an IP
// $ipManager->deleteIP($user_ip);

// Example 7: Activate the latest (most recently created) IP
if ($ipManager->activateLatestIP()) {
    echo "Latest IP activated successfully!";
} else {
    echo "No IP found to activate or activation failed.";
}

// Example 8: Get details of the latest IP
$latest_ip = $ipManager->getLatestIP();
if ($latest_ip) {
    echo "Latest IP: " . $latest_ip['ip_address'];
    echo "Status: " . $latest_ip['status'];
    echo "Username: " . $latest_ip['username'];
} else {
    echo "No IP addresses found in database.";
}
?> 