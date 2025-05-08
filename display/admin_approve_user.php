<?php
$result = $conn->query("SELECT * FROM users WHERE is_approved = 0");

while ($user = $result->fetch_assoc()) {
    echo "<p>{$user['name']} - {$user['email']} - <a href='approve_user.php?id={$user['id']}'>Approve</a></p>";
}
?>
