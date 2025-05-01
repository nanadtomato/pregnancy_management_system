<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST["patient_id"];
    $log_date = $_POST["log_date"];
    $kick_count = intval($_POST["kick_count"]);

    $query = "SELECT * FROM kick_logs WHERE patient_id = ? AND log_date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $patient_id, $log_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();

    $current_time = date("H:i:s");

    if ($existing) {
        if ($existing['kick_count'] >= 10) {
            header("Location: ../patient/patient_pregnancy_progress.php?msg=limit_reached");
            exit();
        }

        $new_count = $existing['kick_count'] + $kick_count;
        if ($new_count >= 10) {
            $new_count = 10;
            $end_time = $current_time;
        } else {
            $end_time = $existing['end_time'];
        }

        $update = "UPDATE kick_logs SET kick_count = ?, end_time = ? WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("isi", $new_count, $end_time, $existing['id']);
        $stmt->execute();

    } else {
        $insert = "INSERT INTO kick_logs (patient_id, log_date, start_time, end_time, kick_count) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("isssi", $patient_id, $log_date, $current_time, $current_time, $kick_count);
        $stmt->execute();
    }

    header("Location: ../patient/patient_pregnancy_progress.php?msg=success");
    exit();
}
?>
