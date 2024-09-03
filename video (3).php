<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
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

        /* Mobile header (visible only on mobile devices) */
        .right-section > header {
            display: none;
            width: 100%;
            background-color: #ffffff;
            padding: 5px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        /* Header 2: Lessons */
        .header-lessons {
            width: 100%;
            background-color: #2980b9;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 2rem; /* Smaller font size */
            font-weight: bold;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .header-lessons::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
            transform: skewX(-20deg);
            transition: transform 0.5s ease-in-out;
        }

        .header-lessons:hover::before {
            transform: skewX(-20deg) translateX(200%);
        }

        .header-lessons span {
            position: relative;
            z-index: 1;
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

        /* Responsive Design */
        @media (min-width: 769px) {
            .right-section > header {
                display: none;
            }
            .sidebar {
                display: none !important;
            }
            .header-lessons {
                margin-top: 0;
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
                display: flex;
            }
            .header-lessons {
                margin-top: 10px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                position: fixed;
                left: 0;
                top: 0;
                height: 100%;
                background-color: #2c3e50;
                z-index: 2000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .right-section {
                width: 100%;
                margin-left: 0;
            }
            .header-lessons {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <div class="profile-info">
        <img src="resource/img/profile.png" alt="Profile Image">
        <h2>User Name</h2>
        <p>ID: 12345</p>
    </div>
    <div class="menu">
        <ul>
            <li><a href="#" class="active">Personal Information</a></li>
            <li><a href="video.html">Courses</a></li>
            <li><a href="index.html">Log Out</a></li>
        </ul>
    </div>
</div>

<div class="left-section">
    <div class="profile-info">
        <img src="resource/img/profile.png" alt="Profile Image">
        <h2>User Name</h2>
        <p>ID: 12345</p>
    </div>
    <div class="menu">
        <ul>
            <li><a href="#">Personal Information</a></li>
            <li><a href="video.html">Courses</a></li>
            <li><a href="index.html">Log Out</a></li>
        </ul>
    </div>
</div>

<div class="right-section">
    <header>
        <img src="resource/img/logo.png" alt="Logo">
        <div class="menu-icon" id="menuIcon"></div>
    </header>

    <div class="header-lessons">
        <span>Lessons</span>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle
    $('#menuIcon').click(function() {
        $('#sidebar').addClass('active');
    });

    // Close sidebar
    $('#closeSidebar').click(function() {
        $('#sidebar').removeClass('active');
    });

    // Close the sidebar when clicking outside of it
    $(window).click(function(event) {
        if (event.target == document.getElementById('sidebar')) {
            $('#sidebar').removeClass('active');
        }
    });
</script>

</body>
</html>
