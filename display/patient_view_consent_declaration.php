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

// Get patient full name
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_full_name);
$stmt->fetch();
$stmt->close();
// Fields expected from consent_declaration
$fields = [
    'full_name', 'id_card_number', 'consent_1', 'reason_1', 'consent_2', 'reason_2',
    'consent_3', 'reason_3', 'consent_4', 'reason_4', 'consent_5', 'reason_5',
    'mother_signature_name', 'mother_signature_ic', 'mother_signature_date',
    'witness_name', 'witness_ic', 'witness_position', 'witness_date', 'consent_acknowledged'
];

$data = array_fill_keys($fields, '');

// Fetch consent declaration data
$stmt = $conn->prepare("SELECT * FROM consent_declaration WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    foreach ($fields as $field) {
        $data[$field] = $row[$field] ?? '';
    }
} else {
    die("No consent declaration record found.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>My Consent Declarations</title>
</head>

<body>
    <div class="main-content">
        <main>
        <h2>My Consent Declaration: <?= htmlspecialchars($patient_full_name) ?></h2>

        <div class="card mb-4">
            <div class="card-header">Consent Overview</div>
            <div class="card-body">
                <strong>I, <?= htmlspecialchars($data['full_name']) ?> (IC: <?= htmlspecialchars($data['id_card_number']) ?>),</strong>
                understand the responsibilities of healthcare personnel and the importance of the procedures listed below.
            </div>
        </div>

        <h5 class="mt-4">Consent Details</h5>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Procedure</th>
                <th>Consent</th>
                <th>Reason (if No)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $procedures = [
                "Home visits during pregnancy for high-risk cases." => ['consent_1', 'reason_1'],
                "Postnatal home visits for mother and baby health." => ['consent_2', 'reason_2'],
                "Routine immunizations according to schedule." => ['consent_3', 'reason_3'],
                "Reminders/follow-ups for missed appointments." => ['consent_4', 'reason_4'],
                "Home visits for at-risk children (e.g., jaundice, malnutrition)." => ['consent_5', 'reason_5'],
            ];

            $i = 1;
            foreach ($procedures as $desc => [$consent, $reason]): ?>
                <tr>
                    <td><?= $i++ ?>.</td>
                    <td><?= $desc ?></td>
                    <td><?= htmlspecialchars($data[$consent]) ?></td>
                    <td><?= htmlspecialchars($data[$reason]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="card mb-4">
            <div class="card-header">Mother's Signature</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($data['mother_signature_name']) ?></p>
                <p><strong>IC Number:</strong> <?= htmlspecialchars($data['mother_signature_ic']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($data['mother_signature_date']) ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Witness Signature</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($data['witness_name']) ?></p>
                <p><strong>IC Number:</strong> <?= htmlspecialchars($data['witness_ic']) ?></p>
                <p><strong>Position:</strong> <?= htmlspecialchars($data['witness_position']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($data['witness_date']) ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Declaration</div>
            <div class="card-body">
                <?= ($data['consent_acknowledged'] === 'Yes') 
                    ? 'I declare that the above information is true and consent is given.' 
                    : 'Consent not acknowledged.' ?>
            </div>
            </div>
            <a href="patient_health_record.php" class="btn btn-secondary btn-rounded">Back</a>
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@
