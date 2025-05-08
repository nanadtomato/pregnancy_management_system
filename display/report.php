<?php

session_start();
require_once "../config.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Fetch the patient ID from the URL
$patient_id = $_GET['patient_id'];

// Fetch the patient's details
$query = "SELECT * FROM patients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if ($patient) {
    // Access patient data
    echo "Patient's name: " . $patient['name'];
} else {
    // Handle the case where no data is found
    echo "No patient data found.";
}

if ($patient && isset($patient['name'])) {
    echo "Patient's name: " . $patient['name'];
} else {
    echo "Patient data not found or incomplete.";
}


// Subsection selection logic
$section = $_GET['section'] ?? 'patient_info'; // Default to 'patient_info'
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Report</title>

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
 

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
