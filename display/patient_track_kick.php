<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$patient_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('H:i:s');

// Handle actions
$action = $_GET['action'] ?? '';

if ($action === 'count') {
    $stmt = $conn->prepare("SELECT kick_count FROM kick_logs WHERE patient_id = ? AND log_date = ?");
    $stmt->bind_param("is", $patient_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = 0;
    if ($row = $result->fetch_assoc()) {
        $count = $row['kick_count'];
    }
    echo json_encode(['kick_count' => $count]);
    exit();
}

if ($action === 'track') {
    $sql = "SELECT * FROM kick_logs WHERE patient_id = ? AND log_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $patient_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $log = $result->fetch_assoc();
        if ($log['kick_count'] >= 10) {
            echo json_encode(['status' => 'done', 'message' => 'Already logged 10 kicks today.']);
            exit();
        }

        $new_count = $log['kick_count'] + 1;
        $end_time = $new_count == 10 ? $now : $log['end_time'];
        $update_sql = "UPDATE kick_logs SET kick_count = ?, end_time = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isi", $new_count, $end_time, $log['id']);
        $update_stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Kick tracked!', 'kick_count' => $new_count]);
    } else {
        $insert_sql = "INSERT INTO kick_logs (patient_id, log_date, start_time, end_time, kick_count) VALUES (?, ?, ?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isss", $patient_id, $today, $now, $now);
        $insert_stmt->execute();

        echo json_encode(['status' => 'started', 'message' => 'Kick tracking started.', 'kick_count' => 1]);
    }
    exit();
}

// You can add more actions (like manual entry) here
