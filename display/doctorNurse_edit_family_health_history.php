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
$existing_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define the fields
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

    // Initialize data array
    $data = [];
    foreach ($fields as $field) {
        // Check if the field exists in POST data, else set to null
        $data[$field] = $_POST[$field] ?? null;
    }

    // Dynamically build the SQL query
    $sql = "UPDATE family_health_history SET " . implode(" = ?, ", $fields) . " = ? WHERE patient_id = ?";

   // Prepare the types string dynamically
$types = str_repeat("s", count($fields)) . "i"; // all fields are strings, last one is patient_id (int)
$params = array_merge(array_values($data), [$patient_id]);


    // Prepare and bind statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind the parameters to the statement
    $stmt->bind_param($types, ...$params);

    // Execute the query
    if ($stmt->execute()) {
        $success = "Family health history updated successfully.";
    } else {
        $error = "Database error: " . $stmt->error;
    }

    // Fetch updated data after executing
    $stmt->close();
    $stmt = $conn->prepare("SELECT * FROM family_health_history WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_data = $result->fetch_assoc();
    $stmt->close();
}

// Fetch existing family health history data
if (empty($existing_data)) {
    $stmt = $conn->prepare("SELECT * FROM family_health_history WHERE patient_id = ? LIMIT 1");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_data = $result->fetch_assoc();
    $stmt->close();
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
 <h2 class="mb-4">Edit Family Health History</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <h5>Menstruation & Family Planning</h5>
    <div class="row g-3">
        <div class="col-md-4"><label>Menstruation Days</label><input type="number" name="menstruation_days" class="form-control" value="<?= htmlspecialchars($existing_data['menstruation_days']) ?>" required></div>
        <div class="col-md-4"><label>Menstruation Cycle</label><input type="text" name="menstruation_cycle" class="form-control" value="<?= htmlspecialchars($existing_data['menstruation_cycle']) ?>" required></div>
        <div class="col-md-4">
            <label>Family Planning Practice</label>
            <select name="family_planning_practice" class="form-control" required>
                <option value="1" <?= $existing_data['family_planning_practice'] == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $existing_data['family_planning_practice'] == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        <div class="col-md-4"><label>Family Planning Method</label><input type="text" name="family_planning_method" class="form-control" value="<?= htmlspecialchars($existing_data['family_planning_method']) ?>" required></div>
        <div class="col-md-4"><label>Family Planning Duration</label><input type="text" name="family_planning_duration" class="form-control" value="<?= htmlspecialchars($existing_data['family_planning_duration']) ?>" required></div>
    </div>

    <hr>
    <h5>Smoking History</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="smoking_mother">Smoking by Mother</label>
            <select name="smoking_mother" id="smoking_mother" class="form-control" required>
                <option value="1" <?= $existing_data['smoking_mother'] == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $existing_data['smoking_mother'] == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="smoking_husband">Smoking by Husband</label>
            <select name="smoking_husband" id="smoking_husband" class="form-control" required>
                <option value="1" <?= $existing_data['smoking_husband'] == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $existing_data['smoking_husband'] == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>
    </div>

    <hr>
    <h5>Mother's Medical Conditions</h5>
    <div class="row g-3">
        <?php
        $mother_conditions = [
            'asthma', 'diabetes', 'thalassemia', 'thyroid', 'hypertension',
            'heart_disease', 'allergy', 'tb', 'cancer', 'psychiatric', 'anemia'
        ];
        foreach ($mother_conditions as $cond) {
            $field_name = 'condition_' . $cond;
            $checked = isset($existing_data[$field_name]) && $existing_data[$field_name] == 1 ? 'checked' : '';
            echo '<div class="col-md-3 form-check">
                    <input class="form-check-input" type="checkbox" name="' . $field_name . '" value="1" id="' . $field_name . '" ' . $checked . '>
                    <label class="form-check-label" for="' . $field_name . '">' . ucfirst(str_replace("_", " ", $cond)) . '</label>
                  </div>';
        }
        ?>
    </div>
    <div class="mb-3 mt-2">
        <label for="condition_others">Other Conditions</label>
        <textarea name="condition_others" class="form-control" id="condition_others"><?= htmlspecialchars($existing_data['condition_others'] ?? '') ?></textarea>
    </div>

    <hr>
    <h5>Tibi Screening</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="cough_more_than_2_weeks">Cough for more than 2 weeks?</label>
            <select name="cough_more_than_2_weeks" class="form-control" required>
                <option value="1" <?= $existing_data['cough_more_than_2_weeks'] == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $existing_data['cough_more_than_2_weeks'] == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>
    </div>

    <hr>
    <h5>Family Medical History</h5>
    <div class="row g-3">
        <?php
        $family_conditions = [
            'asthma', 'diabetes', 'anemia', 'hypertension', 'heart_disease',
            'thalassemia', 'allergy', 'tb', 'psychiatric'
        ];
        foreach ($family_conditions as $fcond) {
            $field_name = 'family_' . $fcond;
            $checked = isset($existing_data[$field_name]) && $existing_data[$field_name] == 1 ? 'checked' : '';
            echo '<div class="col-md-3 form-check">
                    <input class="form-check-input" type="checkbox" name="' . $field_name . '" value="1" id="' . $field_name . '" ' . $checked . '>
                    <label class="form-check-label" for="' . $field_name . '">' . ucfirst(str_replace("_", " ", $fcond)) . '</label>
                  </div>';
        }
        ?>
    </div>
    <div class="mb-3 mt-2">
        <label for="family_others">Other Family Conditions</label>
        <textarea name="family_others" class="form-control" id="family_others"><?= htmlspecialchars($existing_data['family_others'] ?? '') ?></textarea>
    </div>

    <hr>
    <h5>Immunisation Records</h5>
    <div class="row g-3">
        <?php
        $doses = ['dose1', 'dose2', 'booster', 'other1', 'other2'];
        foreach ($doses as $dose) {
            $label = ucfirst(str_replace(['dose', 'other'], ['Dose ', 'Other '], $dose));
            echo '<div class="col-md-4">
                    <label>' . $label . ' Date</label>
                    <input type="date" name="immunisation_' . $dose . '_date" class="form-control" value="' . htmlspecialchars($existing_data['immunisation_' . $dose . '_date'] ?? '') . '">
                  </div>
                  <div class="col-md-4">
                    <label>' . $label . ' Batch No</label>
                    <input type="text" name="immunisation_' . $dose . '_batch_no" class="form-control" value="' . htmlspecialchars($existing_data['immunisation_' . $dose . '_batch_no'] ?? '') . '">
                  </div>
                  <div class="col-md-4">
                    <label>' . $label . ' Expiry</label>
                    <input type="date" name="immunisation_' . $dose . '_expiry" class="form-control" value="' . htmlspecialchars($existing_data['immunisation_' . $dose . '_expiry'] ?? '') . '">
                  </div>';
        }
        ?>
    </div>

    <button type="submit" class="btn btn-pink">Update</button>
    <a href="doctorNurse_view_family_health_history.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary btn-rounded">Cancel</a>
</form>
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>