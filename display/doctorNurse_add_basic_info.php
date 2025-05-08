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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data
    $fields = [
        'full_name', 'registration_number', 'id_card_number', 'date_of_birth', 'age',
        'clinic_phone_number', 'jkn_serial_number', 'antenatal_color_code', 'ethnic_group',
        'nationality', 'education_level', 'occupation', 'home_address_1', 'home_address_2',
        'phone_residential', 'phone_mobile', 'phone_office', 'nurse_ym', 'workplace_address',
        'estimated_due_date', 'revised_due_date', 'gravida', 'para',
        'husband_name', 'husband_id_card_number', 'husband_occupation',
        'husband_workplace_address', 'husband_phone_residential', 'husband_phone_mobile',
        'postnatal_address_1', 'postnatal_address_2', 'postnatal_address_3', 'risk_factors'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    // Basic validation
    if (empty($data['full_name']) || empty($data['registration_number']) || empty($data['id_card_number'])) {
        $error = "Full name, registration number and IC number are required.";
    } else {
        $sql = "INSERT INTO mother_information (
            patient_id, " . implode(",", $fields) . "
        ) VALUES (
            ?, " . str_repeat("?,", count($fields) - 1) . "?
        )";

        $stmt = $conn->prepare($sql);

        $types = "i" . str_repeat("s", count($fields));
        $stmt->bind_param($types, $patient_id, ...array_values($data));

        if ($stmt->execute()) {
            $success = "Mother information added successfully.";
        } else {
            $error = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>Add basic info</title>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4">Add Mother's Basic Information</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <h5>Personal Info</h5>
    <div class="row g-3">
        <div class="col-md-6"><label>Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
        <div class="col-md-6"><label>Registration Number *</label><input type="text" name="registration_number" class="form-control" required></div>
        <div class="col-md-6"><label>IC Number *</label><input type="text" name="id_card_number" class="form-control" required></div>
        <div class="col-md-6"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
        <div class="col-md-3"><label>Age</label><input type="number" name="age" class="form-control"></div>
        <div class="col-md-3"><label>Clinic Phone</label><input type="text" name="clinic_phone_number" class="form-control"></div>
        <div class="col-md-3"><label>JKN Serial Number</label><input type="text" name="jkn_serial_number" class="form-control"></div>
        <div class="col-md-3"><label>Antenatal Color Code</label><input type="text" name="antenatal_color_code" class="form-control"></div>
        <div class="col-md-3"><label>Ethnic Group</label><input type="text" name="ethnic_group" class="form-control"></div>
        <div class="col-md-3"><label>Nationality</label><input type="text" name="nationality" class="form-control"></div>
        <div class="col-md-3"><label>Education Level</label><input type="text" name="education_level" class="form-control"></div>
        <div class="col-md-3"><label>Occupation</label><input type="text" name="occupation" class="form-control"></div>
    </div>

    <hr><h5>Home Address</h5>
    <div class="mb-2"><label>Address Line 1</label><textarea name="home_address_1" class="form-control"></textarea></div>
    <div class="mb-2"><label>Address Line 2</label><textarea name="home_address_2" class="form-control"></textarea></div>

    <div class="row g-3">
        <div class="col-md-4"><label>Phone (Residential)</label><input type="text" name="phone_residential" class="form-control"></div>
        <div class="col-md-4"><label>Phone (Mobile)</label><input type="text" name="phone_mobile" class="form-control"></div>
        <div class="col-md-4"><label>Phone (Office)</label><input type="text" name="phone_office" class="form-control"></div>
    </div>

    <div class="mb-3 mt-2"><label>Nurse YM</label><input type="text" name="nurse_ym" class="form-control"></div>
    <div class="mb-3"><label>Workplace Address</label><textarea name="workplace_address" class="form-control"></textarea></div>

    <hr><h5>Pregnancy Info</h5>
    <div class="row g-3">
        <div class="col-md-4"><label>Estimated Due Date</label><input type="date" name="estimated_due_date" class="form-control"></div>
        <div class="col-md-4"><label>Revised Due Date</label><input type="date" name="revised_due_date" class="form-control"></div>
        <div class="col-md-2"><label>Gravida</label><input type="number" name="gravida" class="form-control"></div>
        <div class="col-md-2"><label>Para</label><input type="number" name="para" class="form-control"></div>
    </div>

    <hr><h5>Husband Info</h5>
    <div class="row g-3">
        <div class="col-md-6"><label>Husband Name</label><input type="text" name="husband_name" class="form-control"></div>
        <div class="col-md-6"><label>Husband IC Number</label><input type="text" name="husband_id_card_number" class="form-control"></div>
        <div class="col-md-6"><label>Occupation</label><input type="text" name="husband_occupation" class="form-control"></div>
        <div class="col-md-6"><label>Workplace Address</label><textarea name="husband_workplace_address" class="form-control"></textarea></div>
        <div class="col-md-6"><label>Phone (Residential)</label><input type="text" name="husband_phone_residential" class="form-control"></div>
        <div class="col-md-6"><label>Phone (Mobile)</label><input type="text" name="husband_phone_mobile" class="form-control"></div>
    </div>

    <hr><h5>Postnatal Address</h5>
    <div class="mb-2"><label>Address Line 1</label><textarea name="postnatal_address_1" class="form-control"></textarea></div>
    <div class="mb-2"><label>Address Line 2</label><textarea name="postnatal_address_2" class="form-control"></textarea></div>
    <div class="mb-2"><label>Address Line 3</label><textarea name="postnatal_address_3" class="form-control"></textarea></div>

    <div class="mb-3"><label>Risk Factors</label><textarea name="risk_factors" class="form-control"></textarea></div>

    <button type="submit" class="btn btn-pink">Submit</button>
    <a href="doctorNurse_manage_health_record_patient.php" class="btn btn-secondary mt-3">Cancel</a>
</form>
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>

