<?php
// view_subsection.php
session_start();
require_once "../config.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Fetch the patient ID and section from the URL
$patient_id = $_GET['patient_id'];
$section = $_GET['section']; // section like 'basic_info', 'past_pregnancy_history', etc.

// Fetch the relevant data based on the section
switch ($section) {
    case 'basic_info':
        // Fetch data from the 'mother_information' table
        $query = "SELECT * FROM mother_information WHERE patient_id = ?";
        break;
    case 'past_pregnancy_history':
        // Fetch data from the 'past_pregnancy_history' table
        $query = "SELECT * FROM past_pregnancy_history WHERE patient_id = ?";
        break;
    case 'family_health_history':
        // Fetch data from the 'family_health_history' table
        $query = "SELECT * FROM family_health_history WHERE patient_id = ?";
        break;
    default:
        die("Unknown section");
}



$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle the form submission for updating or adding data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($section) {
        case 'basic_info':
            $registration_number = $_POST['registration_number'];
            $date_of_birth = $_POST['date_of_birth'];
            $query_update = "UPDATE mother_information SET registration_number = ?, date_of_birth = ? WHERE patient_id = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("ssi", $registration_number, $date_of_birth, $patient_id);
            $stmt_update->execute();
            break;
        case 'past_pregnancy_history':
            $year = $_POST['year'];
            $outcome = $_POST['outcome'];
            $query_update = "INSERT INTO past_pregnancy_history (patient_id, year, outcome) VALUES (?, ?, ?)";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("iis", $patient_id, $year, $outcome);
            $stmt_update->execute();
            break;
        // Add more cases for other subsections like 'family_health_history'
    }


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
<title>Report</title>

<style>
     .btn-pink { background-color: #f78da7; color: white; }
        .btn-pink {
            background-color: #f78da7;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-pink:hover {
            background-color: #f55d83;
        }
        .card-custom {
            border-left: 5px solid #f78da7;
            background-color: #fff0f4;
        }
</style>
</head>


 <body>
 <div class="main-content">
 <main>
    
 <div class="container mt-5">
        <h2 class="mb-4"><?= ucfirst(str_replace('_', ' ', $section)) ?> - Patient Health Record</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php
                    // Display the table headers based on the section
                    if ($section == 'basic_info') {
                        echo '<th>Field Name</th><th>Value</th><th>action</th>';
                    } elseif ($section == 'past_pregnancy_history') {
                        echo '<th>Year</th><th>Outcome</th><th>Delivery Type</th><th>Place & Attendant</th><th>Gender</th><th>Birth Weight</th><th>action</th>';
                    } elseif ($section == 'family_health_history') {
                        echo '<th>Menstruation Days</th><th>Cycle</th><th>Family Planning</th><th>Smoking (Mother)</th><th>Smoking (Husband)</th><th>action</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php
                        // Display data based on the section
                        if ($section == 'basic_info') {
                            echo "<td>Registration Number</td><td>" . htmlspecialchars($row['registration_number']) . "</td>";
                            echo "<td>Date of Birth</td><td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
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
                        }
                        ?>
                         <td>
                            <!-- Edit Button -->
                            <a href="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=<?= $section ?>&record_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <!-- Delete Button -->
                            <a href="doctorNurse_delete_health_record.php?patient_id=<?= $patient_id ?>&section=<?= $section ?>&record_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
