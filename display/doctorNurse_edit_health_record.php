<?php
// view_patient_record.php
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
<link rel="stylesheet" href="../css/mainStyles.css">
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
 <div class="container">
        <h2 class="mt-5">Manage Patient Health Record: <?= $patient['name'] ?></h2>

        <!-- Table of Sections and Subsections -->
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Subsection</th>
                </tr>
            </thead>
            <tbody>
                <!-- Patient Information Section -->
                <tr>
                    <td rowspan="3"> Patient Information</td>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=basic_info" class="btn btn-link">1. Basic Information</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=past_pregnancy_history" class="btn btn-link">2. Past Pregnancy History</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=family_health_history" class="btn btn-link">3.Family Health History</a></td>
                </tr>
                
                
                <!-- Consent & Approval Section -->
                <tr>
                    <td rowspan="3">Consent & Approval</td>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=blood_collection_consent" class="btn btn-link">4.Blood Collection Consent</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=consent_declaration" class="btn btn-link">5. Consent Declaration</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=refusal_of_treatment" class="btn btn-link">6.Refusal of Treatment</a></td>
                </tr>

                <!-- Health Monitoring Section -->
                <tr>
                    <td rowspan="10">Health Monitoring</td>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=checklist_ogtt_screening_criteria" class="btn btn-link">7.Checklist OGTT Screening Criteria</a></td>
                </tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=ogtt_result" class="btn btn-link">8.OGTT Result</a></td>
                </tr>
                </tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=HbA1c_result" class="btn btn-link">9.HbA1c Result</a></td>
                </tr>
                </tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=blood_sugar" class="btn btn-link">10.Blood Sugar Monitoring</a></td>
                </tr>
                </tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=blood_pressure" class="btn btn-link">11.Blood Pressure Monitoring</a></td>
                </tr>
                </tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=preeclampsia_monitoring" class="btn btn-link">12.Pre-Eclampsia Profile Monitoring</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_recordd.php?patient_id=<?= $patient_id ?>&section=weight_monitoring" class="btn btn-link">13.Weight Monitoring</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=hemoglobin_monitoring" class="btn btn-link">14.Hemoglobin Monitoring</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=ultrasound_results" class="btn btn-link">15.Ultrasound Results</a></td>
                </tr>
                <tr>
                    <td><a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=current_pregnancy_examination" class="btn btn-link">16.Current Pregnancy Examination</a></td>
                </tr>

                <!-- Postnatal Care Section -->
                <tr>
                    <td rowspan="3">Postnatal Care</td>
                    <td><a href="edit_patient_record.php?patient_id=<?= $patient_id ?>&section=postnatal_home_visits" class="btn btn-link">17.Postnatal Home Visits</a></td>
                </tr>
                <tr>
                    <td><a href="edit_patient_record.php?patient_id=<?= $patient_id ?>&section=postnatal_issues_management" class="btn btn-link">18.Postnatal Issues & Management</a></td>
                </tr>
                <tr>
                    <td><a href="edit_patient_record.php?patient_id=<?= $patient_id ?>&section=thromboprophylaxis_injection_schedule" class="btn btn-link">19.Thromboprophylaxis Injection</a></td>
                </tr>
            </tbody>
        </table>
    </div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
