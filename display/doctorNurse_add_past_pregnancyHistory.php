<?php
// doctorNurse_edit_basic_info.php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    die("Patient not selected.");
}

$patient_user_id = $_GET['user_id'];

// Fetch patient_id from user_id
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $patient_user_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

if (!$patient_id) {
    die("Patient record not found.");
}

$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [
        'year', 'marriage_date', 'outcome', 'delivery_type',
        'place_and_attendant', 'gender', 'birth_weight',
        'complications_mother', 'complications_child',
        'breastfeeding_info', 'current_condition'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    $sql = "INSERT INTO past_pregnancy_history (
        patient_id, " . implode(",", $fields) . "
    ) VALUES (
        ?, " . str_repeat("?,", count($fields) - 1) . "?
    )";

    $stmt = $conn->prepare($sql);
    $types = "iissssssssss"; // adjust types according to actual data type
    $stmt->bind_param($types, $patient_id, ...array_values($data));

    if ($stmt->execute()) {
        $success = "Past pregnancy history added successfully.";
    } else {
        $error = "Database error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Add Past Pregnancy History</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Add Mother's Pregnancy History</h2>
 <h5 class="mb-4"> Past Pregnancy History</h5>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="row g-3">
        <div class="col-md-3">
            <label>Year</label>
            <input type="number" name="year" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Marriage Date</label>
            <input type="date" name="marriage_date" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Outcome</label>
            <input type="text" name="outcome" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Delivery Type</label>
            <input type="text" name="delivery_type" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Place and Attendant</label>
            <textarea name="place_and_attendant" class="form-control"></textarea>
        </div>
        <div class="col-md-3">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option value="">--Select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Birth Weight (kg)</label>
            <input type="number" step="0.01" name="birth_weight" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Complications (Mother)</label>
            <textarea name="complications_mother" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label>Complications (Child)</label>
            <textarea name="complications_child" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label>Breastfeeding Info</label>
            <textarea name="breastfeeding_info" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label>Current Condition</label>
            <textarea name="current_condition" class="form-control"></textarea>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-pink">Add History</button>
        <a href="doctorNurse_view_past_pregnancyHistory.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary">Back</a>
    </div>
</form>
</main>
    
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

