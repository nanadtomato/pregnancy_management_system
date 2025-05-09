<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";


// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$patient_user_id = $_SESSION['user_id'];

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
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Blood Collection Consent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <style>
        .card-header { background-color: #f8bbd0; color: white; }
        ul.custom-bullet li::before {
            content: "●";
            color: #d81b60;
            font-weight: bold;
            display: inline-block;
            width: 1.2em;
            margin-left: 1.2em;
        }
        .checkbox-result { margin-left: 10px; font-weight: 500; color: #d81b60; }
        .card { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="main-content">
    <main>
    <h2 class="mb-4">My Blood Collection Consent</h2>

    <!-- Purpose -->
    <div class="card">
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
            <div class="card h-100">
                <div class="card-header">My Consent</div>
                <div class="card-body">
                    <p>I, <strong><?= htmlspecialchars($consentData['mother_fullname']) ?></strong> (NRIC: 
                    <strong><?= htmlspecialchars($consentData['mother_nric']) ?></strong>), understand the explanation 
                    and give consent for my blood to be taken for the following tests:</p>
                </div>
            </div>
        </div>

        <!-- Consent Details -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Consent Details</div>
                <div class="card-body">
                    <?php
                    $requiredTests = ['Blood Group & Rhesus', 'Hemoglobin / Full Blood Count','Diabetes Screening','Syphilis', 'HIV', 'Hepatitis B', 'Malaria (BFMP)'];
                    $agreedTests = array_map('trim', explode(',', $consentData['tests']));
                    ?>
                    <ul class="custom-bullet">
                        <?php foreach ($requiredTests as $test): ?>
                            <li><?= htmlspecialchars($test) ?>
                                <?= in_array($test, $agreedTests) ? '<span class="text-success">(✔)</span>' : '<span class="text-danger">(✘)</span>' ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Other Tests:</strong> <?= htmlspecialchars($consentData['other_tests']) ?></p>
                    <p><strong>Signature:</strong> <?= htmlspecialchars($consentData['mother_signature']) ?></p>
                    <p><strong>Witness Name:</strong> <?= htmlspecialchars($consentData['witness_name']) ?></p>
                    <p><strong>Witness NRIC:</strong> <?= htmlspecialchars($consentData['witness_nric']) ?></p>
                    <p><strong>Date of Consent:</strong> <?= htmlspecialchars($consentData['consent_date']) ?></p>
                </div>
            </div>
        </div>
    </div>

   

    <!-- Important Note -->
    <div class="card">
        <div class="card-header">Important Note</div>
        <div class="card-body">
            <p>If you are under 18, consent should be obtained from a parent or guardian as per clinic guidelines.</p>
        </div>
    </div>

    <!-- Risk Factors -->
    <div class="card">
        <div class="card-header">Reminder I: Risk Factors</div>
        <div class="card-body">
            <p>Please inform your healthcare provider if you experience or are at risk due to the following:</p>
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

    <!-- Malaria Info -->
    <div class="card mb-5">
        <div class="card-header">Reminder II : Malaria Screening</div>
        <div class="card-body">
        <p>All mothers suspected of having malaria infection with a cyclic fever pattern — sudden onset of fever characterized by cold, shivering, and sweating phases followed by a hot and sweaty phase (body temperature exceeding 40˚C). This cycle typically lasts 6 to 10 hours and is followed by an asymptomatic phase. The cycle will recur if left untreated.</p>
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
     <div class="card">
        <div class="card-header">My Antenatal Blood Screening Results</div>
        <div class="card-body">
            <?php if (count($screeningResults) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
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
                </div>
            <?php else: ?>
                <div class="alert alert-warning">No screening results available.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
        <div class="alert alert-warning">No blood collection consent data found.</div>
    <?php endif; ?>
    <a href="patient_health_record.php" class="btn btn-secondary btn-rounded">Back</a>
    </main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
