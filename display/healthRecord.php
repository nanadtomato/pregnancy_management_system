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
<div class="main-content">
 
 <main>
    <div id="wrapper">
        <!-- Sidebar -->
        <!-- <?php include('../includes/navbar.php'); ?> -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="margin-left: 210px;">
            <!-- Main Content -->
            <div id="content" class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Health Record</h1>
                <div class="container mt-4">
  <h2 class="text-center">Manage Health Records</h2>
  <ul class="nav nav-tabs" id="recordTabs" role="tablist">
    <!-- Tab 1 -->
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="personal-info-tab" data-bs-toggle="tab" data-bs-target="#personal-info" type="button" role="tab" aria-controls="personal-info" aria-selected="true">Patient Personal Information</button>
    </li>
    <!-- Tab 2 -->
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="consent-tab" data-bs-toggle="tab" data-bs-target="#consent" type="button" role="tab" aria-controls="consent" aria-selected="false">Consent & Approval</button>
    </li>
    <!-- Tab 3 -->
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="health-monitoring-tab" data-bs-toggle="tab" data-bs-target="#health-monitoring" type="button" role="tab" aria-controls="health-monitoring" aria-selected="false">Health Monitoring Record</button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content mt-3" id="recordTabContent">
    <!-- Tab 1 Content -->
    <div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
      <h4>All patient information</h4>
      <div class="accordion" id="personalInfoAccordion">
        <!-- Subsection 1 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="infoHeadingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#infoCollapseOne" aria-expanded="true" aria-controls="infoCollapseOne">
              Patient Information
            </button>
          </h2>
          <div id="infoCollapseOne" class="accordion-collapse collapse show" aria-labelledby="infoHeadingOne">
            <div class="accordion-body">
              <form>
                <!-- Form Content Here -->
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" placeholder="Enter patient's name">
                </div>
                <div class="mb-3">
                  <label for="citizenship" class="form-label">Citizenship</label>
                  <input type="text" class="form-control" id="name" placeholder="Enter patient's citizenship">
                </div>
                <div class="mb-3">
                  <label for="ethnic group" class="form-label">Ethnic Group</label>
                  <input type="text" class="form-control" id="name" placeholder="Enter patient's ethnic group">
                </div>
                <button class="edit-button">Edit </button>
                <!-- Add more fields -->
              </form>
            </div>
          </div>
        </div>
        <!-- Additional Subsections -->
      </div>
    </div>

    <!-- Tab 2 Content -->
    <div class="tab-pane fade" id="consent" role="tabpanel" aria-labelledby="consent-tab">
      <h4>Consent & Approval</h4>
      <!-- Content for Consent & Approval -->
    </div>

    <!-- Tab 3 Content -->
    <div class="tab-pane fade" id="health-monitoring" role="tabpanel" aria-labelledby="health-monitoring-tab">
      <h4>Health Monitoring Record</h4>
      <!-- Content for Health Monitoring -->
    </div>
  </div>
</div>
<div class="row">
  <!-- Card 1 -->
  <div class="col-md-4">
    
  </div>
  <!-- Card 2 -->
  <div class="col-md-4">
    
  </div>
</div>
<div class="modal fade" id="patientInfoModal" tabindex="-1" aria-labelledby="patientInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="patientInfoModalLabel">Patient Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="modalName" class="form-label">Name</label>
            <input type="text" class="form-control" id="modalName">
          </div>
          <!-- Add more fields -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save Changes</button>
      </div>
    </div>
  </div>
</div>
                
            </div>
        </div>
    </div>
    <?php include('../includes/scripts.php'); ?>
    </main>
  </div>
</body>
</html>
