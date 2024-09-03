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

if (!isset($_SESSION['user_id'])) {
    echo "No user ID found in session.";
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Fetch courses from the database
$courses = [];
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Check if the timer should start
if (isset($_GET['start_timer'])) {
    $_SESSION['start_time'] = time();
    $_SESSION['popup_shown'] = true; // Show popup after starting timer
    // No need to redirect, just reload the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if the user is resetting the timer
if (isset($_GET['reset_timer'])) {
    unset($_SESSION['start_time']);
    unset($_SESSION['popup_shown']); // Reset the popup
    // No need to redirect, just reload the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if the timer has started
$startTime = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : null;
$buttonLabel = 'Start'; // Default label

if ($startTime) {
    $elapsedTime = time() - $startTime;
    if ($elapsedTime < 48 * 3600) { // 48 hours in seconds
        $buttonLabel = 'View';
    }
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
        /* Your CSS code */
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
            overflow-y: scroll;
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
            max-width: 1200px;
            width: 100%;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 0 auto;
        }
        .content-container h1 {
            font-size: 28px;
            color: #333;
            margin: 0;
        }
        .posts-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 0 -10px;
        }
        .post-container {
            flex: 0 0 calc(20% - 20px);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .post-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .post-container img {
            width: 100%;
            max-width: 350px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: filter 0.3s ease;
        }
        .post-container:nth-child(n+2) img {
            filter: grayscale(100%);
        }
        .post-container:nth-child(n+2) .post-button button {
            background-color: #6c757d;
            cursor: not-allowed;
            pointer-events: none;
        }
        .post-info {
            text-align: center;
            width: 100%;
            padding: 0 15px;
        }
        .post-info p {
            margin: 8px 0;
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
        .post-button {
            text-align: center;
            margin-top: 15px;
        }
        .post-button button {
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.1);
        }
        .post-button button:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .overlay, .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .popup {
            width: 90%;
            max-width: 500px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0;
            z-index: 2000;
        }
        .popup.active {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        .close-popup-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333;
        }
        .video-container {
            width: 90%;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: inline-block;
            vertical-align: top;
            margin: 0 auto;
        }
        .files-container {
            width: 90%;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: inline-block;
            vertical-align: top;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            .post-container {
                width: 100%;
                margin: 10px 0;
            }
            .post-container img {
                width: 100%;
                height: auto;
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
            <li><a href="video.php">Courses</a></li>
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
            <li><a href="video.php">Courses</a></li>
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
        <h1>COURSES</h1>

        <!-- Post Containers -->
        <div class="posts-row">
            <?php foreach ($courses as $course): ?>
                <div class="post-container" id="post<?php echo $course['ID']; ?>">
                    <img src="<?php echo $course['image']; ?>" alt="<?php echo $course['title']; ?>">
                    <div class="post-info">
                        <p><?php echo $course['tag']; ?></p>
                        <p><?php echo $course['title']; ?></p>
                    </div>
                    <div class="post-button">
                        <?php if ($course['ID'] == 1): ?>
                            <button onclick="showCoursePopup()">Click</button>
                        <?php else: ?>
                            <button class="btn-closed">Closed</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Reset Button at the bottom -->
    <div style="text-align: center; margin-top: 20px;">
        <form method="get" action="">
            <button type="submit" name="reset_timer" class="btn-open" style="background-color: #dc3545;">Reset</button>
        </form>
    </div>
</div>

<!-- Popup and Overlay -->
<div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <button class="close-popup-btn" onclick="closePopup()">&times;</button>
    <h2>Course 1</h2>
    <div class="video-container">
        <h3>Video</h3>
        <div class="video-content">
            <video src="resource/courses/1/1. Letter Aa_Bb.mp4" controls></video>
        </div>
    </div>
    <div class="files-container">
        <h3>Files</h3>
        <table class="files-table">
            <tr>
                <td>Letter Aa.pdf</td>
                <td><a href="resource/courses/1/Letter Aa.pdf" class="btn-download" download>Download</a></td>
            </tr>
            <tr>
                <td>Letter Aa.pptx</td>
                <td><a href="resource/courses/1/Letter Aa.pptx" class="btn-download" download>Download</a></td>
            </tr>
            <tr>
                <td>Letter B,b.pptx</td>
                <td><a href="resource/courses/1/Letter B,b.pptx" class="btn-download" download>Download</a></td>
            </tr>
            <tr>
                <td>Letter B,b.pptx</td>
                <td><a href="resource/courses/1/Letter B,b.pptx" class="btn-download" download>Download</a></td>
            </tr>
        </table>
    </div>
</div>

<script>
    function showCoursePopup() {
        var overlay = document.getElementById('overlay');
        var popup = document.getElementById('popup');
        overlay.classList.add('active');
        popup.classList.add('active');
    }

    function closePopup() {
        var overlay = document.getElementById('overlay');
        var popup = document.getElementById('popup');
        overlay.classList.remove('active');
        popup.classList.remove('active');
    }

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
</script>

</body>
</html>
