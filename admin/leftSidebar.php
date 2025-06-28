<?php

require_once "../helperFunction/CheckLogin.php";
CheckLogin::islogin();

if(stripos($_SERVER['SCRIPT_NAME'], "/dynaform") !== false){
    echo'<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="../admin/style.css">
        <link rel="stylesheet" href="../admin/dashboard.css">
        <link rel="stylesheet" href="../admin/form.css">
    </head>';
}
else{
    echo'<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="form.css">
    </head>';
}

echo'<body>
    <div class="dashboard">
        <!-- Sidebar Toggle Button - Robust Hamburger -->
        <div class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>
        
        <!-- Sidebar/Navbar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Admin Dashboard</h2>
                <button class="sidebar-close-btn" id="sidebarCloseBtn" title="Close Sidebar">&times;</button>
            </div>
            <ul>
                <li><a href="/admin/dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="/admin/roomTable.php" class="nav-link">Rooms</a></li>
                <li><a href="/admin/booking.php" class="nav-link" data-target="bookings">Bookings</a></li>
                <li><a href="/admin/userTable.php" class="nav-link" data-target="users">Users</a></li>
                <li><a href="/admin/approve.php" class="nav-link" data-target="settings">Approve List</a></li>
                <li><a href="paymentHistory.php" class="nav-link" data-target="paymentHistory">Payment & Booking History</a></li>
                <li><a href="/admin/media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                <li><button id="mainSettingsToggle">Main Settings &#9660;</button></li>
                <ul id="mainSettingsSubmenu" style="display:none;list-style:none;padding-left:20px;margin:0;">
                <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media">Form  Manager</a></li>
                    <li><a href="/admin/emailTemplate.php" class="nav-link" data-target="email-template">Email Template</a></li>
                    <li><a href="/admin/backendSetting.php" class="nav-link" data-target="backend-setting">Backend Setting</a></li>
                </ul>
                <li><a href="/admin/dbConnect.php" class="nav-link" id="logoutBtn" >Logout</a></li>
            </ul>
        </nav>
        </div>';

?>
<script>
let btn = document.getElementById("logoutBtn");
btn.onclick = function(event) {
    
    // <li><a href="/admin/media.php" class="nav-link" data-target="media" disable>Media  Manager</a></li>
                // <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media" disabled>Form  Manager</a></li>
    // <li><a href="media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                // <li><a href="/admin/media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                // <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media">Form  Manager</a></li>
            // Prevent the default action of the link (redirection)
            event.preventDefault();
            
            // Show the confirmation dialog
            let confirmLogout = confirm("Are you sure you want to logout?");
            
            // If the user clicks "OK", redirect to the logout page (e.g., dbConnect.php or a logout script)
            if (confirmLogout) {
                window.location.href = "logout.php"; // or your logout script
            }
        };

// Toggle Main Settings
const mainSettingsToggle = document.getElementById('mainSettingsToggle');
const mainSettingsSubmenu = document.getElementById('mainSettingsSubmenu');
mainSettingsToggle.addEventListener('click', function() {
    if (mainSettingsSubmenu.style.display === 'none') {
        mainSettingsSubmenu.style.display = 'block';
        mainSettingsSubmenu.style.transition = 'all 0.3s ease';
        mainSettingsToggle.innerHTML = 'Main Settings &#9650;';
    } else {
        mainSettingsSubmenu.style.display = 'none';
        mainSettingsToggle.innerHTML = 'Main Settings &#9660;';
    }
});

// Sidebar Toggle Functionality - Updated for ALL screen sizes
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

// Check if sidebar state is stored in localStorage
const sidebarState = localStorage.getItem('sidebarCollapsed');
if (sidebarState === 'true') {
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
} else if (sidebarState === null) {
    // Default state - sidebar open
    localStorage.setItem('sidebarCollapsed', 'false');
}

sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
    document.body.classList.toggle('sidebar-collapsed');
    
    // Store sidebar state in localStorage
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
});

// Sidebar close button functionality (big screens only)
sidebarCloseBtn.addEventListener('click', function() {
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', 'true');
});

// Close sidebar when clicking outside on mobile only
document.addEventListener('click', function(event) {
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        // On desktop, don't auto-close sidebar, just maintain current state
        // Remove mobile-specific classes
        document.body.classList.remove('sidebar-open');
    }
});

// Sidebar close on menu click (mobile only)
const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
sidebarLinks.forEach(link => {
  link.addEventListener('click', function() {
    if (window.innerWidth <= 768) {
      sidebar.classList.add('collapsed');
      document.body.classList.add('sidebar-collapsed');
      localStorage.setItem('sidebarCollapsed', 'true');
    }
  });
});
</script>

<style>
/* Sidebar Toggle Button - Robust Hamburger */
.sidebar-toggle {
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 2000;
  width: 40px;
  height: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  box-shadow: none;
}
.sidebar-toggle .bar {
  width: 28px;
  height: 4px;
  background: #fff;
  margin: 3px 0;
  border-radius: 2px;
  transition: background 0.3s;
}
/* Hamburger only visible when sidebar is closed, on ALL screen sizes */
body:not(.sidebar-collapsed) .sidebar-toggle {
  display: none !important;
}
body.sidebar-collapsed .sidebar-toggle {
  display: flex !important;
}

/* Sidebar Header */
.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    position: relative;
}

/* Sidebar Close Button - Only visible on big screens */
.sidebar-close-btn {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 5px;
    position: absolute;
    top: -5px;
    right: -5px;
    transition: all 0.3s ease;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.sidebar-close-btn:hover {
    color: #ff6600;
    transform: scale(1.1);
    background-color: rgba(255, 255, 255, 0.1);
}

/* Show close button only on big screens when sidebar is open */
@media (min-width: 769px) {
    body:not(.sidebar-collapsed) .sidebar-close-btn {
        display: block;
    }
}

/* Responsive Sidebar - Updated for all screen sizes */
.sidebar {
    height: 100vh;
    overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE 10+ */
    transition: transform 0.3s ease;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    width: 250px;
}
.sidebar::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* Chrome/Safari/Webkit */
}

/* Sidebar collapsed state */
.sidebar.collapsed {
    transform: translateX(-100%);
}

/* Dashboard content adjustment for all screen sizes */
.dashboard-content {
    margin-left: 250px;
    padding: 40px;
    padding-top: 80px;
    transition: margin-left 0.3s ease;
}

/* When sidebar is collapsed, adjust content */
body.sidebar-collapsed .dashboard-content {
    margin-left: 0;
    padding-left: 80px;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .sidebar {
        width: 280px;
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    .dashboard-content {
        margin-left: 0 !important;
        padding: 20px !important;
        padding-top: 80px !important;
    }
    
    body.sidebar-collapsed .dashboard-content {
        padding-left: 20px !important;
    }
    
    .room-overview {
        flex-direction: column;
        gap: 15px;
    }
    
    .card {
        padding: 20px;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .dashboard-header h1 {
        font-size: 24px;
    }
    
    .add-room-button {
        font-size: 16px;
        padding: 8px 16px;
    }
    
    .room-table {
        font-size: 12px;
    }
    
    .room-table th, 
    .room-table td {
        padding: 8px 4px;
    }
}

@media (max-width: 480px) {
    .sidebar {
        width: 100%;
    }
    
    .dashboard-content {
        padding: 15px !important;
        padding-top: 70px !important;
    }
    
    body.sidebar-collapsed .dashboard-content {
        padding-left: 15px !important;
    }
    
    .room-table {
        font-size: 11px;
    }
    
    .room-table th, 
    .room-table td {
        padding: 6px 2px;
    }
    
    .card {
        padding: 15px;
    }
    
    .card h3 {
        font-size: 16px;
    }
    
    .card p {
        font-size: 20px;
    }
}

/* Overlay for mobile only */
@media (max-width: 768px) {
    body.sidebar-open::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
}

/* Smooth transitions */
.sidebar, .dashboard-content {
    transition: all 0.3s ease;
}

.sidebar-toggle:hover {
    background: #e65c00;
}
</style>
