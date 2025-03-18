<?php
require_once "../config.php";


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['date'])) {
        $patientId = $_SESSION['user_id']; // Assuming user_id is stored in session
        $selectedDate = $_GET['date'];

        // Retrieve logs for the selected date
        $query = "SELECT * FROM kick_logs WHERE patient_id = ? AND log_date = ? ORDER BY start_time";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $patientId, $selectedDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $logs]);
    } else {
        // Handle the case when 'date' is not provided
        echo json_encode(["status" => "error", "message" => "Date parameter is missing"]);
        exit;
    }
}
?>
