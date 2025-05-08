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
$user_id = $_SESSION['user_id'];

// Fetch current user data
$sql = "SELECT users.*, patients.last_menstrual_date 
        FROM users 
        JOIN patients ON users.id = patients.user_id 
        WHERE users.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $dob = $_POST["date_of_birth"];
    $ic = $_POST["identification_number"];
    $lmp = $_POST["last_menstrual_date"];

    // Update users table
    $updateUser = "UPDATE users SET name=?, email=?, phone=?, address=?, date_of_birth=?, identification_number=? WHERE id=?";
    $stmtUser = $conn->prepare($updateUser);
    $stmtUser->bind_param("ssssssi", $name, $email, $phone, $address, $dob, $ic, $user_id);
    $stmtUser->execute();

    // Update patients table
    $updatePatient = "UPDATE patients SET last_menstrual_date=? WHERE user_id=?";
    $stmtPatient = $conn->prepare($updatePatient);
    $stmtPatient->bind_param("si", $lmp, $user_id);
    $stmtPatient->execute();

    // Redirect after success
    header("Location: patient_dashboard.php?updated=1");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Update Profile</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="text-center">Update Profile</h2>
 <div class="container mt-5">
    <form method="POST" action="patient_update_profile.php">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user_data['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user_data['phone']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user_data['address']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user_data['date_of_birth']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Identification Number</label>
            <input type="text" name="identification_number" value="<?= htmlspecialchars($user_data['identification_number']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Menstrual Period (LMP)</label>
            <input type="date" name="last_menstrual_date" value="<?= htmlspecialchars($user_data['last_menstrual_date']) ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-pink">Update</button>
        <a href="patient_dashboard.php" class="btn btn-secondary btn-rounded">Cancel</a>
    </form>
</div>

 

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
