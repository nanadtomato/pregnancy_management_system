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

    public function view_health_record() {
        session_start();
        global $conn;
    
        // Get the logged-in user
        $user_id = $_SESSION['user_id'] ?? null;
        $name = $_SESSION['name'] ?? 'Unknown';
    
        $mother_info = [];
        $pregnancy_history = [];
        $family_history = [];
    
        if ($user_id) {
            // Get patient ID from patients table
            $stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
    
            if ($result && isset($result['id'])) {
                $patient_id = $result['id'];
    
                // Mother's info
                $stmt = $conn->prepare("SELECT * FROM mother_information WHERE patient_id = ?");
                $stmt->bind_param("i", $patient_id);
                $stmt->execute();
                $mother_info = $stmt->get_result()->fetch_assoc();
    
                // Past pregnancy history
                $stmt = $conn->prepare("SELECT * FROM past_pregnancy_history WHERE patient_id = ?");
                $stmt->bind_param("i", $patient_id);
                $stmt->execute();
                $pregnancy_history = $stmt->get_result();
    
                // Family health history
                $stmt = $conn->prepare("SELECT * FROM family_health_history WHERE patient_id = ?");
                $stmt->bind_param("i", $patient_id);
                $stmt->execute();
                $family_history = $stmt->get_result()->fetch_assoc();
            }
        }
    
        // Pass the variables to the view
        require '../display/healthRecord.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle Mother's Info Submission
            if (isset($_POST['submit_mother_info'])) {
                $patient_id = $_POST['patient_id'];
                $registration_number  = $_POST['registration_number'];
                $id_card_number = $_POST['id_card_number'];
                $date_of_birth  = $_POST['date_of_birth '];
                $age = $_POST['age'];
                $clinic_phone_number = $_POST['clinic_phone_number'];
                $jkn_serial_number  = $_POST['jkn_serial_number'];
                $antenatal_color_code  = $_POST['antenatal_color_code'];
                $ethnic_group = $_POST['ethnic_group'];
                $nationality = $_POST['nationality'];
                $education_level = $_POST['education_level'];
                $occupation = $_POST['occupation'];
                $home_address_1  = $_POST['home_address_1'];
                $home_address_2  = $_POST['home_address_2 '];
                $phone_residential = $_POST['phone_residential'];
                $phone_mobile  = $_POST['phone_mobile'];
                $phone_office  = $_POST['phone_office'];
                $nurse_ym  = $_POST['nurse_ym '];
                $workplace_address = $_POST['workplace_address'];
                $estimated_due_date  = $_POST['estimated_due_date'];
                $revised_due_date  = $_POST['revised_due_date'];
                $gravida = $_POST['gravida'];
                $para = $_POST['para'];
                $husband_name  = $_POST['husband_name'];
                $husband_occupation  = $_POST['husband_occupation'];
                $husband_workplace_address  = $_POST['husband_workplace_address'];
                $husband_phone_residential = $_POST['husband_phone_residential'];
                $husband_phone_mobile  = $_POST['husband_phone_mobile'];
                $postnatal_address_1  = $_POST['postnatal_address_1'];
                $risk_factors  = $_POST['risk_factors'];
               
        
                $stmt = $conn->prepare("UPDATE mother_information SET id_card_number = ?, date_of_birth=?, age=?, clinic_phone_number = ?, jkn_serial_number=?, antenatal_color_code=?, 
                 ethnic_group = ?, nationality=?,education_level=?, occupation=?, home_address_1=?, home_address_2=?, phone_residential=?, phone_mobile=?, phone_office=?, nurse_ym=?, workplace_address=?, estimated_due_date=?, revised_due_date=?, gravida=?, para=?, husband_name=?, husband_occupation=?, husband_workplace_address=?, husband_phone_residential=?,postnatal_address_1=?, risk_factors=?           WHERE patient_id = ?");
                "sssissssssssssssssiiissssssssssssi",
                $registration_number, $id_card_number, $date_of_birth, $age, $clinic_phone_number,
                $jkn_serial_number, $antenatal_color_code, $ethnic_group, $nationality, $education_level,
                $occupation, $home_address_1, $home_address_2, $phone_residential, $phone_mobile,
                $phone_office, $nurse_ym, $workplace_address, $estimated_due_date, $revised_due_date,
                $gravida, $para, $husband_name, $husband_id_card_number, $husband_occupation,
                $husband_workplace_address, $husband_phone_residential, $husband_phone_mobile,
                $postnatal_address_1, $postnatal_address_2, $postnatal_address_3, $risk_factors,
                $patient_id );

                if ($stmt->execute()) {
                    echo "Mother's info updated successfully.";
                } else {
                    echo "Error: " . $stmt->error;
                }
        
                $stmt->close();
            }
            
        
            // Handle Past Pregnancy History Submission
            if (isset($_POST['submit_past_pregnancy'])) {
                $patient_id = $_POST['patient_id'];
                $year = $_POST['year'];
                $outcome = $_POST['outcome'];
                $delivery_type = $_POST['delivery_type'];
                $place_and_attendant = $_POST['place_and_attendant'];
                $gender = $_POST['gender'];
                $birth_weight  = $_POST['birth_weight'];
                $breastfeeding_info  = $_POST['breastfeeding_info '];
                $current_condition  = $_POST['current_condition '];
                $marriage_date  = $_POST['marriage_date '];
                
        
                $stmt = $conn->prepare("INSERT INTO past_pregnancy_history 
                (patient_id, year, outcome, delivery_type, place_and_attendant, gender, birth_weight, breastfeeding_info, current_condition, marriage_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param(
                        "iisssssiss", 
                        $patient_id, $year, $outcome, $delivery_type, $place_and_attendant,
                        $gender, $birth_weight, $breastfeeding_info, $current_condition, $marriage_date
                    );
                    if ($stmt->execute()) {
                        echo "Past pregnancy history added successfully.";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                
                    $stmt->close();
                }
            }
        
            // Handle Family Health History Submission
            if (isset($_POST['submit_family_health'])) {
                $patient_id = $_POST['patient_id'];
                $menstruation_days = $_POST['menstruation_days'];
                $menstruation_cycle = $_POST['mentsruation_cycle'];
                $family_planning_method  = $_POST['family_planning_method '];
                $family_planning_duration  = $_POST['family_planning_duration '];
                $smoking_mother = isset($_POST['smoking_mother']) ? 1 : 0;
                $smoking_husband= isset($_POST['smoking_father']) ? 1 : 0;
                $conditions  = $_POST['conditions'];
                $tb_screening  =isset($_POST['tb_screening']) ? 1 : 0;
                $cough_more_than_2_weeks = isset($_POST['cough_more_than_2_weeks']) ? 1 : 0;
                $family_conditions = $_POST['family_conditions'];
                $immunisation_status = $_POST['immunisation_status'];
               
               
               $stmt = $conn->prepare("UPDATE family_health_history SET 
                menstruation_days = ?, 
                menstruation_cycle = ?, 
                family_planning_method = ?, 
                family_planning_duration = ?, 
                smoking_mother = ?, 
                smoking_husband = ?, 
                conditions = ?, 
                tb_screening = ?, 
                cough_more_than_2_weeks = ?, 
                family_conditions = ?, 
                immunisation_status = ? 
                WHERE patient_id = ?");
        
            $stmt->bind_param(
                "issssiisiisi", 
                $menstruation_days, 
                $menstruation_cycle, 
                $family_planning_method, 
                $family_planning_duration, 
                $smoking_mother, 
                $smoking_husband, 
                $conditions, 
                $tb_screening, 
                $cough_more_than_2_weeks, 
                $family_conditions, 
                $immunisation_status, 
                $patient_id
            );
        
            if ($stmt->execute()) {
                echo "Family health history updated successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
        
            $stmt->close();
        }
            }
        }
        

    
    
    

}
?>
