<?php
require_once "../config.php";

if (!isset($_GET['id']) || !isset($_GET['user_id'])) {
    die("Missing parameters.");
}

$id = intval($_GET['id']);
$user_id = intval($_GET['user_id']);

$stmt = $conn->prepare("DELETE FROM past_pregnancy_history WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: doctorNurse_view_past_pregnancyHistory.php?user_id=$user_id");
exit();
