<?php
session_start();
// Retrieve session variables
$userFirstName = $_SESSION['userFirstName'] ?? 'Guest';
$userRole = $_SESSION['userRole'] ?? 'Guest';
$userStatus = $_SESSION['userStatus'] ?? 0; // Default to '0' if not set
?>

<html>
    <head>
        <meta charset="UTF-8">
      
        <link rel="stylesheet" href="css/mainStyles.css">

        <!-- flaticom CDN Links -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        
<input type="checkbox" id="nav-toggle">
<div class="sidebar">
    <div class="sidebar-brand">
    <h4><span>MaternityMate</span></h4>
    </div>

    <div class="sidebar-menu">
    <ul>
    <?php if ($userStatus == 1): ?>
                    <!-- Patient Role -->
        <li>
            <a href="patient_dashboard.php" class="">
            <i class='bx bxs-dashboard'></i>
            <span class="linksname"> Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        <li>
            <a href="healthRecord.php" class="">
            <i class="fas fa-fw fa-file-medical"></i>
            <span class="linksname">Health Record</span>
            </a>
            <span class="tooltip">Health Record</span>
        </li>
        <li>
            <a href="pregnancyProgress.php" class="">
            <i class="fas fa-fw fa-chart-line"></i>
            <span class="linksname">Pregnancy Progress Tracking</span>
            </a>
            <span class="tooltip">Pregnancy Progress Tracking</span>
        </li>
        
        <li>
            <a href="appointment.php" class="">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span class="linksname">Appointment</span>
            </a>
            <span class="tooltip">Appointment</span>
        </li>
        <li>
            <a href="patient_careCollabRecord.php" class="">
            <i class="fas fa-fw fa-users"></i>
            <span class="linksname">Care Collaboration Record</span>
            </a>
            <span class="tooltip">Care Collaboration Record</span>
        </li>
        <li>
            <a href="report.php" class="">
            <i class="fas fa-fw fa-file-alt"></i>
            <span class="linksname">Report</span>
            </a>
            <span class="tooltip">Report</span>
        </li>

        


        <?php elseif ($userStatus == 2 ): ?>
                    <!-- Doctor  Role -->
            <li>
            <a href="doctor_dashboard.php" class="">
            <i class='bx bxs-dashboard'></i>
            <span class="linksname">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        <li>
            <a href="patient.php" class="">
             <i class="fas fa-fw fa-user-md"></i>
            <span class="linksname">Patient Management</span>
            </a>
            <span class="tooltip">Patient Management</span>
        </li>

        <li>
            <a href="../view/doctor/appointment.php" class="">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span class="linksname">Appointment</span>
            </a>
            <span class="tooltip">Appointment</span>
        </li>
        <li>
            <a href="report.php" class="">
            <i class="fas fa-fw fa-file-alt"></i>
            <span class="linksname">Report</span>
            </a>
            <span class="tooltip">Report</span>
        </li>

        <?php elseif ($userStatus == 3 ): ?>
         <!-- Nurse  Role -->

         <li>
            <a href="nurse_dashboard.php" class="">
            <i class='bx bxs-dashboard'></i>
            <span class="linksname">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        <li>
            <a href="patient.php" class="">
              <i class="fas fa-fw fa-user-md"></i>
            <span class="linksname">Patient Management</span>
            </a>
            <span class="tooltip">Patient Management</span>
        </li>
        <li>
            <a href="appointment.php" class="">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span class="linksname">Appointment</span>
            </a>
            <span class="tooltip">Appointment</span>
        </li>
        <li>
            <a href="report.php" class="">
            <i class="fas fa-fw fa-file-alt"></i>
            <span class="linksname">Report</span>
            </a>
            <span class="tooltip">Report</span>
        </li>

        <?php elseif ($userStatus == 4): ?>
        <!-- Admin Role -->
        <li>
            <a href="admin_dashboard.php" class="">
            <i class='bx bxs-dashboard'></i>
            <span class="linksname">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        <li>
            <a href="doctor.php" class="">
            <i class="fi fi-tr-user-md"></i>
            <span class="linksname">Doctor</span>
            </a>
            <span class="tooltip">Doctor</span>
        </li>
        <li>
            <a href="nurse.php" class="">
            <i class="fi fi-ts-user-nurse-hair-long"></i>
            <span class="linksname">Nurse</span>
            </a>
            <span class="tooltip">Nurse</span>
        </li>
        <li>
            <a href="patient.php" class="">
            <i class="fas fa-fw fa-user-md"></i>
            <span class="linksname">Patient</span>
            </a>
            <span class="tooltip">Patient</span>
        </li>
        <li>
            <a href="report.php" class="">
            <i class="fas fa-fw fa-file-alt"></i>
            <span class="linksname">Report</span>
            </a>
            <span class="tooltip">Report</span>
        </li>
        <?php endif; ?>
    </ul>

    </div>
</div>

<div class="main-content">
    <header>
        <h2>
        
            <label for="nav-toggle">
                <span class="las la-bars"></span>
            </label>

        </h2>
        <div class="search-wrapper">
            <span class="las la-search"></span>
            <input type="search" placeholder="Search here"/>
        </div> 
        
        <div class="user-wrapper">
    <div class="user-info">
        <span><?php echo htmlspecialchars($userFirstName); ?> (<?php echo htmlspecialchars($userRole); ?>)</span>
        <i class='bx bx-chevron-down' id="userDropdownToggle"></i>
    </div>
    
    <div class="dropdown-menu" id="userDropdown">
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>

    
</div>

        
        
    </header>

    
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const mainContent = document.querySelector(".main-content");
    const header = document.querySelector("header");
    const navToggle = document.getElementById("nav-toggle");

    navToggle.addEventListener("click", function () {
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("collapsed");
        header.classList.toggle("collapsed");
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const menuItems = document.querySelectorAll(".sidebar-menu a");

    menuItems.forEach(item => {
        item.addEventListener("click", function() {
            // Remove 'active' class from all items
            menuItems.forEach(link => link.classList.remove("active"));
            
            // Add 'active' class to the clicked item
            this.classList.add("active");

            // Store active link in localStorage so it persists on reload
            localStorage.setItem("activeLink", this.getAttribute("href"));
        });
    });

    // Restore active link after page reload
    const activeLink = localStorage.getItem("activeLink");
    if (activeLink) {
        const matchingLink = document.querySelector(`.sidebar-menu a[href='${activeLink}']`);
        if (matchingLink) {
            matchingLink.classList.add("active");
        }
    }
});

document.getElementById("userDropdownToggle").addEventListener("click", function() {
    document.getElementById("userDropdown").classList.toggle("show");
});

// Close dropdown when clicking outside
document.addEventListener("click", function(event) {
    var dropdown = document.getElementById("userDropdown");
    var toggle = document.getElementById("userDropdownToggle");
    
    if (!toggle.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove("show");
    }
});

document.addEventListener("DOMContentLoaded", function () {
    let userDropdownToggle = document.getElementById("userDropdownToggle");
    if (userDropdownToggle) {
        userDropdownToggle.addEventListener("click", function () {
            document.getElementById("userDropdown").classList.toggle("show");
        });
    }
});

</script>

</body>
</html>