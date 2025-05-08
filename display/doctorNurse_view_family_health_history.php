<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../../login.php");
    exit();
}
// Get user_id from GET parameter
if (!isset($_GET['user_id'])) {
    die("User ID not provided.");
}
$user_id = intval($_GET['user_id']);

// Get patient_id from user_id
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

// Fetch family health history
$stmt = $conn->prepare("SELECT * FROM family_health_history WHERE patient_id = ? LIMIT 1");

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
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>view basic info</title>

<style>
        

        /* Custom checkbox styles */
        .checkbox-label {
    font-weight: 600;
    margin-right: 10px;
    color: #ad1457;  /* Match the color of <strong> */
}


        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-result {
            margin-left: 10px;
            font-weight: 500;
            color: #d81b60;
        }

        ul {
    padding-left: 5px;  /* Ensures consistent padding on the left side */
}

ul li {
    padding-left: 0;     /* Ensures there’s no extra left padding on each <li> */
    margin-bottom: 8px;  /* Adjust margin for consistent spacing between items */
}


    </style>

</head>


 <body>
 <div class="main-content">
 <main>

 <h2 class="text-center mb-4">Family Health History: <?= htmlspecialchars($patient_full_name) ?></h2>

<?php if (!$data): ?>
    <div class="alert alert-warning">No family health history found for this patient.</div>
<?php else: ?>
    <!-- Family Health History Section -->
    <div class="card">
        <div class="card-header">Menstruation & Family Planning</div>
        <div class="card-body">
            <ul>
                <li><strong>Menstruation Days:</strong> <?= $data['menstruation_days'] ?></li>
                <li><strong>Menstruation Cycle:</strong> <?= $data['menstruation_cycle'] ?></li>
                <li><strong>Family Planning Practice:</strong> <?= $data['family_planning_practice'] ? 'Yes' : 'No' ?></li>
                <li><strong>Method:</strong> <?= $data['family_planning_method'] ?></li>
                <li><strong>Duration:</strong> <?= $data['family_planning_duration'] ?></li>
            </ul>
        </div>
    </div>

    <!-- Smoking History Section -->
    <div class="card">
        <div class="card-header">Smoking History</div>
        <div class="card-body">
            <ul>
                <li><strong>Mother:</strong> <?= $data['smoking_mother'] ? 'Yes' : 'No' ?></li>
                <li><strong>Husband:</strong> <?= $data['smoking_husband'] ? 'Yes' : 'No' ?></li>
            </ul>
        </div>
    </div>

    <!-- Medical Conditions Section -->
    <div class="card">
        <div class="card-header">Mother's Medical Conditions</div>
        <div class="card-body">
            <?php
            $mother_conditions = [
                'asthma', 'diabetes', 'thalassemia', 'thyroid', 'hypertension',
                'heart_disease', 'allergy', 'tb', 'cancer', 'psychiatric', 'anemia'
            ];
            foreach ($mother_conditions as $cond) {
                echo '<div class="checkbox-wrapper">';
                echo '<label class="checkbox-label">' . ucfirst($cond) . '</label>';
                echo '<input type="checkbox" disabled ' . ($data["condition_$cond"] ? 'checked' : '') . '>';
                echo '<span class="checkbox-result">' . ($data["condition_$cond"] ? 'Yes' : 'No') . '</span>';
                echo '</div>';
            }
            if (!empty($data['condition_others'])) {
                echo '<div class="checkbox-wrapper">';
                echo '<strong>Other:</strong> ' . $data['condition_others'];
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Family History Section -->
    <div class="card">
        <div class="card-header">Family Medical History</div>
        <div class="card-body">
            <?php
            $family_conditions = [
                'asthma', 'diabetes', 'anemia', 'hypertension', 'heart_disease',
                'thalassemia', 'allergy', 'tb', 'psychiatric'
            ];
            foreach ($family_conditions as $cond) {
                echo '<div class="checkbox-wrapper">';
                echo '<label class="checkbox-label">' . ucfirst($cond) . '</label>';
                echo '<input type="checkbox" disabled ' . ($data["family_$cond"] ? 'checked' : '') . '>';
                echo '<span class="checkbox-result">' . ($data["family_$cond"] ? 'Yes' : 'No') . '</span>';
                echo '</div>';
            }
            if (!empty($data['family_others'])) {
                echo '<div class="checkbox-wrapper">';
                echo '<strong>Other:</strong> ' . $data['family_others'];
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Immunisation Records Section -->
    <div class="card">
        <div class="card-header">Immunisation Records</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Dose</th>
                        <th>Date</th>
                        <th>Batch No</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach (['dose1', 'dose2', 'booster'] as $dose) {
                        echo "<tr>
                            <td>" . ucfirst($dose) . "</td>
                            <td>{$data["immunisation_{$dose}_date"]}</td>
                            <td>{$data["immunisation_{$dose}_batch_no"]}</td>
                            <td>{$data["immunisation_{$dose}_expiry"]}</td>
                        </tr>";
                    }
                    ?>
                    <tr>
                        <td>Other</td>
                        <td colspan="3"><?= $data['immunisation_other1'] ?><br><?= $data['immunisation_other2'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>


<a href="doctorNurse_edit_family_health_history.php?user_id=<?= $user_id ?>" class="btn btn-pink">Edit Information</a>
<a href="doctorNurse_view_health_record.php?user_id=<?= $user_id ?>" class="btn btn-secondary btn-rounded">Back</a>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
