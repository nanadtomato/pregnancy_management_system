<?php
// Start output buffering
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header and navbar
include('../includes/header.php');
include('../includes/navbar.php');

// Include necessary files
require_once "../config.php"; 
require_once "../classes/User.php"; 

// Check if the user is logged in
if (User::loggedIn()) {
    $userFirstName = $_SESSION['name'];
    $userRole = '';
    $userStatus = $_SESSION['role_id'];

    switch ($userStatus) {
        case 1: $userRole = "Patient"; break;
        case 2: $userRole = "Doctor"; break;
        case 3: $userRole = "Nurse"; break;
        case 4: $userRole = "Admin"; break;
        default: $userRole = "Guest";
    }

    $_SESSION['userFirstName'] = $userFirstName;
    $_SESSION['userRole'] = $userRole;
    $_SESSION['userStatus'] = $userStatus;
} else {
    header("Location: ../login.php");
    exit();
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="dashboard-content">
        <h1>Welcome, <?php echo htmlspecialchars($userFirstName); ?>!</h1>
        <p>Your role: <?php echo htmlspecialchars($userRole); ?></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
