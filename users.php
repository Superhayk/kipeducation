<?php
session_start();
include 'db_connect.php';

// Սեսիայի ստուգում
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Ստանալու օգտագործողների և ադմինների ցանկը
$usersResult = $conn->query("SELECT * FROM students");
$adminsResult = $conn->query("SELECT * FROM admins");

$conn->close();

// Հաշվարկի ընթացիկ էջի անվանումը
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users and Admins</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .add-user-btn {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        /* Popup styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            text-align: left;
            transform: scale(0);
            transition: transform 0.3s ease;
            position: relative;
        }
        .modal-content h3 {
            margin: 0;
            margin-bottom: 15px;
            font-size: 24px;
            color: #333;
        }
        .modal-content input[type="text"],
        .modal-content input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .modal-content button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .modal-content button:hover {
            background-color: #0056b3;
        }
        .close-modal {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

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

    <div class="content">
        <div class="navbar d-block d-md-none">
            <div class="menu-icon-container">
                <img src="resource/img/logo.png" alt="Logo">
                <div class="menu-icon" id="menuIcon"></div>
            </div>
        </div>
        <div class="content-header">
            <h1>Users and Admins</h1>
            <p>Manage all users and admins in the system here.</p>
        </div>

        <!-- Users Table -->
        <div class="card mt-4">
            <div class="card-header">Users List</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['registration_date']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admins Table -->
        <div class="card mt-4">
            <div class="card-header">Admins List</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($admin = $adminsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $admin['id']; ?></td>
                            <td><?php echo $admin['email']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add User/Admin Button -->
        <button class="btn btn-custom add-user-btn" id="addUserBtn">Add User or Admin</button>

        <!-- Add User/Admin Modal -->
        <div id="addUserModal" class="modal">
            <div class="modal-content">
                <span class="close-modal" id="closeModal">&times;</span>
                <h3>Add User or Admin</h3>
                <form id="addUserForm" method="POST" action="add_user_admin.php">
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit">Add</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menuIcon').onclick = function() {
            document.getElementById('sidebar').classList.add('active');
        };

        document.getElementById('closeSidebar').onclick = function() {
            document.getElementById('sidebar').classList.remove('active');
        };

        // Modal functionality
        var modal = document.getElementById('addUserModal');
        var btn = document.getElementById('addUserBtn');
        var closeBtn = document.getElementById('closeModal');

        btn.onclick = function() {
            modal.style.display = "flex";
            setTimeout(function() {
                modal.classList.add('show');
            }, 10);
        }

        closeBtn.onclick = function() {
            modal.classList.remove('show');
            setTimeout(function() {
                modal.style.display = "none";
            }, 300);
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('show');
                setTimeout(function() {
                    modal.style.display = "none";
                }, 300);
            }
        }
    </script>
</body>
</html>
