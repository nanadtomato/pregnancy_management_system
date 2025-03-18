<?php
require_once '../config/config.php';

class admin {
    public function manageUsers() {
        session_start();
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->get_result();

        require '../views/admin/manage_users.php';
    }
}
?>
