<?php
session_start();
require_once "../config.php";

// Check user role: Doctor (2) or Nurse (3)
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

// Fetch consent form
$consentData = null;
$stmt = $conn->prepare("SELECT mother_fullname, mother_nric, tests, other_tests, mother_signature, witness_name, witness_nric, consent_date FROM blood_collection_consent WHERE patient_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $consentData = $result->fetch_assoc();
}
$stmt->close();

// Fetch screening results
$screeningResults = [];
$stmt = $conn->prepare("SELECT condition_name, date_collected, result, recorded_by, date_recorded FROM antenatal_blood_screening_results WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $screeningResults[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>View Blood Collection Consent</title>
    <style>
        .checkbox-label {
            font-weight: 600;
            margin-right: 10px;
            color: #ad1457; 
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-result {
            margin-left: 10px;
            font-weight: 500;
            color: #d81b60;
        }

        ul {
            padding-left: 5px;
        }

        ul li {
            padding-left: 0;
            margin-bottom: 8px;
        }

        .card-header {
            background-color: #f8bbd0;
            color: white;
        }

        .btn-pink:hover {
            background-color: #c2185b;
        }

        .card-body ul {
            list-style: none;
            padding-left: 0;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .card {
            margin-bottom: 20px; /* Add some space between the cards */
        }

        .card-header {
            padding: 10px 15px; /* Ensures consistent padding within headers */
        }

        .card-body {
            padding: 15px; /* Consistent padding inside card body */
        }

        .card.mb-4.shadow-sm {
            margin-top: 20px; /* Add space between important note card and the card above */
        }

        ul.custom-bullet li::before {
    content: "●";
    color: #d81b60; /* Soft pink */
    font-weight: bold;
    display: inline-block;
    width: 1.2em;
    margin-left: 1.2em;
}

    </style>
</head>
<body>
<div class="main-content">
    <main>
    <h2 class="mb-4">Blood Collection Consent: <?= htmlspecialchars($consentData['mother_fullname'] ?? 'N/A') ?></h2>

<!-- Purpose -->
<div class="card mb-4">
    <div class="card-header">Purpose of Blood Screening for Pregnant Mothers</div>
    <div class="card-body">
        <ul class="custom-bullet">
            <li>To ensure the mother is in optimal health</li>
            <li>To detect diseases and allow early treatment</li>
            <li>To prevent infection and complications to the baby</li>
            <li>To detect infections early and manage the partner</li>
        </ul>
    </div>
</div>

<?php if ($consentData): ?>
    <div class="row">
       <!-- Mother's Consent -->
<div class="col-md-6">
    <div class="card mb-4 h-100">
        <div class="card-header">Mother's Consent</div>
        <div class="card-body">
            <p>I, <strong><?= htmlspecialchars($consentData['mother_fullname']) ?></strong> NRIC No. 
            <strong><?= htmlspecialchars($consentData['mother_nric']) ?></strong>, understand the explanation given orally 
            and hereby give consent for my blood to be taken for the following tests:</p>
        </div>
    </div>
</div>

<!-- Consent Details -->
<div class="col-md-6">
    <div class="card shadow-sm h-100">
        <div class="card-header bg-lightpink text-white"><strong>Consent Details</strong></div>
        <div class="card-body">
            <?php
            $requiredTests = ['Blood Group & Rhesus', 'Hemoglobin / Full Blood Count','Diabetes Screening','Syphilis', 'HIV', 'Hepatitis B', 'Malaria (BFMP)'];
            $agreedTests = array_map('trim', explode(',', $consentData['tests']));

            ?>
            <div><strong>Tests Agreed:</strong></div>
            <ul class="custom-bullet">
                <?php foreach ($requiredTests as $test): ?>
                    <li>
                        <?= htmlspecialchars($test) ?>
                        <?= in_array(trim($test), $agreedTests) ? '<span class="text-success">(✔)</span>' : '<span class="text-danger">(✘)</span>' ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p><strong>Other Tests:</strong> <?= htmlspecialchars($consentData['other_tests']) ?></p>
            <p><strong>Mother's Signature:</strong> <?= htmlspecialchars($consentData['mother_signature']) ?></p>
            <p><strong>Witness Name:</strong> <?= htmlspecialchars($consentData['witness_name']) ?></p>
            <p><strong>Witness NRIC:</strong> <?= htmlspecialchars($consentData['witness_nric']) ?></p>
            <p><strong>Date of Consent:</strong> <?= htmlspecialchars($consentData['consent_date']) ?></p>
        </div>
    </div>
</div>

</div>

    <!-- Notes -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header"><strong>Important Note</strong></div>
        <div class="card-body">
            <p>If the mother is a minor under 18 years, obtain consent from a husband aged ≥18. If husband is <18, obtain consent from parent/guardian using the Parent/Guardian Consent Form for Medical Treatment at Health Clinics.</p>
        </div>
    </div>

    <!-- Reminder I -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header"><strong>Reminder I: Risk Factors to Declare</strong></div>
        <div class="card-body">
            <p>Mother must inform healthcare staff if they have any of the following risk factors:</p>
            <ul class="custom-bullet">
                <li>Use of illicit drugs</li>
                <li>Engaging in sexual relations with another partner</li>
                <li>Partner is a drug addict</li>
                <li>Partner engages in sexual relations with other partners</li>
                <li>Partner is confirmed to have HIV or Syphilis</li>
            </ul>
            <p>If the mother has one or more of the above risk factors, then:</p>
            <ul class="custom-bullet">
                <li>If the HIV screening test is non-reactive, a repeat screening should be conducted to detect infection during the window period.</li>
                <li>Syphilis screening should be repeated at 28 to 32 weeks of pregnancy.</li>
            </ul>
        </div>
    </div>

    <!-- Reminder II -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header"><strong>Reminder II: Malaria Screening</strong></div>
        <div class="card-body">
            <p>All mothers suspected of having malaria infection with a cyclic fever pattern — sudden onset of fever characterized by cold, shivering, and sweating phases followed by a hot and sweaty phase (body temperature exceeding 40˚C). This cycle typically lasts 6 to 10 hours and is followed by an asymptomatic phase. The cycle will recur if left untreated.</p>
            <p>High-risk groups include:</p>
            <ul class="custom-bullet">
                <li>Indigenous communities (Orang Asli))</li>
                <li>Residents living at the forest fringe or forested areas</li>
                <li>Citizens or non-citizens from malaria-endemic countries or areas</li>
                <li>Individuals involved in activities at risk of malaria infection such as logging, agriculture, recreational activities, security forces, and hunting</li>
                <li>Close contacts of malaria cases within the past 6 weeks</li>
            </ul>
        </div>
    </div>

    <!-- Screening Results -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-lightpink text-white"><strong>Antenatal Blood Screening Results</strong></div>
        <div class="card-body">
            <?php if (count($screeningResults) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Condition/Status</th>
                            <th>Date Collected</th>
                            <th>Result</th>
                            <th>Recorded By</th>
                            <th>Date Recorded</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($screeningResults as $index => $result): ?>
                        <tr>
                            <td><?= $index + 1 ?>.</td>
                            <td><?= htmlspecialchars($result['condition_name']) ?></td>
                            <td><?= htmlspecialchars($result['date_collected']) ?></td>
                            <td><?= htmlspecialchars($result['result']) ?></td>
                            <td><?= htmlspecialchars($result['recorded_by']) ?></td>
                            <td><?= htmlspecialchars($result['date_recorded']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="doctorNurse_edit_blood_collection_consent.php?user_id=<?= $patient_user_id ?>" class="btn btn-pink">Update</a>


        

            </div>
            <?php else: ?>
            <div class="alert alert-warning">No screening results recorded.</div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">No consent form data available for this patient.</div>
<?php endif; ?>

    </main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
