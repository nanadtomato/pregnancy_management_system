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

$fields = [
    'full_name', 'id_card_number', 'consent_1', 'reason_1', 'consent_2', 'reason_2',
    'consent_3', 'reason_3', 'consent_4', 'reason_4', 'consent_5', 'reason_5',
    'mother_signature_name', 'mother_signature_ic', 'mother_signature_date',
    'witness_name', 'witness_ic', 'witness_position', 'witness_date', 'consent_acknowledged'
];

$data = array_fill_keys($fields, '');

$sql = "SELECT * FROM consent_declaration WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    foreach ($fields as $field) {
        $data[$field] = $row[$field] ?? '';
    }
} else {
    die("No consent declaration found for this patient.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>View Blood Collection Consent</title>
   
        
</head>
<body>
<div class="main-content">
    <main>
    <h2 class="text-center mb-4">Maternal and Child Healthcare Consent Form</h2>



<div class="card mb-4">
    <div class="card-header">Consent</div>
    <div class="card-body">
    <strong>I, <?= htmlspecialchars($data['full_name']) ?> (Identification Card Number: <?= htmlspecialchars($data['id_card_number']) ?>),</strong>
    understand that it is the responsibility of healthcare personnel to provide quality healthcare services to improve the health status of both mother and child. I have also been given an explanation and understand the importance of the procedures listed below.
</div>
    </div>


<h5 class="mt-4">Consent Declaration</h5>

<table class="table table-bordered align-middle">
    <thead class="table-light">
    <tr>
        <th style="width: 5%;">No.</th>
        <th style="width: 50%;">Procedure</th>
        <th style="width: 15%;">Consent</th>
        <th style="width: 30%;">If No, Reason</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $procedures = [
        "Home visits by healthcare personnel during pregnancy, especially for high-risk mothers (e.g., diabetes, hypertension, anemia, etc.)." => ['consent_1', 'reason_1'],
        "Home visits by healthcare personnel after delivery to check the health status of the mother and baby." => ['consent_2', 'reason_2'],
        "Administration of immunization injections according to the prescribed schedule." => ['consent_3', 'reason_3'],
        "Reminders and follow-up visits if I fail to attend appointments at the clinic." => ['consent_4', 'reason_4'],
        "Home visits to check on at-risk infants and children:
        - i. Jaundice (Children Act Amendment 2016, Section 17(1)(f) or Section 24(1)) 
        - ii. Others (e.g., malnutrition, children with special needs, etc.)" => ['consent_5', 'reason_5'],
    ];

    $i = 1;
    foreach ($procedures as $label => [$consent, $reason]):
        ?>
        <tr>
            <td><?= $i++ ?>.</td>
            <td><?= $label ?></td>
            <td><?= htmlspecialchars($data[$consent]) ?></td>
            <td><?= htmlspecialchars($data[$reason]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="card mb-4">
    <div class="card-header">Expectant Mother's Signature</div>
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
    <div class="card-header">Declaration:</div>
    <div class="card-body">

    <?= ($data['consent_acknowledged'] === 'Yes') ? 'I declare that the above information is true and consent is given.' : 'Consent not acknowledged.' ?>
</div>
    </div>

   

<a href="doctorNurse_edit_consent_declaration.php?user_id=<?= $patient_user_id ?>" class="btn btn-pink">Edit </a>

    </main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
