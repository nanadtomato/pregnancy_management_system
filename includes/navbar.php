<?php

// Retrieve session variables
$userFirstName = $_SESSION['userFirstName'] ?? 'Guest';
$userRole = $_SESSION['userRole'] ?? 'Guest';
$userStatus = $_SESSION['userStatus'] ?? 0; // Default to '0' if not set
?>


<input type="checkbox" id="nav-toggle">
<div class="sidebar">
<div class="sidebar-brand">
        <h2><i class="fas fa-baby"></i><br><span class="brandname">MaternityMate</span></h2>
    </div>


    <div class="sidebar-menu">
        <ul>
            <?php if ($userStatus == 1): ?>
                <!-- Patient -->
                <li><a href="patient_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="patient_health_record.php"><i class="fas fa-file-medical"></i> <span>Health Record</span></a></li>
                <li><a href="patient_pregnancy_progress.php"><i class="fas fa-chart-line"></i> <span>Pregnancy Progress</span></a></li>
                <li><a href="patient_appointment.php"><i class="fas fa-calendar-alt"></i> <span>Appointment</span></a></li>
                <li><a href="patient_careCollabRecord.php"><i class="fas fa-users"></i> <span>Care Collaboration</span></a></li>
                <li><a href="patient_report.php"><i class="fas fa-file-alt"></i> <span>Report</span></a></li>

            <?php elseif ($userStatus == 2): ?>
                <!-- Doctor -->
                <li><a href="doctor_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="doctor_manage_patient.php"><i class="fas fa-user-md"></i> <span>Patient Management</span></a></li>
                <li><a href="doctorNurse_manage_health_record_patient.php"><i class="fas fa-notes-medical"></i> <span>Health Records</span></a></li>
                <li><a href="doctorNurse_manage_carecollaboration_record_patient.php"><i class="fas fa-hand-holding-medical"></i> <span>Care Collaboration</span></a></li>
                <li><a href="doctor_manage_appointment.php"><i class="fas fa-calendar-alt"></i> <span>Appointment</span></a></li>
                <li><a href="report.php"><i class="fas fa-file-alt"></i> <span>Report</span></a></li>

            <?php elseif ($userStatus == 3): ?>
                <!-- Nurse -->
                <li><a href="nurse_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="patient.php"><i class="fas fa-user-md"></i> <span>Patient Management</span></a></li>
                <li><a href="doctorNurse_manage_health_record_patient.php"><i class="fas fa-notes-medical"></i> <span>Health Records</span></a></li>
                <li><a href="doctorNurse_manage_carecollaboration_record_patient.php"><i class="fas fa-hand-holding-medical"></i> <span>Care Collaboration</span></a></li>
                <li><a href="nurse_manage_appointment.php"><i class="fas fa-calendar-alt"></i> <span>Appointment</span></a></li>
                <li><a href="report.php"><i class="fas fa-file-alt"></i> <span>Report</span></a></li>

            <?php elseif ($userStatus == 4): ?>
                <!-- Admin -->
                <li><a href="admin_dashboard.php"><i class="fas fa-gauge"></i> <span>Dashboard</span></a></li>
                <li><a href="admin_create_user.php"><i class="fas fa-user-plus"></i> <span>Create User</span></a></li>
                <li><a href="admin_manage_user.php"><i class="fas fa-user-check"></i> <span>Approve Users</span></a></li>
                <li><a href="admin_manage_doctor.php"><i class="fas fa-user-md"></i> <span>Doctor</span></a></li>
                <li><a href="admin_manage_nurse.php"><i class="fas fa-user-nurse"></i> <span>Nurse</span></a></li>
                <li><a href="admin_manage_patient.php"><i class="fas fa-female"></i> <span>Patient</span></a></li>
                <li><a href="admin_report.php"><i class="fas fa-file-alt"></i> <span>Report</span></a></li>
            <?php endif; ?>

        </ul>
    </div>
</div>

<script>
    const toggle = document.getElementById('nav-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    toggle.addEventListener('change', function() {
        if (this.checked) {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.remove('active');
        }
    });
</script>
<!-- Main Content -->

<header>
    <div class="header-container">
        <!-- Hamburger Menu Icon -->
    
        <label for="nav-toggle" class="sidebarToggle">
            <i class="fas fa-bars"></i>
        </label>

        <!-- Dynamic Page Title -->
        <h2>
            <?php
                // Check the current page and update the header text
                $currentPage = basename($_SERVER['PHP_SELF']);
                $headerText = ' ';  // Default header text

                if ($currentPage == 'patient_dashboard.php') {
                    $headerText = 'Dashboard';
                } elseif ($currentPage == 'patient_health_record.php') {
                    $headerText = 'Health Record';
                } elseif ($currentPage == 'patient_pregnancy_progress.php') {
                    $headerText = 'Pregnancy Progress';
                } elseif ($currentPage == 'patient_appointment.php') {
                    $headerText = 'Appointment';
                } elseif ($currentPage == 'patient_careCollabRecord.php') {
                    $headerText = 'Care Collaboration';
                } elseif ($currentPage == 'patient_report.php') {
                    $headerText = 'Report';
                } elseif ($currentPage == 'doctor_dashboard.php') {
                    $headerText = 'Dashboard';
                } elseif ($currentPage == 'doctor_manage_patient.php') {
                    $headerText = 'Patient Management';
                } elseif ($currentPage == 'doctorNurse_manage_health_record_patient.php') {
                    $headerText = 'Health Records';
                } elseif ($currentPage == 'doctorNurse_manage_carecollaboration_record_patient.php') {
                    $headerText = 'Care Collaboration';
                } elseif ($currentPage == 'doctor_manage_appointment.php') {
                    $headerText = 'Appointment';
                } elseif ($currentPage == 'report.php') {
                    $headerText = 'Report';
                } elseif ($currentPage == 'nurse_dashboard.php') {
                    $headerText = 'Dashboard';
                } elseif ($currentPage == 'patient.php') {
                    $headerText = 'Patient Management';
                } elseif ($currentPage == 'nurse_manage_appointment.php') {
                    $headerText = 'Appointment';
                } elseif ($currentPage == 'admin_dashboard.php') {
                    $headerText = 'Dashboard';
                } elseif ($currentPage == 'admin_create_user.php') {
                    $headerText = 'Create User';
                } elseif ($currentPage == 'admin_manage_user.php') {
                    $headerText = 'Approve Users';
                } elseif ($currentPage == 'admin_manage_doctor.php') {
                    $headerText = 'Doctor';
                } elseif ($currentPage == 'admin_manage_nurse.php') {
                    $headerText = 'Nurse';
                } elseif ($currentPage == 'admin_manage_patient.php') {
                    $headerText = 'Patient';
                } elseif ($currentPage == 'admin_report.php') {
                    $headerText = 'Report';
                }
                echo $headerText;
            ?>
        </h2>

        <!-- Search Bar and User Profile -->
       

            <!-- User Profile -->
            <div class="user-wrapper">
                <div class="user-info">
                    <h5><?php echo htmlspecialchars($userFirstName); ?></h5>
                    <small><?php echo htmlspecialchars($userRole); ?></small>
                </div>
                <img src="img/default-user.png" width="40px" height="40px" alt="User Profile" class="user-img">

                 <!-- Logout Icon -->
    <a href="logout.php" class="logout-icon" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
    </a>

            </div>
        </div>
    </div>
</header>


