<?php
session_start();
include 'db_connect.php';

// Check if the session is valid
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Get the total number of users
$userCountResult = $conn->query("SELECT COUNT(*) as total FROM students");
if ($userCountResult) {
    $userCount = $userCountResult->fetch_assoc()['total'];
} else {
    $userCount = 0; // Handle the error gracefully
}

// Get the total number of reports (assuming there's a table named `reports`)
$reportCountResult = $conn->query("SELECT COUNT(*) as total FROM reports");
if ($reportCountResult) {
    $reportCount = $reportCountResult->fetch_assoc()['total'];
} else {
    $reportCount = 0; // Handle the error gracefully
}

// Get recent activities
$recentActivitiesResult = $conn->query("SELECT * FROM activities ORDER BY activity_date DESC LIMIT 5");

$conn->close();

include 'sidebar.php'; // Include the sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 250px;
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
        .sidebar.closed {
            transform: translateX(-100%);
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
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .content-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .card {
            border-radius: 10px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
            font-size: 18px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            --bs-table-accent-bg: #e9ecef;
            color: #495057;
        }
        .navbar {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
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
    </style>
</head>
<body>
    <div class="content">
        <div class="navbar d-block d-md-none">
            <div class="menu-icon-container">
                <img src="resource/img/logo.png" alt="Logo">
                <div class="menu-icon" id="menuIcon"></div>
            </div>
        </div>
        <div class="content-header">
            <h1>Dashboard</h1>
            <p>Manage your dashboard</p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <h5 class="card-title">Total Users: <?php echo $userCount; ?></h5>
                        <a href="users.php" class="btn btn-custom">View Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Reports</div>
                    <div class="card-body">
                        <h5 class="card-title">New Reports: <?php echo $reportCount; ?></h5>
                        <a href="reports.php" class="btn btn-custom">View Reports</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Manage Settings</h5>
                        <a href="settings.php" class="btn btn-custom">Go to Settings</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card">
                <div class="card-header">Recent Activities</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">User</th>
                                <th scope="col">Activity</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentActivitiesResult && $recentActivitiesResult->num_rows > 0): ?>
                                <?php while ($activity = $recentActivitiesResult->fetch_assoc()): ?>
                                    <tr>
                                        <th scope="row"><?php echo $activity['id']; ?></th>
                                        <td><?php echo $activity['user']; ?></td>
                                        <td><?php echo $activity['activity']; ?></td>
                                        <td><?php echo $activity['activity_date']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No recent activities.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
