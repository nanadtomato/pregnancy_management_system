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
// Initialize form data
$fields = [
    'full_name', 'id_card_number', 'consent_1', 'reason_1', 'consent_2', 'reason_2',
    'consent_3', 'reason_3', 'consent_4', 'reason_4', 'consent_5', 'reason_5',
     'mother_signature_name', 'mother_signature_ic',
    'mother_signature_date', 'witness_name', 'witness_ic', 'witness_position', 'witness_date', 'consent_acknowledged'
];

$data = array_fill_keys($fields, '');

$success = "";
$error = "";

// Load existing data
$sql = "SELECT * FROM consent_declaration WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    foreach ($fields as $field) {
        if ($field === 'consent_acknowledged') {
            $data[$field] = isset($_POST['agree']) ? 'Yes' : 'No';
        } else {
            $data[$field] = $_POST[$field] ?? null;
        }
    }
    
}
$stmt->close();

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    $checkExisting = $conn->prepare("SELECT id FROM consent_declaration WHERE patient_id = ?");
    $checkExisting->bind_param("i", $patient_id);
    $checkExisting->execute();
    $checkExisting->store_result();

    if ($checkExisting->num_rows > 0) {
        $sql = "UPDATE consent_declaration SET " .
            implode(' = ?, ', $fields) . " = ? WHERE patient_id = ?";
    } else {
        $sql = "INSERT INTO consent_declaration (" . implode(', ', $fields) . ", patient_id) VALUES (" .
            implode(', ', array_fill(0, count($fields), '?')) . ", ?)";
    }

    $checkExisting->close();

    $stmt = $conn->prepare($sql);
    $types = str_repeat("s", count($fields)) . "i";
    $params = array_merge(array_values($data), [$patient_id]);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success = "Consent declaration updated successfully.";
    } else {
        $error = "Error: " . $stmt->error;
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
<title>Add Consent Declaration</title>

</head>


 <body>
 <div class="main-content">
 <main>
 
 <h2>Maternal and Child Healthcare Consent Form</h2>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($data['full_name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Identification Card Number</label>
        <input type="text" name="id_card_number" class="form-control" value="<?= htmlspecialchars($data['id_card_number']) ?>" required>
    </div>

    <h5 class="mt-4">Therefore, I choose to: (please tick the appropriate box)</h5>

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th style="width: 5%;">No.</th>
            <th style="width: 50%;">Procedure</th>
            <th style="width: 15%;">Consent</th>
            <th style="width: 30%;">If No, please state reason</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $procedures = [
            "Home visits by healthcare personnel during pregnancy, especially for high-risk mothers (e.g., diabetes, hypertension, anemia, etc.)." => ['consent_1', 'reason_1'],
            "Home visits by healthcare personnel after delivery to check the health status of the mother and baby." => ['consent_2', 'reason_2'],
            "Administration of immunization injections according to the prescribed schedule." => ['consent_3', 'reason_3'],
            "Reminders and follow-up visits if I fail to attend appointments at the clinic." => ['consent_4', 'reason_4'],
            "Home visits to check on at-risk infants and children: - i. Jaundice (Children Act Amendment 2016, Section 17(1)(f) or Section 24(1)) ii. Others (e.g., malnutrition, children with special needs, etc.)" => ['consent_5', 'reason_5'],
        ];

        $i = 1;
        foreach ($procedures as $label => [$consent, $reason]):
        ?>
        <tr>
            <td><?= $i++ ?>.</td>
            <td><?= $label ?></td>
            <td>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $consent ?>" value="Yes" <?= ($data[$consent] == "Yes") ? "checked" : "" ?>> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $consent ?>" value="No" <?= ($data[$consent] == "No") ? "checked" : "" ?>> No
                </div>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $reason ?>" value="<?= htmlspecialchars($data[$reason]) ?>" placeholder="If No, state reason">
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <hr>
    <h5>Expectant Mother's Signature</h5>
    <div class="mb-2"><label>Name</label><input type="text" name="mother_signature_name" class="form-control" value="<?= htmlspecialchars($data['mother_signature_name']) ?>"></div>
    <div class="mb-2"><label>IC Number</label><input type="text" name="mother_signature_ic" class="form-control" value="<?= htmlspecialchars($data['mother_signature_ic']) ?>"></div>
    <div class="mb-2"><label>Date</label><input type="date" name="mother_signature_date" class="form-control" value="<?= htmlspecialchars($data['mother_signature_date']) ?>"></div>

    <hr>
    <h5>Witness Signature</h5>
    <div class="mb-2"><label>Name</label><input type="text" name="witness_name" class="form-control" value="<?= htmlspecialchars($data['witness_name']) ?>"></div>
    <div class="mb-2"><label>IC Number</label><input type="text" name="witness_ic" class="form-control" value="<?= htmlspecialchars($data['witness_ic']) ?>"></div>
    <div class="mb-2"><label>Position</label><input type="text" name="witness_position" class="form-control" value="<?= htmlspecialchars($data['witness_position']) ?>"></div>
    <div class="mb-2"><label>Date</label><input type="date" name="witness_date" class="form-control" value="<?= htmlspecialchars($data['witness_date']) ?>"></div>

    <div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" name="agree" id="agree" required <?= ($data['consent_acknowledged'] == 'Yes') ? 'checked' : '' ?>>
    <label class="form-check-label" for="agree">
        I declare that the above information is true and consent is given.
    </label>
</div>

    <button type="submit" class="btn btn-primary mt-3">Add Consent</button>
    <a href="doctorNurse_view_consent_declaration.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary mt-3">Cancel</a>
</form>



   
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

