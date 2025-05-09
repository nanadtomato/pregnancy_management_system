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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert OGTT screening checklist
    $stmt = $conn->prepare("INSERT INTO ogtt_screening_criteria 
        (patient_id, bmi_over_27, history_of_gdm, family_history_diabetes, macrosomic_baby, 
         bad_obstetric_history, glycosuria, current_obstetric_problems, age_over_25) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiiii", $patient_id, $_POST['bmi'], $_POST['gdm'], $_POST['family'],
        $_POST['macrosomia'], $_POST['bad_obstetric'], $_POST['glycosuria'], $_POST['obstetric_problems'], $_POST['age']);
    $stmt->execute();
    $stmt->close();

    // Insert OGTT test results
    if (!empty($_POST['ogtt_date'])) {
        $stmt = $conn->prepare("INSERT INTO ogtt_test_results (patient_id, test_date, pog, fasting_blood_sugar, two_hour_postprandial)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdd", $patient_id, $_POST['ogtt_date'], $_POST['ogtt_pog'],
            $_POST['fasting'], $_POST['two_hour']);
        $stmt->execute();
        $stmt->close();
    }

    // Insert HbA1c test results
    if (!empty($_POST['hba1c_date'])) {
        $stmt = $conn->prepare("INSERT INTO hba1c_test_results (patient_id, test_date, pog, hba1c)
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $patient_id, $_POST['hba1c_date'], $_POST['hba1c_pog'], $_POST['hba1c']);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('Data successfully added.'); window.location.href='doctorNurse_add_checklist_ogtt_hba1c.php?user_id=<?= $user_id ?>';</script>";

    exit();
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
 <h2>Add OGTT Screening & Test Results</h2>
 
 <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

 <form method="POST">
    <h4 class="mt-4">Checklist of Criteria for OGTT Screening Test</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 75%;">Criteria / Risk</th>
                <th style="width: 20%;">Tick (√)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $criteria = [
                "bmi" => "BMI >27 kg/m²",
                "gdm" => "History of Gestational Diabetes Mellitus (GDM)",
                "family" => "Family history of diabetes",
                "macrosomia" => "History of delivering a macrosomic baby (≥4kg)",
                "bad_obstetric" => "Bad Obstetric History: IUD/Stillbirth, congenital abnormalities, shoulder dystocia",
                "glycosuria" => "Glycosuria ≥ 2 times",
                "obstetric_problems" => "Current obstetric problems (Hypertension, Polyhydramnios, Corticosteroids)",
                "age" => "Age ≥25 years"
            ];
            $i = 1;
            foreach ($criteria as $key => $label) {
                echo "<tr>
                        <td>$i</td>
                        <td>$label</td>
                        <td class='text-center'>
                            <input type='checkbox' name='$key' value='1'>
                        </td>
                    </tr>";
                $i++;
            }
            ?>
        </tbody>
    </table>

    <h4 class="mt-5">OGTT Test Results</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>POG</th>
                <th>Fasting Blood Sugar (mmol/L)</th>
                <th>2H Postprandial (mmol/L)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="date" name="ogtt_date" class="form-control"></td>
                <td><input type="text" name="ogtt_pog" class="form-control"></td>
                <td><input type="number" step="0.01" name="fasting" class="form-control"></td>
                <td><input type="number" step="0.01" name="two_hour" class="form-control"></td>
            </tr>
        </tbody>
    </table>
    <p class="text-muted">Normal range: FBS &lt;5.1 mmol/L, 2HPP &lt;7.8 mmol/L <br><em>Refer to CPG Diabetes in Pregnancy 2017</em></p>

    <h4 class="mt-5">HbA1c Test Results</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>POG</th>
                <th>HbA1c (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="date" name="hba1c_date" class="form-control"></td>
                <td><input type="text" name="hba1c_pog" class="form-control"></td>
                <td><input type="number" step="0.01" name="hba1c" class="form-control"></td>
            </tr>
        </tbody>
    </table>
    <p class="text-muted">Normal range: FBS &lt;5.1 mmol/L, 2HPP &lt;7.8 mmol/L <br><em>Refer to CPG Diabetes in Pregnancy 2017</em></p>

    <div class="text-end">
        <button type="submit" class="btn btn-pink">Submit</button>
    </div>
</form>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

