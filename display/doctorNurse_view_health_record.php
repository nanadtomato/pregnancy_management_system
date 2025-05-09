<?php

session_start();
require_once "../config.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Fetch the patient ID from the URL
$user_id = $_GET['user_id']; // Use user_id instead of patient_id
// Fetch the patient's details using user_id
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if ($patient) {
    // Access patient data
    echo "Patient's name: " . $patient['name'];
} else {
    // Handle the case where no data is found
    echo "No patient data found.";
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
 <div class="container">
    <h2 class="mt-5">Manage Patient Health Record: <?= $patient['name'] ?></h2>

        <!-- Table of Sections and Subsections -->
        <table class="table table-bordered shadow-sm">
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
                        <td><a href="doctorNurse_view_basic_info.php?user_id=<?= $user_id ?>" class="btn btn-link">1. Basic Information</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_past_pregnancyHistory.php?user_id=<?= $user_id ?>" class="btn btn-link">2. Past Pregnancy History</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_family_health_history.php?user_id=<?= $user_id ?>" class="btn btn-link">3. Family Health History</a></td>
                    </tr>
                
                
                <!-- Consent & Approval Section -->
                <tr>
                   <td rowspan="3">Consent & Approval</td>
                        <td><a href="doctorNurse_view_blood_collection_consent.php?user_id=<?= $user_id ?>" class="btn btn-link">4. Blood Collection Consent</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_consent_declaration.php?user_id=<?= $user_id ?>=" class="btn btn-link">5. Consent Declaration</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_refusal_of_treatment.php?user_id=<?= $user_id ?>" class="btn btn-link">6. Refusal of Treatment</a></td>
                    </tr>

                <!-- Health Monitoring Section -->
                <tr>
                <td rowspan="9">Health Monitoring</td>
                        <td><a href="doctorNurse_view_checklist_ogtt_hba1c.php?user_id=<?= $user_id ?>" class="btn btn-link">7. Checklist OGTT Screening Criteria, OGTT Result & HbA1c Result</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_blood_sugar_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">8. Blood Sugar Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_blood_pressure_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">9. Blood Pressure Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_preeclampsia_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">10. Pre-Eclampsia Profile Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_weight_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">11. Weight Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_hemoglobin_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">12. Hemoglobin Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_kicking_monitoring.php?user_id=<?= $user_id ?>" class="btn btn-link">13. Kicking Monitoring</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_ultrasound_results.php?user_id=<?= $user_id ?>" class="btn btn-link">14. Ultrasound Results</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_current_pregnancy_examination.php?user_id=<?= $user_id ?>" class="btn btn-link">15. Current Pregnancy Examination</a></td>
                    </tr>
                    


                <!-- Postnatal Care Section -->
                <tr>
                <td rowspan="3">Postnatal Care</td>
                        <td><a href="doctorNurse_view_postnatal_home_visits.php?user_id=<?= $user_id ?>" class="btn btn-link">16. Postnatal Home Visits</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_postnatal_issues_management.php?user_id=<?= $user_id ?>" class="btn btn-link">17. Postnatal Issues & Management</a></td>
                    </tr>
                    <tr>
                        <td><a href="doctorNurse_view_thromboprophylaxis_injection_schedule.php?user_id=<?= $user_id ?>e" class="btn btn-link">18. Thromboprophylaxis Injection</a></td>
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
