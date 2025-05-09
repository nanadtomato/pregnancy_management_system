<?php
session_start();
require_once "../config.php";

// Restrict access to Doctor (2) or Nurse (3)
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    die("Patient not selected.");
}

$patient_user_id = intval($_GET['user_id']);

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

// Fetch the latest treatment refusal form
$stmt = $conn->prepare("SELECT id, file_name, file_path FROM treatment_refusal_forms WHERE patient_id = ? ORDER BY upload_date DESC LIMIT 1");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$existing_form = $result->fetch_assoc();
$stmt->close();

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["refusal_form"])) {
    $file = $_FILES["refusal_form"];
    $allowed_types = ['application/pdf'];

    if (!in_array($file['type'], $allowed_types)) {
        $error = "Only PDF files are allowed.";
    } else {
        $upload_dir = "../uploads/refusal_forms/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate new unique file name
        $file_name = uniqid("refusal_", true) . ".pdf";
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // If an old file exists, delete it
            if (!empty($existing_form['file_path']) && file_exists($existing_form['file_path'])) {
                unlink($existing_form['file_path']);
            }

            // Update or insert new form
            if ($existing_form) {
                $stmt = $conn->prepare("UPDATE treatment_refusal_forms SET file_name = ?, file_path = ?, upload_date = NOW() WHERE id = ?");
                $stmt->bind_param("ssi", $file_name, $file_path, $existing_form['id']);
            } else {
                $stmt = $conn->prepare("INSERT INTO treatment_refusal_forms (patient_id, file_name, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $patient_id, $file_name, $file_path);
            }

            if ($stmt->execute()) {
                $success = "Treatment refusal form updated successfully.";
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
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>Edit Treatment Refusal Form</title>
    <style> 
        
        .form-label {
            font-weight: 600;
        }

        .custom-alert {
            animation: fadeSlideIn 0.5s ease-out;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .current-file-info {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 15px;
        }

        .icon-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-title i {
            color:rgb(231, 49, 134);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <main>
        <div class="icon-title mb-4">
        <i class="fas fa-file-medical fa-2x"></i>
        <h2 class="mb-0">Edit Treatment Refusal Declaration Form</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success custom-alert"><i class="fas fa-check-circle me-2"></i><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger custom-alert"><i class="fas fa-exclamation-triangle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <?php if ($existing_form): ?>
        <div class="current-file-info mb-3">
            <strong>Currently uploaded form:</strong>
            <br>
            <a href="<?= htmlspecialchars($existing_form['file_path']) ?>" target="_blank" class="btn btn-outline-primary mt-2">
                <i class="fas fa-eye me-1"></i> View Form
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary">
            <i class="fas fa-info-circle me-1"></i> No form has been uploaded yet.
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="refusal_form" class="form-label">Upload New Treatment Refusal Form <span class="text-muted">(PDF only)</span></label>
            <input type="file" name="refusal_form" class="form-control" accept=".pdf" required>
            <div class="invalid-feedback">Please upload a valid PDF file.</div>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Update Form</button>
            <a href="doctorNurse_add_health_record.php?user_id=<?= $patient_user_id ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
        </div>
    </form>
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Bootstrap 5 client-side validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form =>
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false)
        );
    })();
</script>
</body>
</html>
