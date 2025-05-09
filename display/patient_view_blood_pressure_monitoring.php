<?php
session_start();
require_once "../config.php";

// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Get patient_id from user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

if (!$patient_id) {
    die("Patient not found for this user.");
}

// Get patient full name
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_full_name);
$stmt->fetch();
$stmt->close();

// Get blood pressure monitoring records
$stmt = $conn->prepare("SELECT * FROM blood_pressure_monitoring WHERE patient_id = ? ORDER BY monitoring_datetime DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$success = "";
$error = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>My Blood Pressure Monitoring Records</title>

    <style>
        .btn-link {
            color: #d81b60;
        }

        .btn-link:hover {
            color: #c2185b;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <main>
            <h2 class="text-center">My Blood Pressure Monitoring Records: <?= htmlspecialchars($patient_full_name) ?></h2>
            <p class="text-muted text-center">Note: To be performed for mothers with indications</p>


            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date/Time</th>
                                <th>Symptoms</th>
                                <th>Blood Pressure</th>
                                <th>Weight (kg)</th>
                                <th>Fetal Heart Rate</th>
                                <th>Urine Protein</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td><?= htmlspecialchars($row['monitoring_datetime']) ?></td>
                                    <td><?= htmlspecialchars($row['symptoms']) ?></td>
                                    <td><?= htmlspecialchars($row['blood_pressure']) ?></td>
                                    <td><?= htmlspecialchars($row['weight']) ?></td>
                                    <td><?= htmlspecialchars($row['fetal_heart_rate']) ?></td>
                                    <td><?= htmlspecialchars($row['urine_protein']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No blood pressure monitoring records found for this patient.</p>
            <?php endif; ?>
            <p class="text-muted">*If the pregnancy duration is ≥ 22 weeks</p>
            <div class="mt-3">
                <a href="patient_health_record.php" class="btn btn-secondary btn-rounded">Back</a>
            </div>
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
