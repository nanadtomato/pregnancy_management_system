<?php
session_start();
require_once "../config.php";

// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get patient_id from user_id
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

if (!$patient_id) {
    die("Patient record not found.");
}
// Fetch OGTT criteria
$criteria_query = "SELECT * FROM ogtt_screening_criteria WHERE patient_id = ?";
$stmt = $conn->prepare($criteria_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$criteria_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch latest OGTT test result
$ogtt_query = "SELECT * FROM ogtt_test_results WHERE patient_id = ? ORDER BY test_date DESC LIMIT 1";
$stmt = $conn->prepare($ogtt_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$ogtt_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch latest HbA1c result
$hba1c_query = "SELECT * FROM hba1c_test_results WHERE patient_id = ? ORDER BY test_date DESC LIMIT 1";
$stmt = $conn->prepare($hba1c_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$hba1c_result = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>My OGTT & HbA1c Results</title>
</head>

<body>
    <div class="main-content">
        <main>
        <h2 class="mb-4">My OGTT Screening Checklist and Test Results</h2>

        <h4>Checklist of Criteria for OGTT Screening Test</h4>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 75%;">Criteria / Risk</th>
                    <th style="width: 20%;">Tick (✔️)</th>
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
                $checked = isset($criteria_result[$key]) && $criteria_result[$key] ? '✔️' : '';
                echo "<tr>
                        <td>$i</td>
                        <td>$label</td>
                        <td class='text-center'>$checked</td>
                    </tr>";
                $i++;
            }
            ?>
            </tbody>
        </table>

        <h4 class="mt-5">OGTT Test Result</h4>
        <?php if ($ogtt_result): ?>
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
                        <td><?= htmlspecialchars($ogtt_result['test_date']) ?></td>
                        <td><?= htmlspecialchars($ogtt_result['pog']) ?></td>
                        <td><?= htmlspecialchars($ogtt_result['fasting_blood_sugar']) ?></td>
                        <td><?= htmlspecialchars($ogtt_result['two_hour_postprandial']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No OGTT test result found.</p>
        <?php endif; ?>
        <p class="text-muted mt-3">Normal range: FBS &lt;5.1 mmol/L, 2HPP &lt;7.8 mmol/L<br><em>Refer to CPG Diabetes in Pregnancy 2017</em></p>

        <h4 class="mt-5">HbA1c Test Result</h4>
        <?php if ($hba1c_result): ?>
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
                        <td><?= htmlspecialchars($hba1c_result['test_date']) ?></td>
                        <td><?= htmlspecialchars($hba1c_result['pog']) ?></td>
                        <td><?= htmlspecialchars($hba1c_result['hba1c']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No HbA1c result found.</p>
        <?php endif; ?>

        <p class="text-muted mt-3">Normal range: HbA1c &lt;6.0%<br><em>Refer to CPG Diabetes in Pregnancy 2017</em></p>
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@
