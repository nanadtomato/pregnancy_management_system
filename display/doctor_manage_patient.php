<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$roleName = "Doctor"; // Define role name
$userFirstName = $_SESSION['name'];

// Search logic
$search = $_GET['search'] ?? '';
$sql = "SELECT u.id AS user_id, u.name, u.phone, u.identification_number, u.date_of_birth, p.last_menstrual_date

        FROM users u
        JOIN patients p ON u.id = p.user_id
        LEFT JOIN mother_information m ON p.id = m.patient_id
        WHERE u.role_id = 1 AND (u.name LIKE ? OR u.identification_number LIKE ?)
        ORDER BY u.name ASC";

$stmt = $conn->prepare($sql);
$searchParam = "%" . $search . "%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">
        
        
</head>
<body>
 <div class="main-content">
 <main>

 <h2 class="mb-4 text-center"><?= $roleName ?> - Patient Health Record Management</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Identification Number</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>Last Menstrual Date</th>
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
                            
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">No patient records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>