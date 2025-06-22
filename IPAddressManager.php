<?php
require_once('admin/dbConnect.php');

class IPAddressManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get real visitor IP address
     */
    public function getUserIP() {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
    
    /**
     * Register a new IP address as inactive
     */
    public function registerIP($ip_address, $username = null) {
        if ($username === null) {
            $username = $_SERVER['HTTP_USER_AGENT'];
        }
        
        $sql_insert = "INSERT INTO ip_address_info (ip_address, status, username) VALUES (?, 'inactive', ?)";
        $stmt_insert = $this->conn->prepare($sql_insert);
        $stmt_insert->bind_param("ss", $ip_address, $username);
        
        $result = $stmt_insert->execute();
        $stmt_insert->close();
        
        return $result;
    }
    
    /**
     * Update IP status to active
     */
    public function activateIP($ip_address) {
        $sql_update = "UPDATE ip_address_info SET status = 'active' WHERE ip_address = ?";
        $stmt_update = $this->conn->prepare($sql_update);
        $stmt_update->bind_param("s", $ip_address);
        
        $result = $stmt_update->execute();
        $stmt_update->close();
        
        return $result;
    }
    
    /**
     * Update IP status to inactive
     */
    public function deactivateIP($ip_address) {
        $sql_update = "UPDATE ip_address_info SET status = 'inactive' WHERE ip_address = ?";
        $stmt_update = $this->conn->prepare($sql_update);
        $stmt_update->bind_param("s", $ip_address);
        
        $result = $stmt_update->execute();
        $stmt_update->close();
        
        return $result;
    }
    
    /**
     * Check if IP exists in database
     */
    public function ipExists($ip_address) {
        $sql_check = "SELECT * FROM ip_address_info WHERE ip_address = ?";
        $stmt = $this->conn->prepare($sql_check);
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Get IP status
     */
    public function getIPStatus($ip_address) {
        $sql_get = "SELECT status FROM ip_address_info WHERE ip_address = ?";
        $stmt = $this->conn->prepare($sql_get);
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['status'];
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Get all IP addresses
     */
    public function getAllIPs() {
        $sql_get_all = "SELECT * FROM ip_address_info ORDER BY id DESC";
        $result = $this->conn->query($sql_get_all);
        
        $ips = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ips[] = $row;
            }
        }
        
        return $ips;
    }
    
    /**
     * Delete IP address
     */
    public function deleteIP($ip_address) {
        $sql_delete = "DELETE FROM ip_address_info WHERE ip_address = ?";
        $stmt_delete = $this->conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $ip_address);
        
        $result = $stmt_delete->execute();
        $stmt_delete->close();
        
        return $result;
    }
    
    /**
     * Activate the latest (most recently created) IP address
     */
    public function activateLatestIP() {
        $sql_get_latest = "SELECT ip_address FROM ip_address_info ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($sql_get_latest);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $latest_ip = $row['ip_address'];
            
            // Activate the latest IP
            return $this->activateIP($latest_ip);
        }
        
        return false;
    }
    
    /**
     * Get the latest IP address details
     */
    public function getLatestIP() {
        $sql_get_latest = "SELECT * FROM ip_address_info ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($sql_get_latest);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Handle new IP registration (main method)
     */
    public function handleNewIP() {
        $user_ip = $this->getUserIP();
        
        // Check if IP exists
        if (!$this->ipExists($user_ip)) {
            // Register new IP as inactive
            $this->registerIP($user_ip);
            return ['status' => 'registered', 'ip' => $user_ip];
        }
        
        return ['status' => 'exists', 'ip' => $user_ip];
    }
}
?> 