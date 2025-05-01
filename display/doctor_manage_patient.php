<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$userFirstName = $_SESSION['name'];
// Search logic
$search = $_GET['search'] ?? '';
$sql = "SELECT u.id AS user_id, u.name, u.identification_number, u.date_of_birth, m.estimated_due_date
        FROM users u
        JOIN patients p ON u.id = p.user_id
        LEFT JOIN mother_information m ON p.id = m.patient_id
        WHERE u.role_id = 1 AND (u.name LIKE ? OR u.identification_number LIKE ?)
        ORDER BY u.name ASC";

global $conn;
$stmt = $conn->prepare($sql);
$searchParam = "%" . $search . "%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$patients = $stmt->get_result();


?>
<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
    <title>Patient Management</title>
    <style>
        .btn-pink {
            background-color: #f78da7;
            color: white;
        }
        .btn-pink:hover {
            background-color: #f55d83;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    <main>
    <main class="container mt-4">
        <h2 class="mb-4 text-center">Patient Management</h2>

        <!-- Search Form -->
        <form class="row g-3 mb-4" method="GET" action="">
            <div class="col-md-10">
                <input type="text" class="form-control" name="search" placeholder="Search by Name or IC Number" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-pink w-100">Search</button>
            </div>
        </form>

        <!-- Patient Table -->
        <div class="card shadow-sm">
            
                <div class="card-header bg-danger-subtle text-dark">Patient List</div>
        
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>IC Number</th>
                            <th>Estimated Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while ($row = $patients->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['identification_number']); ?></td>
                            <td><?php echo $row['estimated_due_date'] ? date('d M Y', strtotime($row['estimated_due_date'])) : '—'; ?></td>
                            <td>
                                <a href="healthRecord.php?patient_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-pink">Health Record</a>
                                <a href="careCollaboration.php?patient_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-primary">Care Collab</a>
                                <a href="pregnancyTracking.php?patient_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-secondary">Tracking</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($patients->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No patients found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php include('../includes/scripts.php'); ?>
</body>
    

</html>
