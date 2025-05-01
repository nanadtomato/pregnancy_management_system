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
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
<link rel="stylesheet" href="../css/mainStyles.css">
 
<style>
    .card-hover:hover {
        background-color: #fce4ec;
        transition: 0.3s;
    }
</style>
</head>

 <!-- <?php include('../includes/navbar.php'); ?> -->

 <body>
 <div class="main-content">
 <main>

    <h2 class="text-center mb-4">My Health Records</h2>

    <div class="row">
        <!-- Patient Information -->
        <div class="col-md-6 mb-4">
            <a href="patient_health_record_detail.php?section=patient_info" class="text-decoration-none">
                <div class="card card-hover shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Patient Information</h5>
                        <p class="card-text">View your basic information, past pregnancy history, and family health history.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Consent & Approval -->
        <div class="col-md-6 mb-4">
            <a href="patient_health_record_detail.php?section=consent_approval" class="text-decoration-none">
                <div class="card card-hover shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Consent & Approval</h5>
                        <p class="card-text">View blood collection consents and health service consents.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Health Monitoring -->
        <div class="col-md-6 mb-4">
            <a href="patient_health_record_detail.php?section=health_monitoring" class="text-decoration-none">
                <div class="card card-hover shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Health Monitoring</h5>
                        <p class="card-text">See OGTT results, BP monitoring, fetal ultrasound, and more.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Postnatal Care -->
        <div class="col-md-6 mb-4">
            <a href="patient_health_record_detail.php?section=postnatal_care" class="text-decoration-none">
                <div class="card card-hover shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Postnatal Care</h5>
                        <p class="card-text">Check your postnatal care visits and treatment records.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
 

</main>
</div>
 </body>     

</html>
