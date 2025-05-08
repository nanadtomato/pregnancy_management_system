<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id']) || !isset($_GET['history_id'])) {
    die("Required data not provided.");
}

$patient_user_id = $_GET['user_id'];
$history_id = $_GET['history_id'];

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

// Fetch existing history record
$stmt = $conn->prepare("SELECT * FROM past_pregnancy_history WHERE id = ? AND patient_id = ?");
$stmt->bind_param("ii", $history_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_assoc();
$stmt->close();

if (!$history) {
    die("Past pregnancy record not found.");
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

    $sql = "UPDATE past_pregnancy_history SET
        year = ?, marriage_date = ?, outcome = ?, delivery_type = ?, place_and_attendant = ?,
        gender = ?, birth_weight = ?, complications_mother = ?, complications_child = ?,
        breastfeeding_info = ?, current_condition = ?
        WHERE id = ? AND patient_id = ?";

    $stmt = $conn->prepare($sql);
    $types = "ssssssssssssi";
    $stmt->bind_param($types, 
        $data['year'], $data['marriage_date'], $data['outcome'], $data['delivery_type'], $data['place_and_attendant'],
        $data['gender'], $data['birth_weight'], $data['complications_mother'], $data['complications_child'],
        $data['breastfeeding_info'], $data['current_condition'], $history_id, $patient_id
    );

    if ($stmt->execute()) {
        $success = "Past pregnancy history updated successfully.";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM past_pregnancy_history WHERE id = ?");
        $stmt->bind_param("i", $history_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Database error: " . $stmt->error;
    }


}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Add basic info</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Edit Mother's Pregnancy History</h2>
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
                <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($history['year']) ?>">
            </div>
            <div class="col-md-3">
                <label>Marriage Date</label>
                <input type="date" name="marriage_date" class="form-control" value="<?= htmlspecialchars($history['marriage_date']) ?>">
            </div>
            <div class="col-md-6">
                <label>Outcome</label>
                <input type="text" name="outcome" class="form-control" value="<?= htmlspecialchars($history['outcome']) ?>">
            </div>
            <div class="col-md-6">
                <label>Delivery Type</label>
                <input type="text" name="delivery_type" class="form-control" value="<?= htmlspecialchars($history['delivery_type']) ?>">
            </div>
            <div class="col-md-6">
                <label>Place and Attendant</label>
                <textarea name="place_and_attendant" class="form-control"><?= htmlspecialchars($history['place_and_attendant']) ?></textarea>
            </div>
            <div class="col-md-3">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">--Select--</option>
                    <option value="Male" <?= $history['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                    <option value="Female" <?= $history['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Birth Weight (kg)</label>
                <input type="number" step="0.01" name="birth_weight" class="form-control" value="<?= htmlspecialchars($history['birth_weight']) ?>">
            </div>
            <div class="col-md-6">
                <label>Complications (Mother)</label>
                <textarea name="complications_mother" class="form-control"><?= htmlspecialchars($history['complications_mother']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label>Complications (Child)</label>
                <textarea name="complications_child" class="form-control"><?= htmlspecialchars($history['complications_child']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label>Breastfeeding Info</label>
                <textarea name="breastfeeding_info" class="form-control"><?= htmlspecialchars($history['breastfeeding_info']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label>Current Condition</label>
                <textarea name="current_condition" class="form-control"><?= htmlspecialchars($history['current_condition']) ?></textarea>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update History</button>
            <a href="doctorNurse_view_past_pregnancyHistory.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary">Back</a>
        </div>
    </form>
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>