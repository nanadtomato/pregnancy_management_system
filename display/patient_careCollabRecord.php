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

    </div>
</div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
