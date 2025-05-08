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

$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["refusal_form"])) {
    $file = $_FILES["refusal_form"];

    // Validate file type (only PDF for example)
    $allowed_types = ['application/pdf'];
    if (!in_array($file['type'], $allowed_types)) {
        $error = "Only PDF files are allowed.";
    } else {
        // Create the upload folder if it doesn't exist
        $upload_dir = "../uploads/refusal_forms/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // create folder with permissions
        }

        // Generate a unique file name
        $file_name = uniqid("refusal_", true) . ".pdf";
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the server
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert file information into the database
            $stmt = $conn->prepare("INSERT INTO treatment_refusal_forms (patient_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $patient_id, $file_name, $file_path);

            if ($stmt->execute()) {
                $success = "Treatment refusal form uploaded successfully.";
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Failed to upload the file.";
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
<title>Add Past Pregnancy History</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Upload Treatment Refusal Declaration Form</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="refusal_form" class="form-label">Attach Treatment Refusal Form (PDF only)</label>
                <input type="file" name="refusal_form" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-pink">Submit</button>
            <a href="doctorNurse_add_health_record.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary btn-rounded">Cancel</a>
        
        </form>
    
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

