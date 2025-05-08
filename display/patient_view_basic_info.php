<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Get patient_id from user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

if (!$patient_id) {
    die("Patient not found for this user.");
}

// Get patient full name
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($patient_full_name);
$stmt->fetch();
$stmt->close();

// Get mother information
$stmt = $conn->prepare("SELECT * FROM mother_information WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("No basic information found for this patient.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    <title>My Basic Information</title>
</head>

<body>
    <div class="main-content">
        <main>
            <h2>My Basic Information</h2>

            <div class="card">
                <div class="card-header">Mother's Basic Info</div>
                <div class="card-body row">
                    <?php
                    $fields = [
                        'Full Name' => 'full_name',
                        'Registration Number' => 'registration_number',
                        'ID Card Number' => 'id_card_number',
                        'Date of Birth' => 'date_of_birth',
                        'Age' => 'age',
                        'Clinic Phone Number' => 'clinic_phone_number',
                        'JKN Serial Number' => 'jkn_serial_number',
                        'Antenatal Color Code' => 'antenatal_color_code',
                        'Ethnic Group' => 'ethnic_group',
                        'Nationality' => 'nationality',
                        'Education Level' => 'education_level',
                        'Occupation' => 'occupation',
                        'Phone (Residential)' => 'phone_residential',
                        'Phone (Mobile)' => 'phone_mobile',
                        'Phone (Office)' => 'phone_office',
                        'Nurse YM' => 'nurse_ym',
                        'Estimated Due Date' => 'estimated_due_date',
                        'Revised Due Date' => 'revised_due_date',
                        'Gravida' => 'gravida',
                        'Para' => 'para'
                    ];

                    foreach ($fields as $label => $key) {
                        echo '<div class="col-md-6"><strong>' . $label . ':</strong> ' . htmlspecialchars($data[$key]) . '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Addresses</div>
                <div class="card-body row">
                    <div class="col-md-6"><strong>Home Address 1:</strong> <?= nl2br(htmlspecialchars($data['home_address_1'])) ?></div>
                    <div class="col-md-6"><strong>Home Address 2:</strong> <?= nl2br(htmlspecialchars($data['home_address_2'])) ?></div>
                    <div class="col-md-6"><strong>Workplace Address:</strong> <?= nl2br(htmlspecialchars($data['workplace_address'])) ?></div>
                    <div class="col-md-6"><strong>Postnatal Address 1:</strong> <?= nl2br(htmlspecialchars($data['postnatal_address_1'])) ?></div>
                    <div class="col-md-6"><strong>Postnatal Address 2:</strong> <?= nl2br(htmlspecialchars($data['postnatal_address_2'])) ?></div>
                    <div class="col-md-6"><strong>Postnatal Address 3:</strong> <?= nl2br(htmlspecialchars($data['postnatal_address_3'])) ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Husband Information</div>
                <div class="card-body row">
                    <div class="col-md-6"><strong>Husband Name:</strong> <?= htmlspecialchars($data['husband_name']) ?></div>
                    <div class="col-md-6"><strong>Husband ID Card Number:</strong> <?= htmlspecialchars($data['husband_id_card_number']) ?></div>
                    <div class="col-md-6"><strong>Husband Occupation:</strong> <?= htmlspecialchars($data['husband_occupation']) ?></div>
                    <div class="col-md-6"><strong>Husband Workplace Address:</strong> <?= nl2br(htmlspecialchars($data['husband_workplace_address'])) ?></div>
                    <div class="col-md-6"><strong>Husband Phone (Residential):</strong> <?= htmlspecialchars($data['husband_phone_residential']) ?></div>
                    <div class="col-md-6"><strong>Husband Phone (Mobile):</strong> <?= htmlspecialchars($data['husband_phone_mobile']) ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Risk Factors & Timestamps</div>
                <div class="card-body">
                    <div><strong>Risk Factors:</strong> <?= nl2br(htmlspecialchars($data['risk_factors'])) ?></div>
                    <div><strong>Last Updated:</strong> <?= htmlspecialchars($data['updated_at']) ?></div>
                </div>
            </div>

           
        </main>
    </div>

    <?php include('../includes/navbar.php'); ?>
    <?php include('../includes/scripts.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>