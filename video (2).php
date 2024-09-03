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
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: row;
        }

        /* Left Section Styling */
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
            overflow-y: auto;
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

        /* Sidebar Menu Styling */
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
            background-color: #34495e;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .menu ul li a:hover {
            background-color: #1abc9c;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }
        .menu ul li a.active {
            background-color: #1abc9c;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        /* Right Section Styling */
        .right-section {
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #ecf0f1;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .right-section > header {
            width: 100%;
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

        /* Header Content Styling */
        .right-section > .header-content {
            width: 90%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: #2980b9;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }
        .right-section > .header-content h1 {
            font-size: 2rem;
            color: white;
        }
        .right-section > .header-content .start-btn,
        .right-section > .header-content .reset-btn {
            padding: 10px 25px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin-right: 10px;
        }
        .right-section > .header-content .start-btn {
            background-color: #e74c3c;
        }
        .right-section > .header-content .start-btn:hover {
            background-color: #c0392b;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .right-section > .header-content .reset-btn {
            background-color: #f39c12;
        }
        .right-section > .header-content .reset-btn:hover {
            background-color: #e67e22;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .right-section > .header-content .filter-btn {
            padding: 8px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .right-section > .header-content .filter-btn:hover {
            background-color: #2980b9;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Posts Grid Styling */
        .posts-grid {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .post {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        .post img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }
        .post h2 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .post p {
            font-size: 0.9rem;
            margin-bottom: 10px;
            color: #7f8c8d;
        }
        .post .view-btn {
            padding: 8px 15px;
            background-color: #bdc3c7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: default;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .post:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .timer {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 1rem;
            font-family: 'Courier New', Courier, monospace;
            transition: background-color 0.3s ease;
        }

        /* Popup Styling */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            width: 80%;
            max-width: 600px;
            padding: 20px;
            animation: zoomIn 0.3s forwards; /* Zoom-in animation */
        }
        .popup video {
            width: 100%;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .popup video[controlsList] {
            controlsList: nodownload;
        }
        .popup h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #2c3e50;
            text-align: center;
        }
        .popup a {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #3498db;
            color: white;
            padding: 10px 0;
            margin-bottom: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .popup a:hover {
            background-color: #2980b9;
        }
        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: #c0392b;
            cursor: pointer;
        }

        /* Terms and Conditions Popup Styling */
        .terms-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1002;
            width: 80%;
            max-width: 500px;
            padding: 20px;
            text-align: center;
            animation: zoomIn 0.3s forwards; /* Zoom-in animation */
        }
        .terms-popup h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #2980b9;
        }
        .terms-popup p {
            font-size: 1rem;
            margin-bottom: 20px;
            color: #7f8c8d;
        }
        .terms-popup button {
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .terms-popup button:hover {
            background-color: #2ecc71;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @keyframes zoomIn {
            from {
                transform: translate(-50%, -50%) scale(0);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (min-width: 769px) {
            body {
                flex-direction: row;
            }
            .left-section {
                width: 20%;
            }
            .right-section {
                width: 80%;
            }
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
            .right-section > .header-content {
                width: 100%;
                padding: 0 10px;
            }
            .right-section > .header-content h1 {
                font-size: 1.5rem;
            }
            .posts-grid {
                grid-template-columns: repeat(2, 1fr); /* Two posts per row on tablets */
            }
            .post {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .posts-grid {
                grid-template-columns: 1fr; /* One post per row on phones */
            }
            .right-section > .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            .right-section > .header-content h1 {
                margin-bottom: 10px;
            }
            .post {
                margin-bottom: 15px;
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
            <li><a href="#" class="active">Personal Information</a></li>
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
    <div class="header-content">
        <h1>Courses</h1> <!-- Beautifully styled "Courses" title -->
        <button class="start-btn" id="startBtn">Start</button> <!-- Start button -->
        <button class="reset-btn" id="resetBtn">Reset</button> <!-- Reset button -->
        <button class="filter-btn">Filter</button> <!-- Filter button -->
    </div>
    
    <div class="posts-grid">
        <!-- Post 1 -->
        <div class="post" id="post1">
            <img src="resource/img/Новый проект (92).png" alt="Course Image">
            <h2>Course 1</h2>
            <p>Tag: Letters</p>
            <button class="view-btn" id="viewBtn1">Closed</button> <!-- Gray "Closed" button with no pointer -->
            <div class="timer" id="timer1">48:00:00</div> <!-- Timer for 48 hours -->
        </div>
        
        <!-- Post 2 -->
        <div class="post" id="post2">
            <img src="resource/courses/2/Новый проект (98) (1).png" alt="Course Image">
            <h2>Course 2</h2>
            <p>Tag: Numbers</p>
            <button class="view-btn" id="viewBtn2">Closed</button>
            <div class="timer" id="timer2">48:00:00</div> <!-- Timer for 48 hours -->
        </div>
        
        <!-- Post 3 -->
        <div class="post" id="post3">
            <img src="resource/courses/3/Новый проект (98) (1) (1) (1).png" alt="Course Image">
            <h2>Course 3</h2>
            <p>Tag: Armenia</p>
            <button class="view-btn" id="viewBtn3">Closed</button>
            <div class="timer" id="timer3">48:00:00</div> <!-- Timer for 48 hours -->
        </div>

        <!-- Other posts (skipped for brevity) -->
    </div>
</div>

<!-- Popup for video and files -->
<div class="popup-overlay" id="popupOverlay"></div>
<div class="popup" id="coursePopup">
    <span class="popup-close" id="popupClose">&times;</span>
    <video controls controlsList="nodownload" id="courseVideo" preload="none">
        <source src="" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <h2>Files</h2>
    <div id="fileLinks">
        <!-- Links will be populated dynamically -->
    </div>
</div>

<!-- Terms and Conditions Popup -->
<div class="popup-overlay" id="termsOverlay"></div>
    <div class="terms-popup" id="termsPopup">
        <span class="popup-close" id="termsClose">&times;</span>
        <h2>Terms and Conditions</h2>
        <p>Please accept the terms and conditions to proceed.</p>
        <button id="acceptTerms">Accept</button>
    </div>

<script>
    let countdownStart = false;
    let timerInterval1, timerInterval2, timerInterval3;

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

    // Start button functionality with terms and conditions
    document.getElementById('startBtn').onclick = function() {
        document.getElementById('termsOverlay').style.display = 'block';
        document.getElementById('termsPopup').style.display = 'block';
    };

    document.getElementById('acceptTerms').onclick = function() {
        document.getElementById('termsOverlay').style.display = 'none';
        document.getElementById('termsPopup').style.display = 'none';
        activatePost1(); // Start the countdown for the first post only after accepting the terms
        saveState(); // Save the state for persistence
    };

    document.getElementById('termsClose').onclick = function() {
        document.getElementById('termsOverlay').style.display = 'none';
        document.getElementById('termsPopup').style.display = 'none';
    };

    // Activate the first post
    function activatePost1() {
        var post1 = document.getElementById('post1');
        var viewBtn1 = document.getElementById('viewBtn1');
        var timerElement1 = document.getElementById('timer1');
        
        post1.querySelector('img').style.filter = 'none'; // Remove grayscale filter
        viewBtn1.textContent = 'View Course'; // Change button text
        viewBtn1.style.backgroundColor = '#3498db'; // Change button color to blue
        post1.style.borderColor = '#3498db'; // Change post border color to blue
        viewBtn1.style.cursor = 'pointer'; // Change cursor to pointer
        
        // Start or resume the countdown timer for the first post
        if (!countdownStart) {
            var timeLeft1 = parseInt(localStorage.getItem('timeLeft1')) || 48 * 60 * 60; // 48 ժամ = 172800 վայրկյան
            timerElement1.style.backgroundColor = '#3498db';
            timerInterval1 = setInterval(function() {
                timeLeft1--;
                var hours = Math.floor(timeLeft1 / 3600);
                var minutes = Math.floor((timeLeft1 % 3600) / 60);
                var seconds = timeLeft1 % 60;
                timerElement1.textContent = (hours < 10 ? '0' + hours : hours) + ':' +
                                            (minutes < 10 ? '0' + minutes : minutes) + ':' +
                                            (seconds < 10 ? '0' + seconds : seconds);
                localStorage.setItem('timeLeft1', timeLeft1);
                if (timeLeft1 === 24 * 60 * 60) { // 24 ժամ մնաց
                    activatePost2();
                }
                if (timeLeft1 <= 0) {
                    clearInterval(timerInterval1);
                    timerElement1.textContent = '00:00:00';
                    timerElement1.style.backgroundColor = 'gray';
                    localStorage.removeItem('timeLeft1');
                    deactivatePost1();
                }
            }, 1000);
            countdownStart = true;
        }
    }

    // Deactivate the first post
    function deactivatePost1() {
        var post1 = document.getElementById('post1');
        var viewBtn1 = document.getElementById('viewBtn1');
        post1.querySelector('img').style.filter = 'grayscale(100%)'; // Apply grayscale filter
        viewBtn1.textContent = 'Closed'; // Change button text back to "Closed"
        viewBtn1.style.backgroundColor = '#bdc3c7'; // Change button color back to gray
        post1.style.borderColor = 'transparent'; // Remove post border color
        viewBtn1.style.cursor = 'default'; // Change cursor back to default
        countdownStart = false;
        localStorage.removeItem('activePost1'); // Clear the active post state
    }

    // Activate the second post
    function activatePost2() {
        var post2 = document.getElementById('post2');
        var viewBtn2 = document.getElementById('viewBtn2');
        var timerElement2 = document.getElementById('timer2');

        post2.querySelector('img').style.filter = 'none'; // Remove grayscale filter
        viewBtn2.textContent = 'View Course'; // Change button text
        viewBtn2.style.backgroundColor = '#3498db'; // Change button color to blue
        post2.style.borderColor = '#3498db'; // Change post border color to blue
        viewBtn2.style.cursor = 'pointer'; // Change cursor to pointer

        // Start or resume the countdown timer for the second post
        var timeLeft2 = parseInt(localStorage.getItem('timeLeft2')) || 48 * 60 * 60; // 48 ժամ = 172800 վայրկյան
        timerElement2.style.backgroundColor = '#3498db';
        timerInterval2 = setInterval(function() {
            timeLeft2--;
            var hours = Math.floor(timeLeft2 / 3600);
            var minutes = Math.floor((timeLeft2 % 3600) / 60);
            var seconds = timeLeft2 % 60;
            timerElement2.textContent = (hours < 10 ? '0' + hours : hours) + ':' +
                                        (minutes < 10 ? '0' + minutes : minutes) + ':' +
                                        (seconds < 10 ? '0' + seconds : seconds);
            localStorage.setItem('timeLeft2', timeLeft2);
            if (timeLeft2 === 24 * 60 * 60) { // 24 ժամ մնաց
                activatePost3();
            }
            if (timeLeft2 <= 0) {
                clearInterval(timerInterval2);
                timerElement2.textContent = '00:00:00';
                timerElement2.style.backgroundColor = 'gray';
                localStorage.removeItem('timeLeft2');
                deactivatePost2();
            }
        }, 1000);
    }

    // Deactivate the second post
    function deactivatePost2() {
        var post2 = document.getElementById('post2');
        var viewBtn2 = document.getElementById('viewBtn2');
        post2.querySelector('img').style.filter = 'grayscale(100%)'; // Apply grayscale filter
        viewBtn2.textContent = 'Closed'; // Change button text back to "Closed"
        viewBtn2.style.backgroundColor = '#bdc3c7'; // Change button color back to gray
        post2.style.borderColor = 'transparent'; // Remove post border color
        viewBtn2.style.cursor = 'default'; // Change cursor back to default
        localStorage.removeItem('activePost2'); // Clear the active post state
    }

    // Activate the third post
    function activatePost3() {
        var post3 = document.getElementById('post3');
        var viewBtn3 = document.getElementById('viewBtn3');
        var timerElement3 = document.getElementById('timer3');

        post3.querySelector('img').style.filter = 'none'; // Remove grayscale filter
        viewBtn3.textContent = 'View Course'; // Change button text
        viewBtn3.style.backgroundColor = '#3498db'; // Change button color to blue
        post3.style.borderColor = '#3498db'; // Change post border color to blue
        viewBtn3.style.cursor = 'pointer'; // Change cursor to pointer

        // Start or resume the countdown timer for the third post
        var timeLeft3 = parseInt(localStorage.getItem('timeLeft3')) || 48 * 60 * 60; // 48 ժամ = 172800 վայրկյան
        timerElement3.style.backgroundColor = '#3498db';
        timerInterval3 = setInterval(function() {
            timeLeft3--;
            var hours = Math.floor(timeLeft3 / 3600);
            var minutes = Math.floor((timeLeft3 % 3600) / 60);
            var seconds = timeLeft3 % 60;
            timerElement3.textContent = (hours < 10 ? '0' + hours : hours) + ':' +
                                        (minutes < 10 ? '0' + minutes : minutes) + ':' +
                                        (seconds < 10 ? '0' + seconds : seconds);
            localStorage.setItem('timeLeft3', timeLeft3);
            if (timeLeft3 <= 0) {
                clearInterval(timerInterval3);
                timerElement3.textContent = '00:00:00';
                timerElement3.style.backgroundColor = 'gray';
                localStorage.removeItem('timeLeft3');
                deactivatePost3();
            }
        }, 1000);
    }

    // Deactivate the third post
    function deactivatePost3() {
        var post3 = document.getElementById('post3');
        var viewBtn3 = document.getElementById('viewBtn3');
        post3.querySelector('img').style.filter = 'grayscale(100%)'; // Apply grayscale filter
        viewBtn3.textContent = 'Closed'; // Change button text back to "Closed"
        viewBtn3.style.backgroundColor = '#bdc3c7'; // Change button color back to gray
        post3.style.borderColor = 'transparent'; // Remove post border color
        viewBtn3.style.cursor = 'default'; // Change cursor back to default
        localStorage.removeItem('activePost3'); // Clear the active post state
    }

    // Reset button functionality
    document.getElementById('resetBtn').onclick = function() {
        clearInterval(timerInterval1);
        clearInterval(timerInterval2);
        clearInterval(timerInterval3);
        localStorage.removeItem('activePost1');
        localStorage.removeItem('activePost2');
        localStorage.removeItem('activePost3');
        localStorage.removeItem('timeLeft1');
        localStorage.removeItem('timeLeft2');
        localStorage.removeItem('timeLeft3');
        location.reload(); // Reload the page to reset everything
    };

    // Popup functionality for post 1
    document.getElementById('viewBtn1').onclick = function() {
        if (document.getElementById('viewBtn1').textContent === 'View Course') {
            document.getElementById('popupOverlay').style.display = 'block';
            document.getElementById('coursePopup').style.display = 'block';
            
            // Set video and file links for post 1
            document.getElementById('courseVideo').src = 'resource/courses/1/1. Letter Aa_Bb.mp4';
            document.getElementById('fileLinks').innerHTML = `
                <a href="resource/courses/1/Letter Aa.pdf" download>Download Letter Aa.pdf</a>
                <a href="resource/courses/1/Letter Aa.pptx" download>Download Letter Aa.pptx</a>
                <a href="resource/courses/1/Letter Bb.pdf" download>Download Letter Bb.pdf</a>
                <a href="resource/courses/1/Letter.pptx" download>Download Letter.pptx</a>
            `;
            document.querySelector('#coursePopup video').load(); // Reload the video source
        }
    };

    // Popup functionality for post 2
    document.getElementById('viewBtn2').onclick = function() {
        if (document.getElementById('viewBtn2').textContent === 'View Course') {
            document.getElementById('popupOverlay').style.display = 'block';
            document.getElementById('coursePopup').style.display = 'block';
            
            // Set video and file links for post 2
            document.getElementById('courseVideo').src = 'resource/courses/2/1. Number.mp4';
            document.getElementById('fileLinks').innerHTML = `
                <a href="resource/courses/2/Numbers.pptx" download>Download Numbers.pptx</a>
                <a href="resource/courses/2/Number worksheet.pdf" download>Download Number worksheet.pdf</a>
            `;
            document.querySelector('#coursePopup video').load(); // Reload the video source
        }
    };

    // Popup functionality for post 3
    document.getElementById('viewBtn3').onclick = function() {
        if (document.getElementById('viewBtn3').textContent === 'View Course') {
            document.getElementById('popupOverlay').style.display = 'block';
            document.getElementById('coursePopup').style.display = 'block';
            
            // Set video and file links for post 3
            document.getElementById('courseVideo').src = 'resource/courses/3/1. Armenia.mp4';
            document.getElementById('fileLinks').innerHTML = `
                <a href="resource/courses/3/Armenia map worksheet.pdf" download>Download Armenia map worksheet.pdf</a>
                <a href="resource/courses/3/Armenia.pptx" download>Download Armenia.pptx</a>
                <a href="resource/courses/3/10 facts about Armenia.docx" download>Download 10 facts about Armenia.docx</a>
            `;
            document.querySelector('#coursePopup video').load(); // Reload the video source
        }
    };

    // Close popup
    document.getElementById('popupClose').onclick = function() {
        document.getElementById('popupOverlay').style.display = 'none';
        document.getElementById('coursePopup').style.display = 'none';
        document.querySelector('#coursePopup video').pause(); // Stop the video when the popup is closed
    };

    // Close popup when clicking outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('popupOverlay')) {
            document.getElementById('popupOverlay').style.display = 'none';
            document.getElementById('coursePopup').style.display = 'none';
            document.querySelector('#coursePopup video').pause(); // Stop the video when the popup is closed
        }
    };

    // Save the state of the active post and timer
    function saveState() {
        localStorage.setItem('activePost1', 'post1');
        localStorage.setItem('activePost2', 'post2');
        localStorage.setItem('activePost3', 'post3');
    }

    // Load the state when the page is reloaded
    window.onload = function() {
        if (localStorage.getItem('activePost1') === 'post1') {
            activatePost1();
        }
        if (localStorage.getItem('activePost2') === 'post2') {
            var timeLeft1 = parseInt(localStorage.getItem('timeLeft1')) || 48 * 60 * 60;
            if (timeLeft1 <= 24 * 60 * 60) {
                activatePost2(); // Activate post2 only if post1 timer is at or below 24 hours
            }
        }
        if (localStorage.getItem('activePost3') === 'post3') {
            var timeLeft2 = parseInt(localStorage.getItem('timeLeft2')) || 48 * 60 * 60;
            if (timeLeft2 <= 24 * 60 * 60) {
                activatePost3(); // Activate post3 only if post2 timer is at or below 24 hours
            }
        }
    };
</script>

</body>
</html>
