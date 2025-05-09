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

// Get treatment refusal form records
$stmt = $conn->prepare("SELECT file_name, file_path, upload_date FROM treatment_refusal_forms WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$refusal_forms = $result->fetch_all(MYSQLI_ASSOC);
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
        <h2 class="mb-4">My Treatment Refusal Forms: <?= htmlspecialchars($patient_full_name) ?></h2>

<?php if (count($refusal_forms) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>File Name</th>
                    <th>Upload Date</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($refusal_forms as $index => $form): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($form['file_name']) ?></td>
                        <td><?= htmlspecialchars($form['upload_date']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($form['file_path']) ?>" class="btn btn-sm btn-primary" target="_blank">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">No treatment refusal forms uploaded yet.</div>
<?php endif; ?>

<a href="patient_health_record.php" class="btn btn-secondary mt-3">Back</a>
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@
