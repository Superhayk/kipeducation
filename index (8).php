<?php
session_start(); // Սեսիայի սկիզբ

include 'db_connect.php'; // Կապ տվյալների բազայի հետ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Օգտագործողի ստուգում students աղյուսակում
    $sql_student = "SELECT * FROM students WHERE email = '$email' AND password = '$password'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows > 0) {
        $user = $result_student->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $user['email'];
$_SESSION['student_id'] = $user['id'];

        // Վերահղում profile.php էջ
        header("Location: profile.php?id=" . $user['id']);
        exit();
    } else {
        // Ադմինիստրատորի ստուգում admins աղյուսակում
        $sql_admin = "SELECT * FROM admins WHERE email = '$email' AND password = '$password'";
        $result_admin = $conn->query($sql_admin);

        if ($result_admin->num_rows > 0) {
            $admin = $result_admin->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $admin['email'];
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['role'] = $admin['role'];

            // Վերահղում admin.php էջ
            header("Location: admin.php");
            exit();
        } else {
            echo "Նեպարավի էլ. փոստ կամ գաղտնաբառ։";
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIP Education - Lessons</title>
    <link rel="icon" href="resource/img/favicon.png" type="image/png">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            color: #333;
        }
        header {
            width: 100%;
            padding: 5px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        header img {
            height: 50px;
        }
        .account-btn {
            background-color: #007bff;
            color: #ffffff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .account-btn:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
        .account-btn img {
            margin-right: 10px;
            height: 20px; /* կարգավորել նկարի բարձրությունը */
            width: 20px;  /* կարգավորել նկարի լայնությունը */
        }
        .container {
            width: 100%;
            max-width: 700px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
            text-align: center;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .container h1 {
            font-size: 22px;
            font-weight: 400;
            margin-bottom: 10px;
            color: #007bff;
        }
        .container h2 {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
            text-transform: uppercase;
        }
        .container p {
            font-size: 18px;
            font-weight: 300;
            margin-bottom: 30px;
            color: #666;
        }
        .my-account-btn {
            background-color: #007bff;
            color: white;
            padding: 15px 50px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 20px;
            transition: background-color 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .my-account-btn img {
            margin-right: 10px;
            height: 20px; /* կարգավորել նկարի բարձրությունը */
            width: 20px;  /* կարգավորել նկարի լայնությունը */
        }
        .my-account-btn:hover {
            background-color: #0056b3;
        }
        footer {
            margin-top: auto;
            padding: 20px;
            background-color: #ffffff;
            width: 100%;
            text-align: center;
            box-shadow: 0px -4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Popup (Modal) styles */
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
            max-width: 700px; /* Increased the max width */
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
        .input-wrapper {
            position: relative;
            width: 100%;
        }
        .modal-content input[type="text"],
        .modal-content input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            padding-right: 40px; /* Space for the icon */
        }
        .modal-content .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
            height: 20px;
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
        .modal-content .forgot-password {
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            cursor: pointer;
        }
        .modal-content .forgot-password:hover {
            text-decoration: underline;
        }
        .modal-content .divider {
            margin: 20px 0;
            border-bottom: 1px solid #ccc;
        }
        .modal-content .recovery-btn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }
        .modal-content .recovery-btn:hover {
            background-color: #0056b3;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal.show .modal-content {
            transform: scale(1);
        }

        /* New CSS for the Continue button */
        .continue-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            display: block;
            text-align: center;
        }

        .continue-btn:hover {
            background-color: #218838;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 5px 20px;
            }
            header img {
                height: 50px;
            }
        }
    </style>
</head>
<body>

    <header>
        <img src="resource/img/logo.png" alt="KIP Education Logo">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])): ?>
            <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="account-btn">
                <img src="resource/img/user2.png" alt="User Icon">
                My account
            </a>
        <?php else: ?>
            <div class="account-btn" id="accountBtn">
                <img src="resource/img/user2.png" alt="User Icon">
                My account
            </div>
        <?php endif; ?>
    </header>

    <div class="container">
        <h1>Welcome to</h1>
        <h2>KIP Education and Training ID Center</h2>
        <p>Education is power, a developed child is a bright future...</p>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])): ?>
            <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="my-account-btn">
                <img src="resource/img/user2.png" alt="User Icon">
                MY ACCOUNT
            </a>
        <?php else: ?>
            <div class="my-account-btn" id="myAccountBtn">
                <img src="resource/img/user2.png" alt="User Icon">
                MY ACCOUNT
            </div>
        <?php endif; ?>
    </div>

    <footer>
        © 2024 KIP Education - All rights reserved.
    </footer>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h3>Login</h3>
            <form method="POST" action="">
                <input type="text" name="email" placeholder="Email" required>
                <div class="input-wrapper">
                    <input type="password" name="password" id="passwordInput" placeholder="Password" required>
                    <img src="resource/img/hide.png" class="toggle-password" id="togglePassword" alt="Toggle Password Visibility">
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="forgot-password">Մոռացել եմ գաղտնաբառը</div>
            <div class="divider"></div>
            <button class="recovery-btn">Ստանալ Մուտքի տվյալներ</button>
        </div>
    </div>

    <!-- New Video Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeImageModal">&times;</span>
            <video id="demoVideo" style="width: 100%; border-radius: 10px;" autoplay loop controls>
                <source src="resource/19-copy.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <button id="continueButton" class="continue-btn">Continue</button>
        </div>
    </div>

    <script>
        var modal = document.getElementById("myModal");
        var accountBtn = document.getElementById("accountBtn");
        var myAccountBtn = document.getElementById("myAccountBtn");
        var span = document.getElementById("closeModal");
        var passwordInput = document.getElementById("passwordInput");
        var togglePassword = document.getElementById("togglePassword");

        accountBtn.onclick = function() {
            modal.style.display = "flex";
            setTimeout(function() {
                modal.classList.add('show');
            }, 10);
        }

        myAccountBtn.onclick = function() {
            modal.style.display = "flex";
            setTimeout(function() {
                modal.classList.add('show');
            }, 10);
        }

        span.onclick = function() {
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

        togglePassword.onclick = function() {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);

            if (type === "password") {
                togglePassword.src = "resource/img/hide.png";
            } else {
                togglePassword.src = "resource/img/view.png";
            }
        }

        // Updated script for Video Modal
var imageModal = document.getElementById("imageModal");
var closeImageModal = document.getElementById("closeImageModal");
var continueButton = document.getElementById("continueButton");
var demoVideo = document.getElementById("demoVideo"); // Get the video element

// Show the video modal when the page loads
window.onload = function() {
    imageModal.style.display = "flex";
    setTimeout(function() {
        imageModal.classList.add('show');
    }, 10);
}

// Close the video modal when the close button is clicked
closeImageModal.onclick = function() {
    imageModal.classList.remove('show');
    setTimeout(function() {
        imageModal.style.display = "none";
        demoVideo.pause(); // Stop the video
        demoVideo.currentTime = 0; // Reset the video to the beginning
    }, 300);
}

// Redirect to the specified URL in a new tab when the Continue button is clicked
continueButton.onclick = function() {
    window.open("https://kipeducation.am/page/children-ages-3-6-k1", "_blank");
    demoVideo.pause(); // Stop the video
    demoVideo.currentTime = 0; // Reset the video to the beginning
}

// Close the modal when clicking outside of the modal content
window.onclick = function(event) {
    if (event.target == imageModal) {
        imageModal.classList.remove('show');
        setTimeout(function() {
            imageModal.style.display = "none";
            demoVideo.pause(); // Stop the video
            demoVideo.currentTime = 0; // Reset the video to the beginning
        }, 300);
    }
}

    </script>

</body>
</html>
