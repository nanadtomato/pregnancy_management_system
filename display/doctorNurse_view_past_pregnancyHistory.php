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

// Get past pregnancy history
$stmt = $conn->prepare("SELECT * FROM past_pregnancy_history WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
<title>view basic info</title>

<style>
    .btn-link {
            color: #d81b60;
        }
        .btn-link:hover {
            color: #c2185b;
        }
       
</style>

</head>


 <body>
 <div class="main-content">
 <main>
 <h2>Past Pregnancy History: <?= htmlspecialchars($patient_full_name) ?></h2>

<?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                 <th>#</th>
                            <th>Year</th>
                            <th>Marriage Date</th>
                            <th>Outcome</th>
                            <th>Delivery Type</th>
                            <th>Place and Attendant</th>
                            <th>Gender</th>
                            <th>Birth Weight (kg)</th>
                            <th>Complications (Mother)</th>
                            <th>Complications (Child)</th>
                            <th>Breastfeeding Info</th>
                            <th>Current Condition</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
            </thead>
            <tbody>
                        <?php $counter = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($row['year']) ?></td>
                                <td><?= htmlspecialchars($row['marriage_date']) ?></td>
                                <td><?= htmlspecialchars($row['outcome']) ?></td>
                                <td><?= htmlspecialchars($row['delivery_type']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['place_and_attendant'])) ?></td>
                                <td><?= htmlspecialchars($row['gender']) ?></td>
                                <td><?= htmlspecialchars($row['birth_weight']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['complications_mother'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['complications_child'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['breastfeeding_info'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['current_condition'])) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>
                                
                                <a href="doctorNurse_edit_past_pregnancyHistory.php?user_id=<?= $user_id ?>&history_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>


                                    <a href="doctorNurse_delete_past_pregnancyHistory.php?id=<?= $row['id'] ?>&user_id=<?= $user_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No past pregnancy history found for this patient.</p>
        <?php endif; ?>

        
  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
