<?php
session_start(); // Start the session

// Check if the user is logging out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

// Check if the session is valid
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['student_id'])) { // Changed 'user_id' to 'student_id'
    echo "No student ID found in session."; // Changed message accordingly
    exit();
}

$student_id = $_SESSION['student_id']; // Changed variable name
$sql = "SELECT * FROM students WHERE id = '$student_id'"; // Changed variable name in query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f0f2f5;
        }
        .left-section {
            width: 20%;
            background-color: #2c3e50;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 30px;
        }
        .left-section .profile-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .left-section .profile-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #ffffff;
        }
        .left-section .profile-info h2, .left-section .profile-info p {
            margin: 5px 0;
            color: white;
        }
        .right-section {
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #ecf0f1;
            padding: 20px;
            box-sizing: border-box;
        }
        .right-section > header {
            width: 80%;
            margin-bottom: 20px;
            background-color: #ffffff;
            padding: 5px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }
        .right-section > header img {
            height: 40px;
            width: auto;
        }
        .right-section > header .menu-icon {
            width: 30px;
            height: 30px;
            background-color: #7f8c8d;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            cursor: pointer;
        }
        .right-section > header .menu-icon::before {
            content: '≡';
            font-size: 20px;
            color: white;
        }
        .content-container {
            width: 80%;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .user-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #007bff;
        }
        .user-container h2, .user-container p {
            margin: 5px 0;
        }
        .user-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .user-container table, .user-container th, .user-container td {
            border: 1px solid #ccc;
        }
        .user-container th, .user-container td {
            padding: 10px;
            text-align: left;
        }
        .menu {
            margin-top: 20px;
            width: 100%;
        }
        .menu ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .menu ul li {
            margin-bottom: 15px;
            cursor: pointer;
        }
        .menu ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            display: block;
            padding: 15px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #34495e; /* Make menu buttons more prominent */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow to buttons */
        }
        .menu ul li a:hover {
            background-color: #1abc9c; /* Change to a more vibrant color on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
        }

        /* Sidebar styling for mobile/tablet */
        .sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background-color: #2c3e50;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease, opacity 0.3s ease;
            z-index: 1000;
            opacity: 0;
            display: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 30px;
            padding-left: 20px;
            padding-right: 20px;
            box-sizing: border-box;
        }
        .sidebar.active {
            right: 0;
            opacity: 1;
            display: flex;
        }
        .sidebar .close-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 25px;
            color: white;
            cursor: pointer;
        }
        .sidebar .profile-info {
            text-align: center;
        }
        .sidebar .profile-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #ffffff;
        }
        .sidebar .profile-info h2, .sidebar .profile-info p {
            margin: 5px 0;
            color: white;
        }

        /* Responsive Design */
        @media (min-width: 769px) {
            .right-section > header {
                display: none;
            }
            .sidebar {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .left-section {
                display: none;
            }
            .right-section {
                width: 100%;
            }
            .right-section > header {
                width: 100%;
                padding: 5px 10px;
                display: flex;
            }
            .content-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <div class="profile-info">
        <img src="resource/img/profile.png" alt="Profile Image">
        <h2><?php echo $user['email']; ?></h2>
        <p>ID: <?php echo $user['id']; ?></p>
    </div>
    <div class="menu">
        <ul>
            <li><a href="#">Personal Information</a></li>
            <li><a href="video.php">Courses</a></li> <!-- Link to video.php -->
            <li><a href="?logout=true">Log Out</a></li>
        </ul>
    </div>
</div>

<div class="left-section">
    <div class="profile-info">
        <img src="resource/img/profile.png" alt="Profile Image">
        <h2><?php echo $user['email']; ?></h2>
        <p>ID: <?php echo $user['id']; ?></p>
    </div>
    <div class="menu">
        <ul>
            <li><a href="#">Personal Information</a></li>
            <li><a href="video.php">Courses</a></li> <!-- Link to video.php -->
            <li><a href="?logout=true">Log Out</a></li>
        </ul>
    </div>
</div>

<div class="right-section">
    <header>
        <img src="resource/img/logo.png" alt="Logo">
        <div class="menu-icon" id="menuIcon"></div>
    </header>

    <div class="content-container">
        <div class="user-container">
            <img src="resource/img/profile.png" alt="Profile Image">
            <h2><?php echo $user['email']; ?></h2>
            <p>ID: <?php echo $user['id']; ?></p>

            <table>
                <tr>
                    <th>Registration Date</th>
                    <td><?php echo $user['registration_date']; ?></td>
                </tr>
                <tr>
                    <th>Company</th>
                    <td>Example LLC</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><?php echo $user['first_name']; ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?php echo $user['last_name']; ?></td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td>Armenia</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>123 Example St, Yerevan</td>
                </tr>
                <tr>
                    <th>Passport</th>
                    <td>A1234567</td>
                </tr>
                <tr>
                    <th>Contract Number</th>
                    <td>123456789</td>
                </tr>
                <tr>
                    <th>Current Package</th>
                    <td>Premium Package</td>
                </tr>
                <tr>
                    <th>Package Expiration</th>
                    <td>2024-12-31</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    // Toggle sidebar visibility
    document.getElementById('menuIcon').onclick = function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.style.display = ''; // Remove display property to rely on CSS
        setTimeout(function() {
            sidebar.classList.add('active');
        }, 10); // Small delay to ensure the display change takes effect
    };

    // Close the sidebar
    document.getElementById('closeSidebar').onclick = function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.remove('active');
        setTimeout(function() {
            sidebar.style.display = 'none';
        }, 300); // Match the animation duration
    };

    // Close the sidebar when clicking outside of it
    window.onclick = function(event) {
        var sidebar = document.getElementById('sidebar');
        if (event.target == sidebar) {
            sidebar.classList.remove('active');
            setTimeout(function() {
                sidebar.style.display = 'none';
            }, 300); // Match the animation duration
        }
    };
</script>

</body>
</html>
