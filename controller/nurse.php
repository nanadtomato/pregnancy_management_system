<?php
require_once '../config/config.php';

class nurse {
    public function viewPatientRecords() {
        session_start();
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM patients");
        $stmt->execute();
        $result = $stmt->get_result();

        require '../views/nurse/view_patients.php';
    }
}
?>
