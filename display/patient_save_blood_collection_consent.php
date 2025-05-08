<?php
session_start();
require_once "../config.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get form data
$patient_id = $_POST['patient_id'];
$nric = $_POST['nric'];
$blood_group = isset($_POST['blood_group']) ? 1 : 0;
$hemoglobin = isset($_POST['hemoglobin']) ? 1 : 0;
$diabetes = isset($_POST['diabetes']) ? 1 : 0;
$syphilis = isset($_POST['syphilis']) ? 1 : 0;
$hiv = isset($_POST['hiv']) ? 1 : 0;
$hepatitis_b = isset($_POST['hepatitis_b']) ? 1 : 0;
$malaria = isset($_POST['malaria']) ? 1 : 0;
$others = $_POST['others'];
$signature_mother = $_POST['signature_mother'];
$signature_witness = $_POST['signature_witness'];
$name_witness = $_POST['name_witness'];
$nric_witness = $_POST['nric_witness'];
$consent_date = $_POST['consent_date'];

// Prepare insert query
$stmt = $conn->prepare("
    INSERT INTO blood_collection_consent 
    (patient_id, nric, blood_group, hemoglobin, diabetes, syphilis, hiv, hepatitis_b, malaria, others, 
     signature_mother, signature_witness, name_witness, nric_witness, consent_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isiiiiiiiisssss", 
    $patient_id, $nric, $blood_group, $hemoglobin, $diabetes, $syphilis, $hiv, $hepatitis_b, $malaria,
    $others, $signature_mother, $signature_witness, $name_witness, $nric_witness, $consent_date
);

if ($stmt->execute()) {
    // Redirect back with success
    header("Location: patient_health_record_detail_patientinfo.php?section=consent_declaration&success=1");
    exit();
} else {
    // Redirect back with error
    header("Location: patient_health_record_detail_patientinfo.php?section=consent_declaration&error=1");
    exit();
}
?>
