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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
<title>Care Collaboration Record</title>   

<style>
    .accordion-button.custom-pink {
        background-color: #f78da7;
        color: white;
    }

    .accordion-button.custom-pink:not(.collapsed) {
        background-color: #f78da7;
        color: black;
    }

    .accordion-item {
        border: 1px solid #f78da7;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .accordion-button::after {
        filter: brightness(0) invert(1); /* Make arrow white */
    }

    .btn-pink {
        background-color: #f78da7;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: normal;
        transition: background-color 0.3s ease;
    }

    .btn-pink:hover {
        background-color: #f55d83;
    }

    
</style>

</head>
 <!-- <?php include('../includes/navbar.php'); ?> -->

 <body>
 <div class="main-content">
 <main>

 
 <div class="container mt-4">
  <h2 class="text-center">Care Collaboration Record</h2>
  <div class="box">
  <ul class="nav nav-tabs" id="careCollabsTabs" role="tablist">
        
  <li class="nav-item" role="presentation">
            <button class="nav-link active" id="examination_procedure-tab" data-bs-toggle="tab" data-bs-target="#examination_procedure" type="button" role="tab">Examination & Procedure</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="risk_assesment_checklist-tab" data-bs-toggle="tab" data-bs-target="#risk_assesment_checklist" type="button" role="tab">Risk Assesment & Checklist</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="postnatal_collab-tab" data-bs-toggle="tab" data-bs-target="#postnatal_collab" type="button" role="tab">Postnatal Collaboration</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="health_education_feedback-tab" data-bs-toggle="tab" data-bs-target="#health_education_feedback" type="button" role="tab">Health Education & Feedback</button>
        </li>
    </ul>
 <div class="container my-4">


    <div class="accordion" id="careCollabAccordion">
        <!-- Section: EXAMINATION BY MEDICAL AND DENTAL OFFICERS -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingExamination_by_medical_dentalofficer">
    
                <button class="accordion-button custom-pink" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExamination_by_medical_dentalofficer">
                    Examination by Medical & Dental Officer
                </button>
            </h2>
            <div id="collapseExamination_by_medical_dentalofficer" class="accordion-collapse collapse show" data-bs-parent="#careCollabAccordion">
                <div class="accordion-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Procedure</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display examination data from DB here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
        </div>

              <!-- Section: Postnatal Collaboration -->
       <div class="tab-pane fade show active" id="mother_info" role="tabpanel">
    <h4></h4>
   <!-- Accordion for Risk Assessment and Checklist -->
   <div class="accordion" id="riskAssessmentChecklistAccordion">
        <!-- Hospital Admission Record Accordion -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button custom-pink" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHospitalAddmissionRecord">
           
                Hospital Admission Record
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#motherInfoAccordion">
                <div class="accordion-body">
                <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Topic</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display education data here -->
                        </tbody>
                    </table>

                    </div>
            </div>
        </div>
        </div>

         <!-- Discharge Note -->
       <div class="tab-pane fade show active" id="mother_info" role="tabpanel">
    <h4></h4>
   <!-- Accordion for Risk Assessment and Checklist -->
   <div class="accordion" id="riskAssessmentChecklistAccordion">
        <!-- Discharge Note Accordion -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button custom-pink" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExamination_by_medical_dentalofficer">

                Discharge Note
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#motherInfoAccordion">
                <div class="accordion-body">
                <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Topic</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display education data here -->
                        </tbody>
                    </table>

                    </div>
            </div>
        </div>
        </div>
                    <form action="patient.php" method="POST">
       

                    
</main>
</div>
 </body>     

</html>
