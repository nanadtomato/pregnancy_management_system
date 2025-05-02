<?php
session_start();
require_once "../config.php";

// Allow only nurses
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $purpose = $_POST['purpose'];

    $query = "INSERT INTO appointments (patient_id, appointment_date, appointment_time, purpose) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $patient_id, $appointment_date, $appointment_time, $purpose);
    $stmt->execute();
    $success = "Appointment added successfully!";
}

$sql = "SELECT users.id, users.name 
        FROM users 
        JOIN patients ON users.id = patients.user_id 
        WHERE users.role_id = 1 
        ORDER BY users.name ASC";
$patients = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
    <title>Nurse Dashboard</title>
</head>
<body>
 <div class="main-content">
 <main>

 <div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Nurse: Add New Appointment</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="patient_id" class="form-label">Select Patient</label>
            <select name="patient_id" id="patient_id" class="form-select" required>
                <option value="">-- Choose Patient --</option>
                <?php while ($row = $patients->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="appointment_date" class="form-label">Appointment Date</label>
            <input type="date" name="appointment_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="appointment_time" class="form-label">Appointment Time</label>
            <input type="time" name="appointment_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="purpose" class="form-label">Purpose / Notes</label>
            <textarea name="purpose" class="form-control" rows="3" placeholder="Checkup, Lab Test, etc." required></textarea>
        </div>

        <button type="submit" class="btn btn-pink">Add Appointment</button>
    </form>
</div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>