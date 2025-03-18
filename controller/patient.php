<?php
require_once '../config/config.php';

class patient {
    // View Patient Information (Patients can only view)
    public function viewPatientInfo() {
        session_start();
        $user_id = $_SESSION['user_id'];
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM patients WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        require '../views/patient/patient_dashboard.php';
    }

    // Patients log baby kicks
    public function logKickCount() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $user_id = $_SESSION['user_id'];
            $kick_count = $_POST['kick_count'];

            $stmt = $conn->prepare("INSERT INTO pregnancy_tracking (user_id, kick_count, log_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $user_id, $kick_count);
            $stmt->execute();
        }
    }

    // View pregnancy tracking logs
    public function viewPregnancyTracking() {
        session_start();
        $user_id = $_SESSION['user_id'];
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM pregnancy_tracking WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        require '../views/patient/pregnancy_tracking.php';
    }
}
?>
