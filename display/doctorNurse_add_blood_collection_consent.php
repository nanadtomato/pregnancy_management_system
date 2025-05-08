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
    $form_type = $_POST['form_type'];

    if ($form_type === "consent_form") {
        // Consent Form Handling
        $mother_fullname = $_POST['mother_name']; // This is where the variable should be correctly assigned
        $mother_nric = $_POST['mother_nric'];
        $tests = isset($_POST['tests']) ? implode(", ", $_POST['tests']) : "";
        $other_tests = $_POST['other_tests'];
        $mother_signature = $_POST['mother_signature'];
        $witness_name = $_POST['witness_name'];
        $witness_nric = $_POST['witness_nric'];
        $consent_date = $_POST['consent_date'];

        // Corrected the bind_param to use $mother_fullname
        $stmt = $conn->prepare("INSERT INTO blood_collection_consent 
            (patient_id, mother_fullname, mother_nric, tests, other_tests, mother_signature, witness_name, witness_nric, consent_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", $patient_id, $mother_fullname, $mother_nric, $tests, $other_tests, $mother_signature, $witness_name, $witness_nric, $consent_date);

        if ($stmt->execute()) {
            $success = "Consent form submitted successfully.";
        } else {
            $error = "Error submitting form: " . $stmt->error;
        }
        $stmt->close();
    }

    elseif ($form_type === "screening_form") {
        // Screening Form Handling
        $screenings = ["Blood Group & Rhesus", "Syphilis", "HIV", "Hepatitis B", "Malaria (BFMP)"];

        foreach ($screenings as $i => $name) {
            $date_collected = $_POST["screen_date_$i"];
            $result = $_POST["screen_result_$i"];
            $recorded_by = $_POST["recorded_by_$i"];
            $date_recorded = $_POST["date_recorded_$i"];

            if (!empty($date_collected) && !empty($result)) {
                $stmt = $conn->prepare("INSERT INTO antenatal_blood_screening_results (patient_id, condition_name, date_collected, result, recorded_by, date_recorded) VALUES (?, ?, ?, ?, ?, ?)");

                $stmt->bind_param("isssss", $patient_id, $name, $date_collected, $result, $recorded_by, $date_recorded);
                $stmt->execute();
                $stmt->close();
            }
        }

        $success = "Screening results submitted successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Add Blood Collection Consent</title>

</head>


 <body>
 <div class="main-content">
 <main>
 

 <h2 class="text-center mb-4">Declaration of Blood Collection for Antenatal Screening</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="mb-4">
    <h5 class="section-title">Purpose of Blood Screening for Pregnant Mothers:</h5>
    <ul>
        <li>To ensure the mother is in optimal health</li>
        <li>To detect diseases and allow early treatment</li>
        <li>To prevent infection and complications to the baby</li>
        <li>To detect infections early and manage the partner</li>
    </ul>
</div>

<form method="post">
<input type="hidden" name="form_type" value="consent_form">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Mother’s Full Name</label>
            <input type="text" class="form-control" name="mother_name" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">NRIC No.</label>
            <input type="text" class="form-control" name="mother_nric" required>
        </div>
    </div>

    <div class="mb-3">
        <p>I understand the explanation given orally and hereby give consent for my blood to be taken for the following tests:</p>
        <?php
        $test_options = [
            "Blood Group & Rhesus", "Hemoglobin / Full Blood Count",
            "Diabetes Screening", "Syphilis", "HIV", "Hepatitis B", "Malaria"
        ];
        foreach ($test_options as $test): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tests[]" value="<?= $test ?>">
                <label class="form-check-label"><?= $test ?></label>
            </div>
        <?php endforeach; ?>
        <div class="mt-2">
            <label class="form-label">Others (specify):</label>
            <input type="text" class="form-control" name="other_tests">
        </div>
    </div>

    <div class="mb-3">
        <p><strong>Further blood samples may be taken for confirmation if necessary.</strong></p>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Mother’s Full Name (as consent):</label>
            <input type="text" class="form-control" name="mother_signature" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Witness Full Name:</label>
            <input type="text" class="form-control" name="witness_name" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Witness NRIC No.:</label>
            <input type="text" class="form-control" name="witness_nric" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date:</label>
            <input type="date" class="form-control" name="consent_date" required>
        </div>
    </div>

    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="agree" required>
        <label class="form-check-label">
            I declare that the above information is true and consent is given.
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Submit Consent</button>
</form>

<hr class="my-5">

<div class="mb-4">
    <h5 class="section-title">Note:</h5>
    <p>If the mother is a minor under 18 years, obtain consent from a husband aged ≥18. If husband is &lt;18, obtain consent from parent/guardian...</p>
</div>

<div class="mb-4">
    <h5 class="section-title">Reminder I:</h5>
    <p>The mother must inform healthcare staff if any of these risk factors exist:</p>
    <ul>
        <li>a. Drug use</li>
        <li>b. Sexual relationship with another partner</li>
        <li>c. Partner is a drug user</li>
        <li>d. Partner has sex with another person</li>
        <li>e. Partner is confirmed HIV / Syphilis positive</li>
    </ul>
    <p>If one or more risk factors apply:</p>
    <ul>
        <li>(i) HIV screening non-reactive → repeat test</li>
        <li>(ii) Syphilis screening → repeat at 28–32 weeks</li>
    </ul>
</div>

<div class="mb-4">
    <h5 class="section-title">Reminder II: Malaria Screening</h5>
    <p>Suspect malaria if symptoms cycle through cold – hot – sweat stages (6–10 hrs), repeating without treatment.</p>
    <p>High-risk groups:</p>
    <ul>
        <li>Indigenous people (Orang Asli)</li>
        <li>Forest dwellers</li>
        <li>Endemic area residents</li>
        <li>Loggers, farmers, etc.</li>
        <li>Recent malaria contact</li>
    </ul>
</div>

<hr class="my-5">

<form method="post">
    <input type="hidden" name="form_type" value="screening_form">
    <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Condition/Status Screened</th>
                <th>Date Collected</th>
                <th>Result</th>
                <th>Recorded By</th>
                <th>Date Recorded</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $screenings = ["Blood Group & Rhesus", "Syphilis", "HIV", "Hepatitis B", "Malaria (BFMP)"];
            foreach ($screenings as $i => $screen): ?>
                <tr>
                    <td><?= $i + 1 ?>.</td>
                    <td><input type="text" readonly class="form-control-plaintext" name="screen_name_<?= $i ?>" value="<?= $screen ?>"></td>
                    <td><input type="date" class="form-control" name="screen_date_<?= $i ?>"></td>
                    <td><input type="text" class="form-control" name="screen_result_<?= $i ?>"></td>
                    <td><input type="text" class="form-control" name="recorded_by_<?= $i ?>"></td>
                    <td><input type="date" class="form-control" name="date_recorded_<?= $i ?>"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" class="btn btn-success mt-3">Submit Screening Results</button>
</form>


   
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

