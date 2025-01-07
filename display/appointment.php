<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$userFirstName = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<?php include('../includes/header.php'); ?>
<head>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <title>Appointment</title>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include('../includes/navbar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="margin-left: 210px;">
            <!-- Main Content -->
            <div id="content" class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Appointment</h1>

                
            </div>
        </div>
    </div>
    <?php include('../includes/scripts.php'); ?>
</body>
</html>
