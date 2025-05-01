<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$userFirstName = $_SESSION['name'];
// Fetch counts from DB
global $conn;

// Get total patients
$totalPatientsQuery = $conn->query("SELECT COUNT(*) AS total FROM patients");
$totalPatients = $totalPatientsQuery->fetch_assoc()['total'];


    // // Get total appointments
    // $totalAppointmentsQuery = $conn->query("SELECT COUNT(*) AS total FROM appointments");
    // $totalAppointments = $totalAppointmentsQuery->fetch_assoc()['total'];

// Get patient status list (dummy data or real data, adjust as needed)
// $patientStatusQuery = $conn->query("
//     SELECT p.full_name, a.status, a.appointment_date 
//     FROM appointments a 
//     JOIN patients p ON a.patient_id = p.id 
//     ORDER BY a.appointment_date DESC
// ");

?>
<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
    <title>Doctor Dashboard</title>
    <style>
        .card-custom {
            border-left: 5px solid #f78da7;
            background-color: #fff0f4;
        }
    </style>

</head>
<body>
<?php include('../includes/navbar.php'); ?>


<main> 
        <h2 class="text-center mb-4">Welcome, Dr. <?php echo htmlspecialchars($userFirstName); ?></h2>
<!-- Stats Cards -->
<div class="row g-4 mb-4">
            <div class="col-md-6">
            <div class="card card-custom shadow-sm" style="width: 18rem;">
               
                    <div class="card-body">
                        <h5 class="card-title text-muted">Total Patients</h5>
                        <h2 class="text-dark"><?php echo $totalPatients; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
            <div class="card card-custom shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Total Appointments</h5>
                        <!-- <h2 class="text-dark"><?php echo $totalAppointments; ?></h2> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Status Table -->
        <div class="card shadow-sm">
            
            <div class="card-header bg-danger-subtle text-dark">Recent Appointment Status</div>
              
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Patient Name</th>
                            <th>Status</th>
                            <th>Appointment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <?php while ($row = $patientStatusQuery->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                <td><?php echo date("d M Y", strtotime($row['appointment_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?> -->
                    </tbody>
                </table>
            </div>
            </main>



       

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php include('../includes/scripts.php'); ?>
</body>
    

</html>
