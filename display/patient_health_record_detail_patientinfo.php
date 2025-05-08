<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Patient)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$userFirstName = $_SESSION['name'];
$section = $_GET['section'] ?? '';  // e.g. 'basic_info', 'past_pregnancy_history', etc.
// Validate section
$valid_sections = ['basic_info', 'past_pregnancy_history', 'family_health_history','blood_collection_consent'];
if (!in_array($section, $valid_sections)) {
    die("Invalid section.");
}

// Build the query
switch ($section) {
    case 'basic_info':
        $query = "SELECT * FROM mother_information WHERE patient_id = ?";
        break;
    case 'past_pregnancy_history':
        $query = "SELECT * FROM past_pregnancy_history WHERE patient_id = ?";
        break;
    case 'family_health_history':
        $query = "SELECT * FROM family_health_history WHERE patient_id = ?";
        break;
    case 'blood_collection_consent':
            $query = "SELECT * FROM blood_collection_consent WHERE patient_id = ?";
            break;
    
}
// Execute query
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient health record detail patient info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">

    

<body>
 <div class="main-content">
 <main>
 <main class="container mt-5">
        <h2 class="mb-4 text-center"><?= ucfirst(str_replace('_', ' ', $section)) ?> - My Record</h2>

        <table class="table table-bordered shadow-sm">
            <thead>
                <tr>
                    <?php
                    if ($section == 'basic_info') {
                        echo '<th>Registration Number</th><th>Date of Birth</th>';
                    } elseif ($section == 'past_pregnancy_history') {
                        echo '<th>Year</th><th>Outcome</th><th>Delivery Type</th><th>Place & Attendant</th><th>Gender</th><th>Birth Weight</th>';
                    } elseif ($section == 'family_health_history') {
                        echo '<th>Menstruation Days</th><th>Cycle</th><th>Family Planning</th><th>Smoking (Mother)</th><th>Smoking (Husband)</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php
                        if ($section == 'basic_info') {
                            echo "<td>" . htmlspecialchars($row['registration_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                        } elseif ($section == 'past_pregnancy_history') {
                            echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['outcome']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['delivery_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['place_and_attendant']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['birth_weight']) . "</td>";
                        } elseif ($section == 'family_health_history') {
                            echo "<td>" . htmlspecialchars($row['menstruation_days']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['menstruation_cycle']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['family_planning_method']) . "</td>";
                            echo "<td>" . ($row['smoking_mother'] ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . ($row['smoking_husband'] ? 'Yes' : 'No') . "</td>";
                        } elseif ($section == 'blood_collection_consent') {
                        }
                        ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($section == 'blood_collection_consent'): ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Consent declaration submitted successfully.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Failed to submit consent. Please try again.</div>
    <?php endif; ?>

    <?php $consent = $result->fetch_assoc(); ?>


    <form method="POST" action="patient_save_consent_declaration.php" class="mt-4">
        <p>I, <strong><?= htmlspecialchars($userFirstName) ?></strong>, NRIC No. <input type="text" name="nric" required>, understand the explanation and hereby give consent for my blood to be taken for the following tests:</p>

        <?php
        $tests = [
            'blood_group' => 'Blood Group & Rhesus',
            'hemoglobin' => 'Hemoglobin / Full Blood Count',
            'diabetes' => 'Diabetes Screening',
            'syphilis' => 'Syphilis',
            'hiv' => 'HIV',
            'hepatitis_b' => 'Hepatitis B',
            'malaria' => 'Malaria'
        ];

        foreach ($tests as $field => $label):
        ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="<?= $field ?>" id="<?= $field ?>">
                <label class="form-check-label" for="<?= $field ?>"><?= $label ?></label>
            </div>
        <?php endforeach; ?>

        <div class="mt-3">
            <label>Others (specify):</label>
            <input type="text" class="form-control" name="others">
        </div>

        <hr>
        <h5>Signatures</h5>
        <div class="mb-2">
            <label>Patient’s Signature:</label>
            <input type="text" class="form-control" name="signature_mother" required>
        </div>
        <div class="mb-2">
            <label>Witness Signature:</label>
            <input type="text" class="form-control" name="signature_witness" required>
        </div>
        <div class="mb-2">
            <label>Witness Name:</label>
            <input type="text" class="form-control" name="name_witness" required>
        </div>
        <div class="mb-2">
            <label>Witness NRIC:</label>
            <input type="text" class="form-control" name="nric_witness" required>
        </div>
        <div class="mb-2">
            <label>Date:</label>
            <input type="date" class="form-control" name="consent_date" value="<?= date('Y-m-d') ?>" required>
        </div>

        <input type="hidden" name="patient_id" value="<?= $_SESSION['user_id'] ?>">
        <button type="submit" class="btn btn-pink">Submit Consent</button>
    </form>
<?php endif; ?>

    </main>


  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
