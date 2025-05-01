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

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Fetch patient's data
$query_patient = "SELECT * FROM patients WHERE user_id = ?";
$stmt_patient = $conn->prepare($query_patient);
$stmt_patient->bind_param("i", $user_id);
$stmt_patient->execute();
$patient_result = $stmt_patient->get_result();
$patient_data = $patient_result->fetch_assoc();


// Calculate due date and progress
$due_date = null;
$days_left = null;
$pregnancy_progress = null;

if ($patient_data && $patient_data['last_menstrual_date']) {
    $lmp = new DateTime($patient_data['last_menstrual_date']);
    $due_date = clone $lmp;
    $due_date->modify('+280 days');
    $today = new DateTime();
    $days_left = $today->diff($due_date)->days;
    $total_duration = 280;
    $elapsed_days = $total_duration - $days_left;
    $pregnancy_progress = min(100, round(($elapsed_days / $total_duration) * 100));
}



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   
    <link rel="stylesheet" href="../css/mainStyles.css">
</head>
<body>
<div class="main-content">

<main>
<main class="container mt-4">

  <!-- Due Date and Progress -->
  <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Expected Due Date</div>
    <div class="card-body">
      <?php if ($due_date): ?>
        <h5>Due in <?= $days_left ?> days (<?= $due_date->format('jS M Y') ?>)</h5>
        <div class="progress mt-3">
          <div class="progress-bar" role="progressbar" style="width: <?= $pregnancy_progress ?>%;" aria-valuenow="<?= $pregnancy_progress ?>" aria-valuemin="0" aria-valuemax="100">
            <?= $pregnancy_progress ?>%
          </div>
        </div>
      <?php else: ?>
        <p>Please update your Last Menstrual Period (LMP) to see due date and progress.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Profile Info -->
  <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Your Information</div>
  
    <div class="card-body">
      <p><strong>Name:</strong> <?= $user_data['name'] ?></p>
      <p><strong>Email:</strong> <?= $user_data['email'] ?></p>
      <p><strong>Phone:</strong> <?= $user_data['phone'] ?? '-' ?></p>
      <p><strong>Address:</strong> <?= $user_data['address'] ?></p>
      <p><strong>Date of Birth:</strong> <?= $user_data['date_of_birth'] ?></p>
      <p><strong>Identification Number:</strong> <?= $user_data['identification_number'] ?></p>
      <a href="patient_update_profile.php" class="btn btn-outline-primary">Update Profile / LMP</a>
    </div>
  </div>

  <!-- Upcoming Appointments -->
  <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Upcoming Appointments</div>
  <div class="card-body"></div>
  </div>
  </div>

   <!-- Kick Tracker Preview -->
  <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Kick Tracker Summary</div>
  <div class="card-body"></div>
  </div>
  </div>


</main>
</div>


<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
