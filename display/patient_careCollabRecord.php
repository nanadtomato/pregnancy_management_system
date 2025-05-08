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

<h2 class="text-center mb-5">Welcome, <?= htmlspecialchars($userFirstName) ?>! View Your Care Collaboration Records</h2>

<table class="table table-bordered shadow-sm">
    <thead>
        <tr>
            <th>Section</th>
            <th>Subsection</th>
        </tr>
    </thead>
    <tbody>
        <!-- Examination & Procedure -->
        <tr><td rowspan="3">Examination & Procedure</td>
            <td><a href="patient_carecollab_record_detail_patientinfo.php?section=examination_medicalDentalOfficer_info" class="btn btn-pink w-100">1. Examination by Medical & Dental Officer</a></td>
        </tr>
        <tr>
            <td><a href="patient_carecollab_record_detail_patientinfo.php?section=hosp_admission_record" class="btn btn-pink w-100">2. Hospital Admission Record</a></td>
        </tr>
        <tr>
            <td><a href="patient_carecollab_record_detail_patientinfo.php?section=birth_details" class="btn btn-pink w-100">3. Birth Details</a></td>
        </tr>

        <!-- Risk Assessment & Checkout -->
        <tr><td rowspan="2">Risk Assessment & Checkout</td>
            <td><a href="patient_carecollab_record_detail.php?section=checklist_maternal_management_duringPregnancy" class="btn btn-pink w-100">4. Blood Collection Consent</a></td>
        </tr>
        <tr>
            <td><a href="patient_carecollab_record_detail.php?section=cnsent_declaration" class="btn btn-pink w-100">5. </a></td>
        </tr>
        

        <!-- Postnatal Collaboration -->
        <tr><td rowspan="4">Postnatal Collaboration</td>
            <td><a href="patient_carecollab_record_detail.php?section=checklist_breastfeeding" class="btn btn-pink w-100">6. Breastfeeding Effectiveness Checklist </a></td>
        </tr>
        <tr>
            <td><a href="patient_carecollab_record_detail.php?section=checklist_postnatalCare" class="btn btn-pink w-100">7. Postnatal Care Checklist</a></td>
        </tr>
        
        <tr>
            <td><a href="patient_carecollab_record_detail.php?section=postnatal_checkup" class="btn btn-pink w-100">8. Postnatal Checkup (1 Month)</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=birth_details" class="btn btn-pink w-100">9. Birth Details</a></td>
        </tr>
        

        <!-- Postnatal Care -->
        <tr><td rowspan="2">Health Education and Feedback</td>
            <td><a href="patient_health_record_detail.php?section=maternal_health_education_record" class="btn btn-pink w-100">10. Maternal Health Education Record</a></td>
        </tr>
        <tr>
            <td><a href="patient_health_record_detail.php?section=postnatal_issues_management" class="btn btn-pink w-100">11. </a></td>
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
