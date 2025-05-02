<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

$record_id = $_GET['record_id'];
$section = $_GET['section'];
$patient_id = $_GET['patient_id'];

switch ($section) {
    case 'past_pregnancy_history':
        $query = "DELETE FROM past_pregnancy_history WHERE id = ?";
        break;
    case 'family_health_history':
        $query = "DELETE FROM family_health_history WHERE id = ?";
        break;
    case 'basic_info':
        $query = "DELETE FROM mother_information WHERE id = ?";
        break;
    default:
        die("Invalid section");
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $record_id);
$stmt->execute();

// Redirect back
header("Location: view_subsection.php?patient_id=$patient_id&section=$section");
exit();
?>
