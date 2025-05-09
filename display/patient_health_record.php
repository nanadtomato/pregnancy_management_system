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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Health Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">

    
    
</head>

<body>
 <div class="main-content">
 <main>
<!-- Inside <main> -->

<h2 class="text-center mb-5">Welcome, <?= htmlspecialchars($userFirstName) ?>! View Your Health Records</h2>

<table class="table table-bordered shadow-sm">
    <thead>
        <tr>
            <th>Section</th>
            <th>Subsection</th>
        </tr>
    </thead>
    <tbody>
        <!-- Patient Information -->
        <tr><td rowspan="3">Patient Information</td>
            <td><a href="patient_view_basic_info.php" class="btn btn-pink w-100">1. Basic Information</a></td>
           
        </tr>
        <tr>
            <td><a href="patient_view_past_pregnancyHistory.php" class="btn btn-pink w-100">2. Past Pregnancy History</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_family_health_history.php" class="btn btn-pink w-100">3.Family Health History</a></td>
        </tr>

        <!-- Consent & Approval -->
        <tr><td rowspan="3">Consent & Approval</td>
            <td><a href="patient_view_blood_collection_consent.php" class="btn btn-pink w-100">4. Blood Collection Consent</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_consent_declaration.php" class="btn btn-pink w-100">5. Consent Declaration</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_refusal_of_treatment.php" class="btn btn-pink w-100">6. Refusal of Treatment</a></td>
        </tr>

        <!-- Health Monitoring -->
        <tr><td rowspan="9">Health Monitoring</td>
            <td><a href="patient_view_checklist_ogtt_hba1c.php" class="btn btn-pink w-100">7. Checklist OGTT Screening Criteria, OGTT Result & HbA1c Result </a></td>
        </tr>
        <tr>
            <td><a href="patient_view_blood_sugar_monitoring.php" class="btn btn-pink w-100">8. Blood Sugar Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_blood_pressure_monitoring.php" class="btn btn-pink w-100">9. Blood Pressure Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_preeclampsia_monitoring.php?" class="btn btn-pink w-100">10. Pre-Eclampsia Profile Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_weight_monitoring.php?" class="btn btn-pink w-100">11. Weight Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_hemoglobin_monitoring.php?" class="btn btn-pink w-100">12. Hemoglobin Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_kicking_monitoring.php?" class="btn btn-pink w-100">13. Kicking Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_ultrasound_results.php?" class="btn btn-pink w-100">14. Ultrasound Results</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_view_current_pregnancy_examination.php?" class="btn btn-pink w-100">15. Current Pregnancy Examination</a></td>
        </tr>
       

        <!-- Postnatal Care -->
        <tr><td rowspan="3">Postnatal Care</td>
            <td><a href="patient_view_postnatal_home_visits.php?" class="btn btn-pink w-100">16. Postnatal Home Visits</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail_patientinfo.php?section=postnatal_issues_management" class="btn btn-pink w-100">17. Postnatal Issues & Management</a></td>
        </tr>
        <tr>
            <td><a href="patient_view_thromboprophylaxis_injection_schedule.php?" class="btn btn-pink w-100">18. Thromboprophylaxis Injection</a></td>
        </tr>
    </tbody>
</table>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
