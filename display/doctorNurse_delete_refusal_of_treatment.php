<?php
session_start();
require_once "../config.php";

// Ensure user is logged in and has appropriate role
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Validate inputs
if (!isset($_GET['form_id']) || !isset($_GET['user_id'])) {
    die("Invalid request.");
}

$form_id = intval($_GET['form_id']);
$user_id = intval($_GET['user_id']);

// Get the file path to delete it from the server
$stmt = $conn->prepare("SELECT file_path FROM treatment_refusal_forms WHERE id = ?");
$stmt->bind_param("i", $form_id);
$stmt->execute();
$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();

// Delete the file from the server
if (!empty($file_path) && file_exists($file_path)) {
    unlink($file_path); // delete file
}

// Delete the record from the database
$stmt = $conn->prepare("DELETE FROM treatment_refusal_forms WHERE id = ?");
$stmt->bind_param("i", $form_id);
$stmt->execute();
$stmt->close();

// Redirect back to the view page
header("Location: doctorNurse_view_refusal_of_treatment.php?user_id=" . $user_id);
exit();
