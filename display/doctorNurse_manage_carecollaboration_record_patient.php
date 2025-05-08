<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor&nurse)
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [2, 3])) {
    header("Location: ../login.php");
    exit();
}

$roleName = ($_SESSION['role_id'] == 2) ? "Doctor" : "Nurse";
echo "<h2 class='mb-4'>{$roleName} - Patient Health Record Management</h2>";

// Fetch patients
$sql = "SELECT users.id, users.name, users.phone, users.date_of_birth, users.identification_number, patients.last_menstrual_date 
        FROM users 
        JOIN patients ON users.id = patients.user_id 
        WHERE users.role_id = 1";

$userFirstName = $_SESSION['name'];

// Fetch patients
$sql = "SELECT users.id, users.name, users.phone, users.date_of_birth, users.identification_number, patients.last_menstrual_date 
        FROM users 
        JOIN patients ON users.id = patients.user_id 
        WHERE users.role_id = 1";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<title>Manage Patient Health Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
    
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    <main>
    <div class="container mt-5">
    <h2 class="mb-4">Patient Care Collaboration Record Management</h2>


    <table class="table table-bordered shadow-sm">
    <thead>
            <tr>
                <th>Name</th>
                <th>Identification Number</th>
                <th>Phone</th>
                <th>Date of Birth</th>
                <th>Last Menstrual Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['identification_number']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($row['last_menstrual_date']) ?></td>
                    <td>
                        <a href="doctor_view_carecollaboration_record.php?user_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="doctor_carecollaboration_health_record.php?user_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Update</a>
                        <a href="doctor_delete_carecollaboration_record.php?user_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No patient records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/scripts.php'); ?>
</body>
</html>
    </main>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php include('../includes/scripts.php'); ?>
</body>
    

</html>
