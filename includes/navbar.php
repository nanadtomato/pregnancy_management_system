<?php
// Retrieve session variables
$userFirstName = $_SESSION['userFirstName'] ?? 'Guest';
$userRole = $_SESSION['userRole'] ?? 'Guest';
$userStatus = $_SESSION['userStatus'] ?? 0; // Default to '0' if not set
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion position-fixed" id="accordionSidebar" style="height: 100%; top: 0;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Pregnancy Management</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- User Info -->
    <div class="sidebar-user-info text-center mt-3">
        <div class="user-names">
            <strong><?php echo htmlspecialchars($userFirstName); ?></strong>
        </div>
        <div class="user-role text-muted">
            <?php echo htmlspecialchars($userRole); ?>
        </div>
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider">


    <?php if ($userStatus == 1): ?>
        <!-- Patient Role -->
        <li class="nav-item">
        <a class="nav-link" href="patient_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
        <li class="nav-item">
            <a class="nav-link" href="healthRecord.php">
                <i class="fas fa-fw fa-file-medical"></i>
                <span>Health Record</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="pregnancyProgress.php">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Pregnancy Progress Tracking</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="appointment.php">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Appointment</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="careCollabRecord.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Care Collaboration Record</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="report.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Report</span>
            </a>
        </li>
    <?php elseif ($userStatus == 2 ): ?>
        <!-- Doctor  Role -->
        <li class="nav-item">
        <a class="nav-link" href="doctor_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
        <li class="nav-item">
            <a class="nav-link" href="patient.php">
                <i class="fas fa-fw fa-user-md
                <span>Patient Management</span>
            </a>
        </li>

    <li class="nav-item">
            <a class="nav-link" href="appointment.php">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Appointment</span>

         <li class="nav-item">
            <a class="nav-link" href="report.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Report</span>
            </a>
        </li>

        <?php elseif ($userStatus == 3 ): ?>
        <!-- Nurse  Role -->
        <li class="nav-item">
        <a class="nav-link" href="nurse_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
        <li class="nav-item">
            <a class="nav-link" href="patient.php">
                <i class="fas fa-fw fa-user-md
                <span>Patient Management</span>
            </a>
        </li>
        
    <li class="nav-item">
            <a class="nav-link" href="appointment.php">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Appointment</span>

         <li class="nav-item">
            <a class="nav-link" href="report.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Report</span>
            </a>
        </li>

    <?php elseif ($userStatus == 4): ?>
        <!-- Admin Role -->
        <li class="nav-item">
            <a class="nav-link" href="doctor.php">
                <i class="fas fa-fw fa-user-md"></i>
                <span>Doctor </span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="nurse.php">
                <i class="fas fa-fw fa-user-md"></i>
                <span>Nurse </span>
                </a>
                </li>
                
                <li class="nav-item">
            <a class="nav-link" href="patient.php">
                <i class="fas fa-fw fa-user-md"></i>
                <span>Patient </span>
            </a>
        </li>
         
        <li class="nav-item">
            <a class="nav-link" href="report.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Report</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->