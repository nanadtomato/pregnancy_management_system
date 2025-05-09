<?php
// doctorNurse_edit_basic_info.php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    die("Patient not selected.");
}

$patient_user_id = $_GET['user_id'];

// Fetch patient_id from user_id
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $patient_user_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

if (!$patient_id) {
    die("Patient record not found.");
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $monitoring_datetime = $_POST['monitoring_datetime'];
    $pre_post_breakfast = $_POST['pre_post_breakfast'];
    $pre_post_lunch = $_POST['pre_post_lunch'];
    $pre_post_dinner = $_POST['pre_post_dinner'];
    $pre_bed = $_POST['pre_bed'];
    $notes = $_POST['notes'];

    // Insert the blood sugar monitoring record into the database
    $stmt = $conn->prepare("INSERT INTO blood_sugar_monitoring 
        (patient_id, monitoring_datetime, pre_post_breakfast, pre_post_lunch, pre_post_dinner, pre_bed, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddds", $patient_id, $monitoring_datetime, $pre_post_breakfast, $pre_post_lunch, $pre_post_dinner, $pre_bed, $notes);

    if ($stmt->execute()) {
        $success = "Blood sugar monitoring record added successfully.";
    } else {
        $error = "Database error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Add Blood Sugar Monitoring</title>

<style>
         
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .form-row {
            margin-bottom: 20px;
        }
    </style>

</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4 text-center">Add Blood Sugar Monitoring</h2>

<!-- Success/Error message section -->
<?php if ($success): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-12">
                <label for="monitoring_datetime" class="form-label">Date/Time</label>
                <input type="datetime-local" name="monitoring_datetime" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label for="pre_post_breakfast" class="form-label">Pre/Post Breakfast (mmol/L)</label>
                <input type="number" step="0.01" name="pre_post_breakfast" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label for="pre_post_lunch" class="form-label">Pre/Post Lunch (mmol/L)</label>
                <input type="number" step="0.01" name="pre_post_lunch" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label for="pre_post_dinner" class="form-label">Pre/Post Dinner (mmol/L)</label>
                <input type="number" step="0.01" name="pre_post_dinner" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label for="pre_bed" class="form-label">Pre Bed (mmol/L)</label>
                <input type="number" step="0.01" name="pre_bed" class="form-control" required>
            </div>
        </div>

        <!-- Notes section moved below -->
        <div class="row g-3">
            <div class="col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4"></textarea>
            </div>
        </div>

        <p class="text-muted">Note: For Diabetic Mothers</em></p>
<p class="text-muted">(Target: Pre meal ≤5.3mmol/L, 1H post meal ≤7.8mmol/L, 2H post meal ≤6.7mmol/L)</em></p>
<p class="text-muted">Watch for signs of hypoglycemia and ensure readings do not fall ≤ 4.0mmol/L</em></p>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-pink">Add Blood Sugar Monitoring</button>

            <a href="doctorNurse_view_blood_sugar_monitoring.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary btn-rounded">Back</a>
            
        </div>
        
    </form>
</main>
    
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

