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
   
    <link rel="stylesheet" href="../css/mainStyles.css">
    <style>
         h2 {
            color: #d81b60;
        }
         .table th {
            background-color: #f8bbd0;
            color: #880e4f;
        }
        .btn-link { color: #ec407a; text-decoration: none; }
        .btn-link:hover { color: #d81b60; text-decoration: underline; }
    </style>
    
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
            <td><a href="patient_health_record_detail_patientinfo.php?section=patient_info" class="btn btn-pink w-100">1. Basic Information</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail_patientinfo.php?section=past_pregnancy_history" class="btn btn-pink w-100">2. Past Pregnancy History</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail_patientinfo.php?section=family_health_history" class="btn btn-pink w-100">3.Family Health History</a></td>
        </tr>

        <!-- Consent & Approval -->
        <tr><td rowspan="3">Consent & Approval</td>
            <td><a href="patient_health_record_detail.php?section=blood_collection_consent" class="btn btn-pink w-100">4. Blood Collection Consent</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=cnsent_declaration" class="btn btn-pink w-100">5. Consent Declaration</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=refusal_of_treatment" class="btn btn-pink w-100">6. Refusal of Treatment</a></td>
        </tr>

        <!-- Health Monitoring -->
        <tr><td rowspan="10">Health Monitoring</td>
            <td><a href="patient_health_record_detail.php?section=checklist_ogtt_screening_criteria" class="btn btn-pink w-100">7. Checklist OGTT Criteria</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=ogtt_result" class="btn btn-pink w-100">8. OGTT Result</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=HbA1c_result" class="btn btn-pink w-100">9. HbA1c Result</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=blood_sugar" class="btn btn-pink w-100">10. Blood Sugar Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=blood_pressure" class="btn btn-pink w-100">11. Blood Pressure Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=preeclampsia_monitoring" class="btn btn-pink w-100">12. Pre-Eclampsia Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=weight_monitoring" class="btn btn-pink w-100">13. Weight Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=hemoglobin_monitoring" class="btn btn-pink w-100">14. Hemoglobin Monitoring</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=ultrasound_results" class="btn btn-pink w-100">15. Ultrasound Results</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=current_pregnancy_examination" class="btn btn-pink w-100">16. Current Pregnancy Examination</a></td>
        </tr>

        <!-- Postnatal Care -->
        <tr><td rowspan="3">Postnatal Care</td>
            <td><a href="patient_health_record_detail.php?section=postnatal_home_visits" class="btn btn-pink w-100">17. Postnatal Home Visits</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=postnatal_issues_management" class="btn btn-pink w-100">18. Postnatal Issues & Management</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=thromboprophylaxis_injection_schedule" class="btn btn-pink w-100">19. Thromboprophylaxis Injection</a></td>
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
