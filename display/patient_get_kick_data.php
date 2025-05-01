// === get_kick_data.php ===
<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    http_response_code(403);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$query = "SELECT log_date, kick_count FROM kick_logs WHERE patient_id = ? ORDER BY log_date DESC LIMIT 7";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($range === 'custom' && $start && $end) {
    $where .= " AND log_date BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
} elseif ($range === 'today') {
    $where .= " AND log_date = CURDATE()";
} elseif (is_numeric($range)) {
    $where .= " AND log_date >= CURDATE() - INTERVAL ? DAY";
    $params[] = (int)$range;
    $types .= "i";
}

$sql = "SELECT log_date, kick_count FROM kick_logs WHERE $where ORDER BY log_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
