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
        'menstruation_days', 'menstruation_cycle',
        'family_planning_practice', 'family_planning_method', 'family_planning_duration',
        'smoking_mother', 'smoking_husband',
        'condition_asthma', 'condition_diabetes', 'condition_thalassemia', 'condition_thyroid',
        'condition_hypertension', 'condition_heart_disease', 'condition_allergy', 'condition_tb',
        'condition_cancer', 'condition_psychiatric', 'condition_anemia', 'condition_others',
        'cough_more_than_2_weeks',
        'family_asthma', 'family_diabetes', 'family_anemia', 'family_hypertension',
        'family_heart_disease', 'family_thalassemia', 'family_allergy', 'family_tb',
        'family_psychiatric', 'family_others',
        'immunisation_dose1_date', 'immunisation_dose1_batch_no', 'immunisation_dose1_expiry',
        'immunisation_dose2_date', 'immunisation_dose2_batch_no', 'immunisation_dose2_expiry',
        'immunisation_booster_date', 'immunisation_booster_batch_no', 'immunisation_booster_expiry',
        'immunisation_other1', 'immunisation_other2'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    // Insert data into the database
    $sql = "INSERT INTO family_health_history (
        patient_id, " . implode(",", $fields) . "
    ) VALUES (
        ?, " . str_repeat("?,", count($fields) - 1) . "?
    )";

    $stmt = $conn->prepare($sql);

    $types = "i" . str_repeat("s", count($fields));
    $stmt->bind_param($types, $patient_id, ...array_values($data));

    if ($stmt->execute()) {
        $success = "Family health history added successfully.";
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
<title>Add Family Health History</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Add Family Health History</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <h5>Menstruation & Family Planning</h5>
    <div class="row g-3">
        <div class="col-md-4"><label>Menstruation Days</label><input type="number" name="menstruation_days" class="form-control" required></div>
        <div class="col-md-4"><label>Menstruation Cycle</label><input type="text" name="menstruation_cycle" class="form-control" required></div>
        <div class="col-md-4">
            <label>Family Planning Practice</label>
            <select name="family_planning_practice" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="col-md-4"><label>Family Planning Method</label><input type="text" name="family_planning_method" class="form-control" required></div>
        <div class="col-md-4"><label>Family Planning Duration</label><input type="text" name="family_planning_duration" class="form-control" required></div>
    </div>

    <hr>
<h5>Smoking History</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label for="smoking_mother">Smoking by Mother</label>
        <select name="smoking_mother" id="smoking_mother" class="form-control" required>
            <option value="" disabled selected>Select</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="smoking_husband">Smoking by Husband</label>
        <select name="smoking_husband" id="smoking_husband" class="form-control" required>
            <option value="" disabled selected>Select</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
</div>

    <hr><h5>Mother's Medical Conditions</h5>
<div class="row g-3">
    <?php
    $mother_conditions = [
        'asthma', 'diabetes', 'thalassemia', 'thyroid', 'hypertension',
        'heart_disease', 'allergy', 'tb', 'cancer', 'psychiatric', 'anemia'
    ];
    foreach ($mother_conditions as $cond) {
        echo '<div class="col-md-3 form-check">
                <input class="form-check-input" type="checkbox" name="condition_' . $cond . '" value="1" id="condition_' . $cond . '">
                <label class="form-check-label" for="condition_' . $cond . '">' . ucfirst(str_replace("_", " ", $cond)) . '</label>
              </div>';
    }
    ?>
</div>
<div class="mb-3 mt-2">
    <label for="condition_others">Other Conditions</label>
    <textarea name="condition_others" class="form-control" id="condition_others"></textarea>
</div>

<hr><h5>Tibi Screening</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label for="cough_more_than_2_weeks">Cough for more than 2 weeks?</label>
        <select name="cough_more_than_2_weeks" class="form-control" required>
            <option value="" disabled selected>Select</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
</div>


<hr><h5>Family Medical History</h5>
<div class="row g-3">
    <?php
    $family_conditions = [
        'asthma', 'diabetes', 'anemia', 'hypertension', 'heart_disease',
        'thalassemia', 'allergy', 'tb', 'psychiatric'
    ];
    foreach ($family_conditions as $fcond) {
        echo '<div class="col-md-3 form-check">
                <input class="form-check-input" type="checkbox" name="family_' . $fcond . '" value="1" id="family_' . $fcond . '">
                <label class="form-check-label" for="family_' . $fcond . '">' . ucfirst(str_replace("_", " ", $fcond)) . '</label>
              </div>';
    }
    ?>
</div>
<div class="mb-3 mt-2">
    <label for="family_others">Other Family History</label>
    <textarea name="family_others" class="form-control" id="family_others"></textarea>
</div>


<hr><h5>Immunisation Records</h5>
<table class="table table-bordered">
    <thead>
        <tr class="text-center">
            <th>Tetanus/Toxoid</th>
            <th>Date</th>
            <th>Batch No</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $doses = ['dose1', 'dose2', 'booster', 'other1', 'other2'];
        foreach ($doses as $dose) {
            $label = ucfirst(str_replace(['dose', 'other'], ['Dose ', 'Other '], $dose));
            echo '<tr>
                    <td>' . $label . '</td>
                    <td><input type="date" name="immunisation_' . $dose . '_date" class="form-control"></td>
                    <td><input type="text" name="immunisation_' . $dose . '_batch_no" class="form-control"></td>
                    <td><input type="date" name="immunisation_' . $dose . '_expiry" class="form-control"></td>
                  </tr>';
        }
        ?>
    </tbody>
</table>




    <button type="submit" class="btn btn-pink">Submit</button>
    <a href="doctorNurse_manage_health_record_patient.php" class="btn btn-secondary btn-rounded">Cancel</a>
</form>
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

