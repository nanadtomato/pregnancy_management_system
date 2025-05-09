<?php
session_start();
require_once "../config.php";

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

// Fetch existing OGTT and HbA1c test results and criteria
$stmt = $conn->prepare("SELECT * FROM ogtt_screening_criteria WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$criteria_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM ogtt_test_results WHERE patient_id = ? ORDER BY test_date DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$ogtt_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an array
$stmt->close();

// Fetch all HbA1c test results
$stmt = $conn->prepare("SELECT * FROM hba1c_test_results WHERE patient_id = ? ORDER BY test_date DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$hba1c_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an array
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update OGTT screening criteria
    $fields = [
        'bmi_over_27', 'history_of_gdm', 'family_history_diabetes', 'macrosomic_baby',
        'bad_obstetric_history', 'glycosuria', 'current_obstetric_problems', 'age_over_25'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? 0;
    }

    $sql = "UPDATE ogtt_screening_criteria SET " . implode(" = ?, ", $fields) . " = ? WHERE patient_id = ?";
    $types = str_repeat("i", count($fields)) . "i";
    $params = array_merge(array_values($data), [$patient_id]);

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $success = "OGTT Screening Criteria updated successfully.";
    } else {
        $error = "Error updating OGTT Screening Criteria: " . $stmt->error;
    }

    $stmt->close();

    // Insert or update OGTT test results
    if (isset($_POST['ogtt_date'])) {
        foreach ($_POST['ogtt_date'] as $index => $date) {
            // Check if the row already exists
            if (!empty($_POST['ogtt_id'][$index])) {
                // Update existing OGTT result
                $stmt = $conn->prepare("UPDATE ogtt_test_results SET test_date = ?, pog = ?, fasting_blood_sugar = ?, two_hour_postprandial = ? WHERE id = ?");
                $stmt->bind_param("ssdds", $date, $_POST['ogtt_pog'][$index], $_POST['fasting'][$index], $_POST['two_hour'][$index], $_POST['ogtt_id'][$index]);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new OGTT result
                $stmt = $conn->prepare("INSERT INTO ogtt_test_results (patient_id, test_date, pog, fasting_blood_sugar, two_hour_postprandial) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issdd", $patient_id, $date, $_POST['ogtt_pog'][$index], $_POST['fasting'][$index], $_POST['two_hour'][$index]);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Insert or update HbA1c test results
    if (isset($_POST['hba1c_date'])) {
        foreach ($_POST['hba1c_date'] as $index => $date) {
            // Check if the row already exists
            if (!empty($_POST['hba1c_id'][$index])) {
                // Update existing HbA1c result
                $stmt = $conn->prepare("UPDATE hba1c_test_results SET test_date = ?, pog = ?, hba1c = ? WHERE id = ?");
                $stmt->bind_param("ssds", $date, $_POST['hba1c_pog'][$index], $_POST['hba1c'][$index], $_POST['hba1c_id'][$index]);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new HbA1c result
                $stmt = $conn->prepare("INSERT INTO hba1c_test_results (patient_id, test_date, pog, hba1c) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issd", $patient_id, $date, $_POST['hba1c_pog'][$index], $_POST['hba1c'][$index]);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Display success or error messages
    if ($success) {
        echo "<div class='alert alert-success'>$success</div>";
    } elseif ($error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>Edit OGTT & HbA1c Results</title>
</head>
<body>
<div class="main-content">
<main>
<h2>Edit OGTT Screening & Test Results</h2>

<!-- Success/Error message section -->
<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <h4 class="mt-4">OGTT Screening Criteria</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Criteria / Risk</th>
                <th>Tick (√)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $criteria = [
                "bmi_over_27" => "BMI >27 kg/m²",
                "history_of_gdm" => "History of Gestational Diabetes Mellitus (GDM)",
                "family_history_diabetes" => "Family history of diabetes",
                "macrosomic_baby" => "History of delivering a macrosomic baby (≥4kg)",
                "bad_obstetric_history" => "Bad Obstetric History: IUD/Stillbirth, congenital abnormalities, shoulder dystocia",
                "glycosuria" => "Glycosuria ≥ 2 times",
                "current_obstetric_problems" => "Current obstetric problems (Hypertension, Polyhydramnios, Corticosteroids)",
                "age_over_25" => "Age ≥25 years"
            ];
            $i = 1;
            foreach ($criteria as $key => $label) {
                $checked = isset($criteria_result[$key]) && $criteria_result[$key] ? 'checked' : '';
                echo "<tr>
                        <td>$i</td>
                        <td>$label</td>
                        <td><input type='checkbox' name='$key' value='1' $checked></td>
                    </tr>";
                $i++;
            }
            ?>
        </tbody>
    </table>

    <h4 class="mt-5">OGTT Test Results</h4>
    <table class="table table-bordered" id="ogtt-table">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>POG</th>
                <th>Fasting Blood Sugar (mmol/L)</th>
                <th>2H Postprandial (mmol/L)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($ogtt_results as $ogtt) {
                echo "<tr>
                        <td><input type='date' name='ogtt_date[]' class='form-control' value='" . htmlspecialchars($ogtt['test_date']) . "'></td>
                        <td><input type='text' name='ogtt_pog[]' class='form-control' value='" . htmlspecialchars($ogtt['pog']) . "'></td>
                        <td><input type='number' step='0.01' name='fasting[]' class='form-control' value='" . htmlspecialchars($ogtt['fasting_blood_sugar']) . "'></td>
                        <td><input type='number' step='0.01' name='two_hour[]' class='form-control' value='" . htmlspecialchars($ogtt['two_hour_postprandial']) . "'></td>
                        <td>
                            <input type='hidden' name='ogtt_id[]' value='" . htmlspecialchars($ogtt['id']) . "'>
                            <button type='button' class='btn btn-danger remove-row'>Remove</button>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <h4 class="mt-5">HbA1c Test Results</h4>
    <table class="table table-bordered" id="hba1c-table">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>POG</th>
                <th>HbA1c (%)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($hba1c_results as $hba1c) {
                echo "<tr>
                        <td><input type='date' name='hba1c_date[]' class='form-control' value='" . htmlspecialchars($hba1c['test_date']) . "'></td>
                        <td><input type='text' name='hba1c_pog[]' class='form-control' value='" . htmlspecialchars($hba1c['pog']) . "'></td>
                        <td><input type='number' step='0.01' name='hba1c[]' class='form-control' value='" . htmlspecialchars($hba1c['hba1c']) . "'></td>
                        <td>
                            <input type='hidden' name='hba1c_id[]' value='" . htmlspecialchars($hba1c['id']) . "'>
                            <button type='button' class='btn btn-danger remove-row'>Remove</button>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="text-end">
        <button type="submit" class="btn btn-pink">Update</button>
        <a href="doctorNurse_view_checklist_ogtt_hba1c.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary mt-3">Cancel</a>
    </div>
</form>
</main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
<script>
    document.getElementById("add-ogtt-row").addEventListener("click", function() {
        var table = document.getElementById("ogtt-table").getElementsByTagName("tbody")[0];
        var newRow = table.insertRow();
        newRow.innerHTML = `
            <td><input type="date" name="ogtt_date[]" class="form-control"></td>
            <td><input type="text" name="ogtt_pog[]" class="form-control"></td>
            <td><input type="number" step="0.01" name="fasting[]" class="form-control"></td>
            <td><input type="number" step="0.01" name="two_hour[]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
        `;
    });

    document.getElementById("add-hba1c-row").addEventListener("click", function() {
        var table = document.getElementById("hba1c-table").getElementsByTagName("tbody")[0];
        var newRow = table.insertRow();
        newRow.innerHTML = `
            <td><input type="date" name="hba1c_date[]" class="form-control"></td>
            <td><input type="text" name="hba1c_pog[]" class="form-control"></td>
            <td><input type="number" step="0.01" name="hba1c[]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
        `;
    });

    document.addEventListener("click", function(event) {
        if (event.target && event.target.classList.contains("remove-row")) {
            var row = event.target.closest("tr");
            row.remove();
        }
    });
</script>
</body>
</html>