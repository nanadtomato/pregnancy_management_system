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

$patient_user_id = intval($_GET['user_id']);

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

// Get patient name
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->bind_param("i", $patient_user_id);
$stmt->execute();
$stmt->bind_result($patient_full_name);
$stmt->fetch();
$stmt->close();

// Get refusal form records
$stmt = $conn->prepare("SELECT id, file_name, file_path, upload_date FROM treatment_refusal_forms WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$refusal_forms = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>view basic info</title>


</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Treatment Refusal Forms for <?= htmlspecialchars($patient_full_name) ?></h2>

<?php if (count($refusal_forms) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-pink">
                <tr>
                    <th>#</th>
                    <th>File Name</th>
                    <th>Upload Date</th>
                    <th>Download</th>
                    <th>Action</th>
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
                        <td>
                            <a href="doctorNurse_edit_refusal_of_treatment.php?user_id=<?= $patient_user_id ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="doctorNurse_delete_refusal_of_treatment.php?form_id=<?= $form['id'] ?>&user_id=<?= $patient_user_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this form?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">No treatment refusal forms uploaded yet.</div>
<?php endif; ?>


<a href="doctorNurse_view_health_record.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary btn-rounded">Back</a>


  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
