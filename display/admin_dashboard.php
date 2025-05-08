<?php

require_once "../config.php";
session_start();

// Check if admin
if ($_SESSION['role_id'] != 4) {
    header("Location: ../login/login.php");
    exit();
}

// Count all approved users by role
$count_patient = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 1 AND is_approved = 1")->fetch_assoc()['total'];
$count_doctor = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 2 AND is_approved = 1")->fetch_assoc()['total'];
$count_nurse = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 3 AND is_approved = 1")->fetch_assoc()['total'];

// Pending approvals
$pending_approvals = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_approved = 0")->fetch_assoc()['total'];

// Get recent activities (example: registrations from the last week)
$recent_activities = $conn->query("SELECT name, role_id, created_at FROM users WHERE created_at >= CURDATE() - INTERVAL 7 DAY ORDER BY created_at DESC");

// Data for Chart.js (Number of patients registered in the last 30 days)
$patient_data = [];
$doctor_data = [];
$dates = [];
for ($i = 30; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $date;

    $patient_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 1 AND created_at LIKE '$date%'")->fetch_assoc()['total'];
    $doctor_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 2 AND created_at LIKE '$date%'")->fetch_assoc()['total'];

    $patient_data[] = $patient_count;
    $doctor_data[] = $doctor_count;
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/mainStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
.card-custom {
        transition: transform 0.3s ease-in-out;
    }

    .card-custom:hover {
        transform: scale(1.05);
    }

    .card-header {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .card-body h2 {
        font-size: 2.5rem;
        font-weight: bold;
    }

    /* Optional: add a custom color for card header background if needed */
    .card-custom .card-header {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Optional: Customizing the card body */
    .card-custom .card-body {
        background-color: #fce9f1;
    }

    </style>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    <main>
    <div class="row">
    <!-- Total Patient Card -->
    <div class="col-md-3">
        <div class="card card-custom text-white bg-pink mb-4">
            <div class="card-header">
                <i class="bi bi-person-fill" style="font-size: 2rem;"></i> Total Patients
            </div>
            <div class="card-body">
                <h2><?= $count_patient ?></h2>
            </div>
        </div>
    </div>

    <!-- Total Doctor Card -->
    <div class="col-md-3">
        <div class="card card-custom text-white bg-pink mb-4">
            <div class="card-header">
                <i class="bi bi-person-check-fill" style="font-size: 2rem;"></i> Total Doctors
            </div>
            <div class="card-body">
                <h2><?= $count_doctor ?></h2>
            </div>
        </div>
    </div>

    <!-- Total Nurse Card -->
    <div class="col-md-3">
        <div class="card card-custom text-white bg-pink mb-4">
            <div class="card-header">
                <i class="bi bi-person-badge" style="font-size: 2rem;"></i> Total Nurses
            </div>
            <div class="card-body">
                <h2><?= $count_nurse ?></h2>
            </div>
        </div>
    </div>

    <!-- Pending Approvals Card -->
    <div class="col-md-3">
        <div class="card card-custom text-white bg-pink mb-4">
            <div class="card-header">
                <i class="bi bi-person-dash-fill" style="font-size: 2rem;"></i> Pending Approvals
            </div>
            <div class="card-body">
                <h2><?= $pending_approvals ?></h2>
            </div>
        </div>
    </div>


    
    
    

        <!-- Recent Activity Section -->
        <div class="col-md-6">
            <h4>Recent Activity</h4>
            <ul class="list-group">
                <?php while ($activity = $recent_activities->fetch_assoc()) : ?>
                    <li class="list-group-item">
                        <?= $activity['name'] ?> (<?= $activity['role_id'] == 1 ? 'Patient' : ($activity['role_id'] == 2 ? 'Doctor' : 'Nurse') ?>) - <?= $activity['created_at'] ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>Patient Registrations Over Time</h4>
            <canvas id="patientChart"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Doctor Registrations Over Time</h4>
            <canvas id="doctorChart"></canvas>
        </div>
    </div>

    </main>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    // Chart.js for Patient Registrations Over Time
    const ctx1 = document.getElementById('patientChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Patients Registered',
                data: <?php echo json_encode($patient_data); ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false,
            }]
        },
    });

    // Chart.js for Doctor Registrations Over Time
    const ctx2 = document.getElementById('doctorChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Doctors Registered',
                data: <?php echo json_encode($doctor_data); ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false,
            }]
        },
    });
</script>

    <?php include('../includes/scripts.php'); ?>

</body>
    

</html>
