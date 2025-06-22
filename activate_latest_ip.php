<?php
// Command line script to activate the latest IP address

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line\n");
}

// Include required files
require_once('admin/dbConnect.php');
require_once('IPAddressManager.php');

echo "=== IP Address Manager - Command Line ===\n\n";

try {
    // Initialize the IP Address Manager
    $ipManager = new IPAddressManager($conn);
    
    echo "Checking for latest IP address...\n";
    
    // Get the latest IP details first
    $latest_ip = $ipManager->getLatestIP();
    
    if ($latest_ip) {
        echo "Latest IP found:\n";
        echo "- IP Address: " . $latest_ip['ip_address'] . "\n";
        echo "- Current Status: " . $latest_ip['status'] . "\n";
        echo "- Username: " . $latest_ip['username'] . "\n";
        echo "- ID: " . $latest_ip['id'] . "\n";
        
        // Check if it's already active
        if ($latest_ip['status'] === 'active') {
            echo "\n✅ IP is already active!\n";
        } else {
            echo "\nActivating latest IP...\n";
            
            // Activate the latest IP
            if ($ipManager->activateLatestIP()) {
                echo "✅ Latest IP activated successfully!\n";
                
                // Verify the activation
                $updated_status = $ipManager->getIPStatus($latest_ip['ip_address']);
                echo "✅ New status: " . $updated_status . "\n";
            } else {
                echo "❌ Failed to activate IP!\n";
            }
        }
    } else {
        echo "❌ No IP addresses found in database!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Script completed ===\n";
?> 