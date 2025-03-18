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
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="css/styles.css">
    <title>Pregnancy Progress</title>
    <style>
        .round-button {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: rgb(223, 78, 158);
            color: white;
            font-size: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
        }
    </style>
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
                    <div class="row mb-4">
                        <div class="col text-center">
                            <h1>Welcome, <?php echo htmlspecialchars($userFirstName); ?></h1>
                            <p class="text-muted">Track your pregnancy progress, monitor baby kicks, and review trends.</p>
                        </div>
                    </div>

                    <!-- Kick Counter -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Kick Counter Tracker</h6>
                        </div>
                        <div class="card-body text-center">
                            <p>Press the button below to start tracking kicks. Tracking stops after <strong>10 kicks</strong> or <strong>12 hours</strong>.</p>
                            <button id="startStopBtn" class="round-button">Start</button>
                            <p class="mt-3"><strong>Kicks Counted:</strong> <span id="kickCount">0</span></p>
                            <p><strong>Time Left:</strong> <span id="timeLeft">12:00:00</span></p>
                        </div>
                    </div>

                    <!-- Manual Log Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Manual Kick Log</h6>
                        </div>
                        <div class="card-body">
                            <form id="manualLogForm">
                                <div class="form-group">
                                    <label for="logDate">Date</label>
                                    <input type="date" id="logDate" name="logDate" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="startTime">Start Time</label>
                                    <input type="time" id="startTime" name="startTime" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="endTime">End Time</label>
                                    <input type="time" id="endTime" name="endTime" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="kickCountManual">Kick Count</label>
                                    <input type="number" id="kickCountManual" name="kickCountManual" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Log</button>
                            </form>
                        </div>
                    </div>

                    <!-- Kick Log Chart -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Kick Log Analysis</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="logDatePicker">Select Date:</label>
                                <input type="date" id="logDatePicker" class="form-control">
                            </div>
                            <canvas id="kickChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    


    <!-- Include scripts -->
    <?php include('../includes/scripts.php'); ?>
    <?php include('../PregnancyTracking/get_kick_log.php'); ?>
    <?php include('../PregnancyTracking/save_kick_log.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../PregnancyTracking/pregnancyProgress.js"></script>

    
</body>
</html>
