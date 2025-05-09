<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id']) || !isset($_GET['record_id'])) {
    die("Required data not provided.");
}

$patient_user_id = $_GET['user_id'];
$record_id = $_GET['record_id'];

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

// Fetch existing blood pressure monitoring record
$stmt = $conn->prepare("SELECT * FROM blood_pressure_monitoring WHERE id = ? AND patient_id = ?");
$stmt->bind_param("ii", $record_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();

if (!$record) {
    die("Blood pressure monitoring record not found.");
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $monitoring_datetime = $_POST['monitoring_datetime'];
    $symptoms = $_POST['symptoms'];
    $blood_pressure = $_POST['blood_pressure'];
    $weight = $_POST['weight'];
    $fetal_heart_rate = $_POST['fetal_heart_rate'];
    $urine_protein = $_POST['urine_protein'];

    // Update the blood pressure monitoring record in the database
    $stmt = $conn->prepare("UPDATE blood_pressure_monitoring SET 
        monitoring_datetime = ?, symptoms = ?, blood_pressure = ?, weight = ?, fetal_heart_rate = ?, urine_protein = ? 
        WHERE id = ? AND patient_id = ?");
    
    $stmt->bind_param("ssssssii", $monitoring_datetime, $symptoms, $blood_pressure, $weight, $fetal_heart_rate, $urine_protein, $record_id, $patient_id);

    if ($stmt->execute()) {
        $success = "Blood pressure monitoring record updated successfully.";
    } else {
        $error = "Database error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>Edit Blood Pressure Monitoring</title>

    <style>
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .form-row {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="main-content">
<main>
    <h2 class="mb-4 text-center">Edit Blood Pressure Monitoring</h2>

    <!-- Success/Error message section -->
    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="monitoring_datetime" class="form-label">Date/Time</label>
                    <input type="datetime-local" name="monitoring_datetime" class="form-control" value="<?= htmlspecialchars($record['monitoring_datetime']) ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="symptoms" class="form-label">Symptoms</label>
                    <input type="text" name="symptoms" class="form-control" value="<?= htmlspecialchars($record['symptoms']) ?>">
                </div>
                <div class="col-md-12">
                    <label for="blood_pressure" class="form-label">Blood Pressure</label>
                    <input type="text" name="blood_pressure" class="form-control" value="<?= htmlspecialchars($record['blood_pressure']) ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" value="<?= htmlspecialchars($record['weight']) ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="fetal_heart_rate" class="form-label">Fetal Heart Rate</label>
                    <input type="text" name="fetal_heart_rate" class="form-control" value="<?= htmlspecialchars($record['fetal_heart_rate']) ?>">
                </div>
                <div class="col-md-12">
                    <label for="urine_protein" class="form-label">Urine Protein</label>
                    <input type="text" name="urine_protein" class="form-control" value="<?= htmlspecialchars($record['urine_protein']) ?>">
                </div>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-pink">Update Blood Pressure Monitoring</button>
                <a href="doctorNurse_view_blood_pressure_monitoring.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary btn-rounded">Back</a>
            </div>
        </form>
    </div>
</main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
