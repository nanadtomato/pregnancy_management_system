<?php
session_start();
require_once "../config.php";

// Only doctors and nurses allowed
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    die("Patient not selected.");
}

$patient_user_id = $_GET['user_id'];
// Get patient_id from user_id
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST['form_type'];

    if ($form_type === "consent_form") {
        $mother_fullname = $_POST['mother_name'];
        $mother_nric = $_POST['mother_nric'];
        $tests = isset($_POST['tests']) ? implode(", ", $_POST['tests']) : "";
        $other_tests = $_POST['other_tests'];
        $mother_signature = $_POST['mother_signature'];
        $witness_name = $_POST['witness_name'];
        $witness_nric = $_POST['witness_nric'];
        $consent_date = $_POST['consent_date'];

        $stmt = $conn->prepare("UPDATE blood_collection_consent SET 
            mother_fullname = ?, mother_nric = ?, tests = ?, other_tests = ?, mother_signature = ?, 
            witness_name = ?, witness_nric = ?, consent_date = ? WHERE patient_id = ?");
        $stmt->bind_param("ssssssssi", $mother_fullname, $mother_nric, $tests, $other_tests, $mother_signature, $witness_name, $witness_nric, $consent_date, $patient_id);

        if ($stmt->execute()) {
            $success = "Consent form updated successfully.";
        } else {
            $error = "Error updating consent form: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($form_type === "screening_form") {
        $screenings = ["Blood Group & Rhesus", "Syphilis", "HIV", "Hepatitis B", "Malaria (BFMP)"];

        foreach ($screenings as $i => $name) {
            $date_collected = $_POST["screen_date_$i"];
            $result = $_POST["screen_result_$i"];
            $recorded_by = $_POST["recorded_by_$i"];
            $date_recorded = $_POST["date_recorded_$i"];

            $stmt = $conn->prepare("SELECT id FROM antenatal_blood_screening_results WHERE patient_id = ? AND condition_name = ?");
            $stmt->bind_param("is", $patient_id, $name);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                $stmt = $conn->prepare("UPDATE antenatal_blood_screening_results 
                    SET date_collected = ?, result = ?, recorded_by = ?, date_recorded = ? 
                    WHERE patient_id = ? AND condition_name = ?");
                $stmt->bind_param("ssssis", $date_collected, $result, $recorded_by, $date_recorded, $patient_id, $name);
            } else {
                $stmt->close();
                $stmt = $conn->prepare("INSERT INTO antenatal_blood_screening_results 
                    (patient_id, condition_name, date_collected, result, recorded_by, date_recorded) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $patient_id, $name, $date_collected, $result, $recorded_by, $date_recorded);
            }

            $stmt->execute();
            $stmt->close();
        }

        $success = "Screening results updated successfully.";
    }
}

// Fetch existing consent data
$consent = [];
$stmt = $conn->prepare("SELECT mother_fullname, mother_nric, tests, other_tests, mother_signature, witness_name, witness_nric, consent_date 
                        FROM blood_collection_consent WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($consent['mother_name'], $consent['mother_nric'], $consent['tests'], $consent['other_tests'], $consent['mother_signature'], $consent['witness_name'], $consent['witness_nric'], $consent['consent_date']);
$stmt->fetch();
$stmt->close();

$selected_tests = isset($consent['tests']) ? explode(", ", $consent['tests']) : [];

// Fetch existing screening results
$screening_data = [];
$stmt = $conn->prepare("SELECT condition_name, date_collected, result, recorded_by, date_recorded 
                        FROM antenatal_blood_screening_results WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $screening_data[$row['condition_name']] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Blood Collection Consent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
</head>
<body>
<div class="main-content">
    <main class="container mt-4">
    <h2 class="text-center mb-4">Edit Blood Collection Consent and Screening</h2>

    <h4>Consent Form</h4>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="form_type" value="consent_form">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Mother’s Full Name</label>
                <input type="text" class="form-control" name="mother_name" value="<?= htmlspecialchars($consent['mother_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">NRIC No.</label>
                <input type="text" class="form-control" name="mother_nric" value="<?= htmlspecialchars($consent['mother_nric'] ?? '') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <p>Consent for the following tests:</p>
            <?php
            $test_options = [
                "Blood Group & Rhesus", "Hemoglobin / Full Blood Count", "Diabetes Screening", "Syphilis", "HIV", "Hepatitis B", "Malaria (BFMP)"
            ];
            
            foreach ($test_options as $test): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tests[]" value="<?= $test ?>" <?= in_array($test, $selected_tests) ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $test ?></label>
                </div>
            <?php endforeach; ?>
            <label class="form-label mt-2">Others (specify):</label>
            <input type="text" class="form-control" name="other_tests" value="<?= htmlspecialchars($consent['other_tests'] ?? '') ?>">
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Mother’s Signature:</label>
                <input type="text" class="form-control" name="mother_signature" value="<?= htmlspecialchars($consent['mother_signature'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Witness Full Name:</label>
                <input type="text" class="form-control" name="witness_name" value="<?= htmlspecialchars($consent['witness_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Witness NRIC:</label>
                <input type="text" class="form-control" name="witness_nric" value="<?= htmlspecialchars($consent['witness_nric'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Consent Date:</label>
                <input type="date" class="form-control" name="consent_date" value="<?= htmlspecialchars($consent['consent_date'] ?? '') ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Consent</button>
    </form>

    <hr class="my-5">

    <form method="post">
        <input type="hidden" name="form_type" value="screening_form">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">

        <h4>Screening Results</h4>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Condition</th>
                    <th>Date Collected</th>
                    <th>Result</th>
                    <th>Recorded By</th>
                    <th>Date Recorded</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $screenings = ["Blood Group & Rhesus", "Syphilis", "HIV", "Hepatitis B", "Malaria (BFMP)"];
                foreach ($screenings as $i => $screen):
                    $data = $screening_data[$screen] ?? ["date_collected" => "", "result" => "", "recorded_by" => "", "date_recorded" => ""];
                    ?>
                    <tr>
                        <td><?= $i + 1 ?>.</td>
                        <td><input type="text" readonly class="form-control-plaintext" name="screen_name_<?= $i ?>" value="<?= $screen ?>"></td>
                        <td><input type="date" class="form-control" name="screen_date_<?= $i ?>" value="<?= $data['date_collected'] ?>"></td>
                        <td><input type="text" class="form-control" name="screen_result_<?= $i ?>" value="<?= $data['result'] ?>"></td>
                        <td><input type="text" class="form-control" name="recorded_by_<?= $i ?>" value="<?= $data['recorded_by'] ?>"></td>
                        <td><input type="date" class="form-control" name="date_recorded_<?= $i ?>" value="<?= $data['date_recorded'] ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success mt-3">Update Screening Results</button>
        <a href="doctorNurse_view_blood_collection_consent.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary mt-3">Cancel</a>
    </form>
    </main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
</body>
</html>
