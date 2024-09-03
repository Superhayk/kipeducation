<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    /* Sidebar styles */
    .sidebar {
        background-color: #343a40;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        padding: 20px;
        color: white;
        z-index: 1000;
        transition: transform 0.3s ease;
    }
    .sidebar .nav-link {
        color: #adb5bd;
        margin: 10px 0;
        border-radius: 5px;
        transition: background-color 0.2s ease;
    }
    .sidebar .nav-link:hover {
        background-color: #495057;
        color: white;
    }
    .sidebar .nav-link.active {
        background-color: #495057;
        color: white;
    }
    .sidebar .navbar-brand {
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: bold;
        color: white;
    }
    .sidebar .close-btn {
        display: none;
        font-size: 25px;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

    /* Desktop Sidebar width */
    @media (min-width: 769px) {
        .sidebar {
            width: 250px;
        }
        .content {
            margin-left: 250px;
        }
    }

    /* Mobile Sidebar width */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: 100%;
            right: 0;
            left: auto;
            transform: translateX(100%);
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar .close-btn {
            display: block;
        }
        .content {
            margin-left: 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #007bff;
            color: white;
        }
        .navbar img {
            height: 40px;
            flex: 0 0 auto;
        }
        .menu-icon {
            width: 30px;
            height: 30px;
            background-color: #343a40;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            cursor: pointer;
            margin-left: auto;
            flex: 0 0 auto;
        }
        .menu-icon::before {
            content: '≡';
            font-size: 20px;
            color: white;
        }
        .navbar .menu-icon-container {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
    }

    .content {
        padding: 20px;
        transition: margin-left 0.3s ease;
    }
    .content-header {
        margin-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }
</style>

<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="#" class="navbar-brand">Admin Panel</a>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo $current_page == 'admin.php' ? 'active' : ''; ?>" href="admin.php">Dashboard</a>
        <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php">Users and Admins</a>
        <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">Settings</a>
        <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" href="reports.php">Reports</a>
        <a class="nav-link <?php echo $current_page == 'courses.php' ? 'active' : ''; ?>" href="courses.php">Lessons</a>
        <a class="nav-link" href="logout.php">Logout</a>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar visibility
    var menuIcon = document.getElementById('menuIcon');
    if (menuIcon) {
        menuIcon.onclick = function() {
            document.getElementById('sidebar').classList.add('active');
        };
    }

    // Close the sidebar with the close button
    var closeSidebar = document.getElementById('closeSidebar');
    if (closeSidebar) {
        closeSidebar.onclick = function() {
            document.getElementById('sidebar').classList.remove('active');
        };
    }
});
</script>
