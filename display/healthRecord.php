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
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<title>Health Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
    <style> .edit-button {
        color: #fff;
        background-color: #f76c6c;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        font-size: 14px;
    }</style>
    
</head>



<body id="page-top">
 <main>
    <div id="wrapper">
        <!-- Sidebar -->
        <!-- <?php include('../includes/navbar.php'); ?> -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="margin-left: 100px;">
            <!-- Main Content -->
            <div id="content" class="container-fluid">
            <ul class="nav nav-tabs">
 
            <li class="nav-item">
    <a class="nav-link active" href="patient_view_healthRecord.php">View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="patient_manage_health_record.php">Manage</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-danger" href="delete_patient.php">Delete</a>
  </li>
</ul>
     
<div class="container mt-4">
  <h2 class="text-center">Manage Health Records</h2>
  <div class="box">
  <ul class="nav nav-tabs" id="healthTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="mother-tab" data-bs-toggle="tab" data-bs-target="#mother_info" type="button" role="tab">Mother Info</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="consent_approval-tab" data-bs-toggle="tab" data-bs-target="#consent_approval" type="button" role="tab">Consent & Approval</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="health_monitoring-tab" data-bs-toggle="tab" data-bs-target="#health_monitoring" type="button" role="tab">Health Monitoring</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="postnatal_care-tab" data-bs-toggle="tab" data-bs-target="#postnatal_care" type="button" role="tab">Postnatal Care</button>
        </li>
    </ul>

    <div class="tab-content border p-4" id="healthTabsContent">
        <!-- Mother Info -->
        <div class="tab-pane fade show active" id="mother_info" role="tabpanel">
            <!-- Mother's Basic Info -->
             
        <!-- Mother Info -->
<div class="tab-pane fade show active" id="mother_info" role="tabpanel">
    <h4>Mother's Basic Info</h4>
   <!-- Accordion for Mother Info -->
   <div class="accordion" id="motherInfoAccordion">
        <!-- Mother's Basic Info Accordion -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Mother's Basic Info
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#motherInfoAccordion">
                <div class="accordion-body">
                    <form action="patient.php" method="POST">
        <input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>"> <!-- Required for update -->
        
        <div class="mb-3">
            <label for="registration_number" class="form-label">Registration Number</label>
            <input type="text" class="form-control" id="registration_number" name="registration_number">
        </div>

        <div class="mb-3">
            <label for="id_card_number" class="form-label">ID Card Number</label>
            <input type="text" class="form-control" id="id_card_number" name="id_card_number">
        </div>

        <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
        </div>

        <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input type="number" class="form-control" id="age" name="age">
        </div>

        <div class="mb-3">
            <label for="clinic_phone_number" class="form-label">Clinic Phone Number</label>
            <input type="text" class="form-control" id="clinic_phone_number" name="clinic_phone_number">
        </div>

        <div class="mb-3">
            <label for="jkn_serial_number" class="form-label">JKN Serial Number</label>
            <input type="text" class="form-control" id="jkn_serial_number" name="jkn_serial_number">
        </div>

        <div class="mb-3">
            <label for="antenatal_color_code" class="form-label">Antenatal Color Code</label>
            <input type="text" class="form-control" id="antenatal_color_code" name="antenatal_color_code">
        </div>

        <div class="mb-3">
            <label for="ethnic_group" class="form-label">Ethnic Group</label>
            <input type="text" class="form-control" id="ethnic_group" name="ethnic_group">
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">Nationality</label>
            <input type="text" class="form-control" id="nationality" name="nationality">
        </div>

        <div class="mb-3">
            <label for="education_level" class="form-label">Education Level</label>
            <input type="text" class="form-control" id="education_level" name="education_level">
        </div>

        <div class="mb-3">
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" class="form-control" id="occupation" name="occupation">
        </div>

        <div class="mb-3">
            <label for="home_address_1" class="form-label">Home Address Line 1</label>
            <input type="text" class="form-control" id="home_address_1" name="home_address_1">
        </div>

        <div class="mb-3">
            <label for="home_address_2" class="form-label">Home Address Line 2</label>
            <input type="text" class="form-control" id="home_address_2" name="home_address_2">
        </div>

        <div class="mb-3">
            <label for="phone_residential" class="form-label">Residential Phone</label>
            <input type="text" class="form-control" id="phone_residential" name="phone_residential">
        </div>

        <div class="mb-3">
            <label for="phone_mobile" class="form-label">Mobile Phone</label>
            <input type="text" class="form-control" id="phone_mobile" name="phone_mobile">
        </div>

        <div class="mb-3">
            <label for="phone_office" class="form-label">Office Phone</label>
            <input type="text" class="form-control" id="phone_office" name="phone_office">
        </div>

        <div class="mb-3">
            <label for="nurse_ym" class="form-label">Nurse YM</label>
            <input type="text" class="form-control" id="nurse_ym" name="nurse_ym">
        </div>

        <div class="mb-3">
            <label for="workplace_address" class="form-label">Workplace Address</label>
            <input type="text" class="form-control" id="workplace_address" name="workplace_address">
        </div>

        <div class="mb-3">
            <label for="estimated_due_date" class="form-label">Estimated Due Date</label>
            <input type="date" class="form-control" id="estimated_due_date" name="estimated_due_date">
        </div>

        <div class="mb-3">
            <label for="revised_due_date" class="form-label">Revised Due Date</label>
            <input type="date" class="form-control" id="revised_due_date" name="revised_due_date">
        </div>

        <div class="mb-3">
            <label for="gravida" class="form-label">Gravida</label>
            <input type="number" class="form-control" id="gravida" name="gravida">
        </div>

        <div class="mb-3">
            <label for="para" class="form-label">Para</label>
            <input type="number" class="form-control" id="para" name="para">
        </div>

        <div class="mb-3">
            <label for="husband_name" class="form-label">Husband's Name</label>
            <input type="text" class="form-control" id="husband_name" name="husband_name">
        </div>

        <div class="mb-3">
            <label for="husband_occupation" class="form-label">Husband's Occupation</label>
            <input type="text" class="form-control" id="husband_occupation" name="husband_occupation">
        </div>

        <div class="mb-3">
            <label for="husband_workplace_address" class="form-label">Husband's Workplace Address</label>
            <input type="text" class="form-control" id="husband_workplace_address" name="husband_workplace_address">
        </div>

        <div class="mb-3">
            <label for="husband_phone_residential" class="form-label">Husband Residential Phone</label>
            <input type="text" class="form-control" id="husband_phone_residential" name="husband_phone_residential">
        </div>

        <div class="mb-3">
            <label for="husband_phone_mobile" class="form-label">Husband Mobile Phone</label>
            <input type="text" class="form-control" id="husband_phone_mobile" name="husband_phone_mobile">
        </div>

        <div class="mb-3">
            <label for="postnatal_address_1" class="form-label">Postnatal Address</label>
            <input type="text" class="form-control" id="postnatal_address_1" name="postnatal_address_1">
        </div>

        <div class="mb-3">
            <label for="risk_factors" class="form-label">Risk Factors</label>
            <textarea class="form-control" id="risk_factors" name="risk_factors"></textarea>
        </div>

        <button type="submit" name="submit_mother_info" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</div>
</div>


 <!-- Past Pregnancy -->

            <h4>Past Pregnancy History</h4>
            <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Past Pregnancy History
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#motherInfoAccordion">
                <div class="accordion-body">
                    <form action="healthRecord.php" method="POST">
                                            <div class="mb-3">
                                                <label for="year" class="form-label">Year</label>
                                                <input type="number" class="form-control" id="year" name="year">
                                            </div>
                                            <div class="mb-3">
                                                <label for="outcome" class="form-label">Outcome</label>
                                                <input type="text" class="form-control" id="outcome" name="outcome">
                                            </div>
                                            <div class="mb-3">
                                                <label for="delivery_type" class="form-label">Delivery Type</label>
                                                <input type="text" class="form-control" id="delivery_type" name="delivery_type">
                                            </div>
                                            <div class="mb-3">
                                                <label for="place_and_attendant" class="form-label">Place & Attendant</label>
                                                <input type="text" class="form-control" id="place_and_attendant" name="place_and_attendant">
                                            </div>
                                            <div class="mb-3">
                                                <label for="gender" class="form-label">Gender</label>
                                                <input type="text" class="form-control" id="gender" name="gender">
                                            </div>

                                            <div class="mb-3">
                                                <label for="birth_weight" class="form-label">Birth Weight</label>
                                                <input type="decimal" class="form-control" id="birth_weight" name="birth_weight">
                                            </div>

                                            <div class="mb-3">
                                                <label for="breastfeeding_info " class="form-label">Gender</label>
                                                <input type="text" class="form-control" id="breastfeeding_info " name="breastfeeding_info ">
                                            </div>

                                            <button type="submit" name="submit_past_pregnancy" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    </div>
                                  </div>

<h4>Family Health History</h4>
<div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Family Health History
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#motherInfoAccordion">
                <div class="accordion-body">
<h4>Family Health History</h4>
            <form action="healthRecord.php" method="POST">
                                            <div class="mb-3">
                                                <label for="menstruation_days" class="form-label">Menstruation Days</label>
                                                <input type="number" class="form-control" id="menstruation_days" name="menstruation_days">
                                            </div>
                                            <div class="mb-3">
            <label for="menstruation_cycle" class="form-label">Menstruation Cycle</label>
            <input type="text" class="form-control" id="menstruation_cycle" name="menstruation_cycle">
        </div>
        <div class="mb-3">
            <label for="family_planning_method" class="form-label">Family Planning Method</label>
            <input type="text" class="form-control" id="family_planning_method" name="family_planning_method">
        </div>
        <div class="mb-3">
            <label for="family_planning_duration" class="form-label">Family Planning Duration</label>
            <input type="text" class="form-control" id="family_planning_duration" name="family_planning_duration">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="smoking_mother" name="smoking_mother" value="1">
            <label class="form-check-label" for="smoking_mother">Mother Smoking</label>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="smoking_husband" name="smoking_husband" value="1">
            <label class="form-check-label" for="smoking_husband">Husband Smoking</label>
        </div>
        <div class="mb-3">
            <label for="conditions" class="form-label">Medical Conditions</label>
            <textarea class="form-control" id="conditions" name="conditions"></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="tb_screening" name="tb_screening" value="1">
            <label class="form-check-label" for="tb_screening">TB Screening</label>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="cough_more_than_2_weeks" name="cough_more_than_2_weeks" value="1">
            <label class="form-check-label" for="cough_more_than_2_weeks">Cough > 2 Weeks</label>
        </div>
        <div class="mb-3">
            <label for="family_conditions" class="form-label">Family Conditions</label>
            <textarea class="form-control" id="family_conditions" name="family_conditions"></textarea>
        </div>
        <div class="mb-3">
            <label for="immunisation_status" class="form-label">Immunisation Status</label>
            <textarea class="form-control" id="immunisation_status" name="immunisation_status"></textarea>
        </div>
        <button type="submit" name="submit_family_health" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</div>
</div>
                                    <!-- Family Health -->
                                    <div class="tab-pane fade" id="family_health" role="tabpanel">
            
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>
    </>
    <?php include('../includes/scripts.php'); ?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YMRqp7NwXzS1vHiYUgKLDjaAVP2CzXxQK3wn+FHwJTPFu9yDbblAHM7VyXksh8DK" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
