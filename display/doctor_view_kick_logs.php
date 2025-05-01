// === doctor_view_kick_logs.php ===
<?php
require_once "../config.php";

// Suppose $patient_id is passed via GET or session (validate this in real use)
$patient_id = $_GET['patient_id'] ?? 0;

$query = "SELECT * FROM kick_logs WHERE patient_id = ? ORDER BY log_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Kicks</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['log_date'] ?></td>
            <td><?= $row['start_time'] ?></td>
            <td><?= $row['end_time'] ?></td>
            <td><?= $row['kick_count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>