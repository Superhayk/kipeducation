<?php
session_start();
include 'db_connect.php';

// Check if the session is valid
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Get the total number of reports
$reportCountResult = $conn->query("SELECT COUNT(*) as total FROM reports");
if ($reportCountResult) {
    $reportCount = $reportCountResult->fetch_assoc()['total'];
} else {
    $reportCount = 0; // Handle the error gracefully
}

// Get the list of reports
$reportsResult = $conn->query("SELECT id, report_name, date_created FROM reports ORDER BY date_created DESC");

$conn->close();

include 'sidebar.php'; // Include the sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Reports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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
            .content {
                margin-left: 0;
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
            <h1>Reports</h1>
            <p>Manage and view reports here.</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Reports List</div>
                    <div class="card-body">
                        <?php if ($reportsResult && $reportsResult->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Report Name</th>
                                    <th scope="col">Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($report = $reportsResult->fetch_assoc()): ?>
                                <tr>
                                    <th scope="row"><?php echo $report['id']; ?></th>
                                    <td><?php echo $report['report_name']; ?></td>
                                    <td><?php echo $report['date_created']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No reports found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
