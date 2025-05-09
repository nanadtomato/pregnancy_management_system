<?php
session_start();
require_once "../config.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Check if the user_id and record_id are passed in the URL
if (!isset($_GET['user_id']) || !isset($_GET['record_id'])) {
    die("Required data not provided.");
}

$patient_user_id = $_GET['user_id'];
$record_id = $_GET['record_id'];

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

// Delete the blood sugar monitoring record
$stmt = $conn->prepare("DELETE FROM blood_sugar_monitoring WHERE id = ? AND patient_id = ?");
$stmt->bind_param("ii", $record_id, $patient_id);

if ($stmt->execute()) {
    // Redirect back to the blood sugar monitoring view page
    header("Location: doctorNurse_view_blood_sugar_monitoring.php?user_id=$patient_user_id");
    exit();
} else {
    die("Error deleting record: " . $stmt->error);
}
?>
