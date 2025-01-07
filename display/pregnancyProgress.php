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
    <title>Patient Dashboard</title>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include('../includes/navbar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="margin-left: 210px;">
            <!-- Main Content -->
            <div id="content" class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Pregnancy Progress</h1>

                <div class="container mt-5">
    <!-- Header Section -->
    <div class="row mb-4">
      <div class="col text-center">
        <h1>Track Pregnancy Progress</h1>
        <p class="text-muted">Log baby kicks, monitor trends, and receive important alerts</p>
      </div>
    </div>

    <!-- Log Baby Kicks Section -->
    <div class="row mb-4">
      <div class="col-md-6">
        <h4>Log Baby Kicks</h4>
        <form id="kickLogForm">
          <div class="mb-3">
            <label for="kickDate" class="form-label">Date</label>
            <input type="date" id="kickDate" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="kickTime" class="form-label">Time</label>
            <input type="time" id="kickTime" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="kickCount" class="form-label">Number of Kicks</label>
            <input type="number" id="kickCount" class="form-control" min="1" required>
          </div>
          <button type="submit" class="btn btn-primary">Log Kick</button>
        </form>
      </div>

      <!-- Alerts Section -->
      <div class="col-md-6">
        <h4>Alerts & Notifications</h4>
        <div class="alert alert-warning" role="alert">
          Fewer than 10 baby kicks recorded in 12 hours. Please visit the hospital immediately.
        </div>
      </div>
    </div>

    <!-- Summary and Trends Section -->
    <div class="row">
      <div class="col">
        <h4>Summary & Trends</h4>
        <div class="card">
          <div class="card-body">
            <canvas id="kickTrendChart"></canvas>
          </div>
    
                
            </div>
        </div>
    </div>
    <?php include('../includes/scripts.php'); ?>
</body>
</html>
