<?php

require_once "../config.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = $_SESSION['user_id']; // Assuming user_id is stored in session
    $logDate = $_POST['logDate'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $kickCount = $_POST['kickCount'];

    // Save the log in the database
    $query = "INSERT INTO kick_logs (patient_id, log_date, start_time, end_time, kick_count) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $patientId, $logDate, $startTime, $endTime, $kickCount);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Kick log saved successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save kick log.']);
    }
}
?>
