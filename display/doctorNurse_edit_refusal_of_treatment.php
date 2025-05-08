<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id']) || !isset($_GET['history_id'])) {
    die("Required data not provided.");
}

$patient_user_id = $_GET['user_id'];

// Get patient ID
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

// Fetch refusal form entries
$stmt = $conn->prepare("SELECT id, file_name, file_path, upload_date FROM treatment_refusal_forms WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$forms = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$success = "";
$error = "";

// Handle form replacement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['form_id'])) {
        $error = "Form ID not provided.";
    } elseif (!isset($_FILES['new_file'])) {
        $error = "File not provided.";
    } elseif ($_FILES['new_file']['error'] != 0) {
        $error = "File upload error: " . $_FILES['new_file']['error'];
    } else {
        $form_id = intval($_POST['form_id']);
        $new_file = $_FILES['new_file'];

        // Validate file type
        if ($new_file['type'] != 'application/pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            $upload_dir = "../uploads/refusal_forms/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_file_name = uniqid("refusal_", true) . ".pdf";
            $new_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($new_file['tmp_name'], $new_file_path)) {
                // Update DB record
                $stmt = $conn->prepare("UPDATE treatment_refusal_forms SET file_name = ?, file_path = ?, upload_date = NOW() WHERE id = ?");
                $stmt->bind_param("ssi", $new_file_name, $new_file_path, $form_id);

                if ($stmt->execute()) {
                    $success = "Refusal form updated successfully.";
                } else {
                    $error = "Failed to update the record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to upload new file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Edit Refusal of Treatment Form</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2>Edit Treatment Refusal Forms for <?= htmlspecialchars($patient_full_name) ?></h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if (count($forms) > 0): ?>
    <?php foreach ($forms as $form): ?>
        <div class="card mb-3">
            <div class="card-header">
                File: <?= htmlspecialchars($form['file_name']) ?> (Uploaded: <?= htmlspecialchars($form['upload_date']) ?>)
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
                    <div class="col-md-8">
                        <input type="file" name="new_file" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Replace File</button>
                        <a href="<?= $form['file_path'] ?>" target="_blank" class="btn btn-secondary">View Current</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No refusal forms found for this patient.</div>
<?php endif; ?>

<a href="doctorNurse_view_refusal_of_treatment.php?user_id=<?= $patient_user_id ?>" class="btn btn-pink mt-3">View Refusal Forms</a>
<a href="doctorNurse_manage_health_record_patient.php" class="btn btn-secondary mt-3">Back</a>
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>