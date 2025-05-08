<?php
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

$fields = [
    'full_name', 'id_card_number', 'consent_1', 'reason_1', 'consent_2', 'reason_2',
    'consent_3', 'reason_3', 'consent_4', 'reason_4', 'consent_5', 'reason_5',
    'mother_signature_name', 'mother_signature_ic', 'mother_signature_date',
    'witness_name', 'witness_ic', 'witness_position', 'witness_date', 'consent_acknowledged'
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
        $data[$field] = $row[$field] ?? '';
    }
}
$stmt->close();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($fields as $field) {
        if ($field === 'consent_acknowledged') {
            $data[$field] = isset($_POST['agree']) ? 'Yes' : 'No';
        } else {
            $data[$field] = $_POST[$field] ?? '';
        }
    }

    $sql = "UPDATE consent_declaration SET " . implode(" = ?, ", $fields) . " = ? WHERE patient_id = ?";
    $stmt = $conn->prepare($sql);
    $types = str_repeat("s", count($fields)) . "i";
    $params = array_merge(array_values($data), [$patient_id]);

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success = "Consent declaration updated successfully.";
    } else {
        $error = "Error updating consent declaration: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>Edit Consent Declaration</title>
</head>
<body>
<div class="main-content">
<main>
    <h2>Edit Maternal and Child Healthcare Consent Form</h2>

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
                    <th>No.</th>
                    <th>Procedure</th>
                    <th>Consent</th>
                    <th>If No, please state reason</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $procedures = [
                    "Home visits by healthcare personnel during pregnancy..." => ['consent_1', 'reason_1'],
                    "Home visits by healthcare personnel after delivery..." => ['consent_2', 'reason_2'],
                    "Administration of immunization injections..." => ['consent_3', 'reason_3'],
                    "Reminders and follow-up visits..." => ['consent_4', 'reason_4'],
                    "Home visits to check on at-risk infants and children..." => ['consent_5', 'reason_5'],
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
                    <td><input type="text" name="<?= $reason ?>" class="form-control" value="<?= htmlspecialchars($data[$reason]) ?>" placeholder="If No, state reason"></td>
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
            <label class="form-check-label" for="agree">I declare that the above information is true and consent is given.</label>
        </div>

        <button type="submit" class="btn btn-primary">Update Consent</button>
        <a href="doctorNurse_view_health_record.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary">Cancel</a>
    </form>
</main>
</div>

<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>