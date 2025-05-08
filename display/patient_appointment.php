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
<title>Appointment</title>
</head>
 

 <body>
 <div class="main-content">
 <main>
  <!-- Section 1: Upcoming Appointment Confirmation -->
 <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Your Upcoming Appointment</div>
    <div class="card-body">
 
       
    </div>
  </div>

  <div class="card mb-4">
  <div class="card-header bg-danger-subtle text-dark">Request New Appointment</div>
    <div class="card-body">
    <form action="request_appointment.php" method="post">
                <div class="mb-3">
                    <label for="preferred_date" class="form-label">Preferred Date</label>
                    <input type="date" class="form-control" name="preferred_date" required>
                </div>
                <div class="mb-3">
                    <label for="preferred_time" class="form-label">Preferred Time</label>
                    <input type="time" class="form-control" name="preferred_time" required>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Request</label>
                    <textarea class="form-control" name="reason" rows="3" placeholder="e.g., not available on scheduled day"></textarea>
                </div>
                <button type="submit" class="btn-pink"">Submit Request</button>
            </form>
        </div>
       
    </div>
  </div>

    </main>
    </div>
<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
