<?php
// view_subsection.php
session_start();
require_once "../config.php";

// Ensure the user is logged in as a Doctor or Nurse
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login.php");
    exit();
}

// Fetch the patient ID and section from the URL
$patient_id = $_GET['patient_id'];
$section = $_GET['section']; // section like 'basic_info', 'past_pregnancy_history', etc.
$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : null;


if ($record_id !== null) {
    // Fetch the relevant data based on the section
    switch ($section) {
        case 'basic_info':
            $query = "SELECT * FROM mother_information WHERE patient_id = ? AND id = ?";
            break;
        case 'past_pregnancy_history':
            $query = "SELECT * FROM past_pregnancy_history WHERE patient_id = ? AND id = ?";
            break;
        case 'family_health_history':
            $query = "SELECT * FROM family_health_history WHERE patient_id = ? AND id = ?";
            break;
        default:
            die("Unknown section");
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $patient_id, $record_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc(); // Fetch the specific record data for editing
    } else {
        // For new entries, we skip fetching
        $data = []; // or set defaults
    }
} // Close the if block for $record_id !== null



?>

<?php
// Handle the form submission for updating or adding data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success_message = '';
    $error_message = '';

    switch ($section) {
        case 'basic_info':
            $registration_number = $_POST['registration_number'];
            $date_of_birth = $_POST['date_of_birth'];
            $id_card_number = $_POST['id_card_number'];
            $age = $_POST['age'];
            $clinic_phone_number = $_POST['clinic_phone_number'];
            $jkn_serial_number = $_POST['jkn_serial_number'];
            $antenatal_color_code = $_POST['antenatal_color_code'];
            $ethnic_group = $_POST['ethnic_group'];
            $nationality = $_POST['nationality'];
            $education_level = $_POST['education_level'];
            $occupation = $_POST['occupation'];
            $home_address_1 = $_POST['home_address_1'];
            $home_address_2 = $_POST['home_address_2'];
            $phone_residential = $_POST['phone_residential'];
            $phone_mobile = $_POST['phone_mobile'];
            $phone_office = $_POST['phone_office'];
            $nurse_ym = $_POST['nurse_ym'];
            $workplace_address = $_POST['workplace_address'];
            $estimated_due_date = $_POST['estimated_due_date'];
            $revised_due_date = $_POST['revised_due_date'];
            $gravida = $_POST['gravida'];
            $para = $_POST['para'];
            $husband_name = $_POST['husband_name'];
            $husband_id_card_number = $_POST['husband_id_card_number'];
            $husband_occupation = $_POST['husband_occupation'];
            $husband_workplace_address = $_POST['husband_workplace_address'];
            $husband_phone_residential = $_POST['husband_phone_residential'];
            $husband_phone_mobile = $_POST['husband_phone_mobile'];
            $postnatal_address_1 = $_POST['postnatal_address_1'];
            $postnatal_address_2 = $_POST['postnatal_address_2'];
           $postnatal_address_3 = $_POST['postnatal_address_3'];
          $risk_factors = $_POST['risk_factors'];
            
           // Update query
          $query_update = "UPDATE mother_information SET 
          registration_number = ?, id_card_number = ?, date_of_birth = ?, age = ?, clinic_phone_number = ?,
          jkn_serial_number = ?, antenatal_color_code = ?, ethnic_group = ?, nationality = ?, education_level = ?,
          occupation = ?, home_address_1 = ?, home_address_2 = ?, phone_residential = ?, phone_mobile = ?,
          phone_office = ?, nurse_ym = ?, workplace_address = ?, estimated_due_date = ?, revised_due_date = ?,
          gravida = ?, para = ?, husband_name = ?, husband_id_card_number = ?, husband_occupation = ?,
          husband_workplace_address = ?, husband_phone_residential = ?, husband_phone_mobile = ?,
          postnatal_address_1 = ?, postnatal_address_2 = ?, postnatal_address_3 = ?, risk_factors = ?
          WHERE patient_id = ?";
  
      $stmt_update = $conn->prepare($query_update);
      $stmt_update->bind_param("sssissssssssssssssssiisssssssssssi",
          $registration_number, $id_card_number, $date_of_birth, $age, $clinic_phone_number,
          $jkn_serial_number, $antenatal_color_code, $ethnic_group, $nationality, $education_level,
          $occupation, $home_address_1, $home_address_2, $phone_residential, $phone_mobile,
          $phone_office, $nurse_ym, $workplace_address, $estimated_due_date, $revised_due_date,
          $gravida, $para, $husband_name, $husband_id_card_number, $husband_occupation,
          $husband_workplace_address, $husband_phone_residential, $husband_phone_mobile,
          $postnatal_address_1, $postnatal_address_2, $postnatal_address_3, $risk_factors,
          $patient_id
      );
  
      if ($stmt_update->execute()) {
          $success_message = "Data updated successfully!";
      } else {
          $error_message = "Error updating data: " . $stmt_update->error;
      }
    
      break;
      

     case 'past_pregnancy_history':
                $patient_id = $_POST['patient_id'];
                $year = $_POST['year'];
                $outcome = $_POST['outcome'];
                $delivery_type = $_POST['delivery_type'];
                $place_and_attendant = $_POST['place_and_attendant'];
                $gender = $_POST['gender'];
                $birth_weight = $_POST['birth_weight'];
                $breastfeeding_info = $_POST['breastfeeding_info'];
                $current_condition = $_POST['current_condition'];
                

                 $query_insert = "INSERT INTO past_pregnancy_history 
                 (patient_id, year, outcome, delivery_type, place_and_attendant, gender, birth_weight, breastfeeding_info, current_condition) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt_insert = $conn->prepare($query_insert);

                $stmt_insert->bind_param("iissssdds",
                    $patient_id, $year, $outcome, $delivery_type, $place_and_attendant, $gender,
                    $birth_weight, $breastfeeding_info, $current_condition
                );


        if ($stmt_insert->execute()) {
            $success_message = "Data added successfully!";
        } else {
            $error_message = "Error adding data: " . $stmt_insert->error;
        }
            
            break;

            case 'family_health_history':
                $patient_id = $_POST['patient_id'];
                $diabetes = $_POST['diabetes'];
                $hypertension = $_POST['hypertension'];
                $heart_disease = $_POST['heart_disease'];
                $genetic_disorder = $_POST['genetic_disorder'];
                $others = $_POST['others'];
            
                $query_insert = "INSERT INTO family_health_history 
                (patient_id, diabetes, hypertension, heart_disease, genetic_disorder, others) 
                VALUES (?, ?, ?, ?, ?, ?)";
            
                $stmt_insert = $conn->prepare($query_insert);
                $stmt_insert->bind_param("isssss", $patient_id, $diabetes, $hypertension, $heart_disease, $genetic_disorder, $others);
            
                if ($stmt_insert->execute()) {
                    $success_message = "Family health history added successfully!";
                } else {
                    $error_message = "Error adding family health history: " . $stmt_insert->error;
                }
            
                break;
               
        // Add more cases for other subsections like 'family_health_history'
    }
    // Redirect or display success/error message
    $_SESSION['success_message'] = $success_message;
    $_SESSION['error_message'] = $error_message;
    header("Location: doctorNurse_view_subsection_health_record.php?patient_id=$patient_id&section=$section");

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
<title>edit health record</title>

<style>
     .btn-pink { background-color: #f78da7; color: white; }
        .btn-pink {
            background-color: #f78da7;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-pink:hover {
            background-color: #f55d83;
        }
</style>
</head>


 <body>
 <div class="main-content">
 <main>
 <h2 class="mb-4"><?= ucfirst(str_replace('_', ' ', $section)) ?> - Add/ Edit</h2>

<!-- Form for Update/Add Data -->
    <?php if ($section == 'basic_info'): ?>
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <form action="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=<?= $section ?>&record_id=<?= $record_id ?>" method="POST">
   
        <div class="mb-3">
            <label for="registration_number" class="form-label">Registration Number</label>
            <input type="text" name="registration_number" class="form-control" value="<?= $data['registration_number'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ID Card Number</label>
            <input type="text" name="id_card_number" class="form-control" value="<?= $data['id_card_number'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?= $data['date_of_birth'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
        <label class="form-label">Age</label>
        <input type="number" name="age" class="form-control" value="<?= $data['age'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Clinic Phone Number</label>
        <input type="text" name="clinic_phone_number" class="form-control" value="<?= $data['clinic_phone_number'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">JKN Serial Number</label>
        <input type="text" name="jkn_serial_number" class="form-control" value="<?= $data['jkn_serial_number'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Antenatal Color Code</label>
        <input type="text" name="antenatal_color_code" class="form-control" value="<?= $data['antenatal_color_code'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Ethnic Group</label>
        <input type="text" name="ethnic_group" class="form-control" value="<?= $data['ethnic_group'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Nationality</label>
        <input type="text" name="nationality" class="form-control" value="<?= $data['nationality'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Education Level</label>
        <input type="text" name="education_level" class="form-control" value="<?= $data['education_level'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Occupation</label>
        <input type="text" name="occupation" class="form-control" value="<?= $data['occupation'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Home Address 1</label>
        <textarea name="home_address_1" class="form-control"><?= $data['home_address_1'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Home Address 2</label>
        <textarea name="home_address_2" class="form-control"><?= $data['home_address_2'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Phone (Residential)</label>
        <input type="text" name="phone_residential" class="form-control" value="<?= $data['phone_residential'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Phone (Mobile)</label>
        <input type="text" name="phone_mobile" class="form-control" value="<?= $data['phone_mobile'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Phone (Office)</label>
        <input type="text" name="phone_office" class="form-control" value="<?= $data['phone_office'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Nurse YM</label>
        <input type="text" name="nurse_ym" class="form-control" value="<?= $data['nurse_ym'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Workplace Address</label>
        <textarea name="workplace_address" class="form-control"><?= $data['workplace_address'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Estimated Due Date</label>
        <input type="date" name="estimated_due_date" class="form-control" value="<?= $data['estimated_due_date'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Revised Due Date</label>
        <input type="date" name="revised_due_date" class="form-control" value="<?= $data['revised_due_date'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Gravida</label>
        <input type="number" name="gravida" class="form-control" value="<?= $data['gravida'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Para</label>
        <input type="number" name="para" class="form-control" value="<?= $data['para'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Husband Name</label>
        <input type="text" name="husband_name" class="form-control" value="<?= $data['husband_name'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Husband ID Card Number</label>
        <input type="text" name="husband_id_card_number" class="form-control" value="<?= $data['husband_id_card_number'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Husband Occupation</label>
        <input type="text" name="husband_occupation" class="form-control" value="<?= $data['husband_occupation'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Husband Workplace Address</label>
        <textarea name="husband_workplace_address" class="form-control"><?= $data['husband_workplace_address'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Husband Phone (Residential)</label>
        <input type="text" name="husband_phone_residential" class="form-control" value="<?= $data['husband_phone_residential'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Husband Phone (Mobile)</label>
        <input type="text" name="husband_phone_mobile" class="form-control" value="<?= $data['husband_phone_mobile'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Postnatal Address 1</label>
        <textarea name="postnatal_address_1" class="form-control"><?= $data['postnatal_address_1'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Postnatal Address 2</label>
        <textarea name="postnatal_address_2" class="form-control"><?= $data['postnatal_address_2'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Postnatal Address 3</label>
        <textarea name="postnatal_address_3" class="form-control"><?= $data['postnatal_address_3'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Risk Factors</label>
        <textarea name="risk_factors" class="form-control"><?= $data['risk_factors'] ?? '' ?></textarea>
    </div>

        <button type="submit" class="btn btn-pink">Add</button>
    </form>

<?php elseif ($section == 'past_pregnancy_history'): ?>
    
    <form action="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=<?= $section ?>&record_id=<?= $record_id ?>" method="POST">
    <input type="hidden" name="patient_id" value="<?= $patient_id ?>">

    <div class="mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="<?= $data['year'] ?? '' ?>"required>
    </div>
    <div class="mb-3">
        <label class="form-label">Outcome</label>
        <input type="text" name="outcome" class="form-control" value="<?= $data['outcome'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Delivery Type</label>
        <input type="text" name="delivery_type" class="form-control" value="<?= $data['delivery_type'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Place and Attendant</label>
        <textarea name="place_and_attendant" class="form-control"value="<?= $data['place_and_attendant'] ?? '' ?>"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Gender</label>
        <input type="text" name="gender" class="form-control"value="<?= $data['gender'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Birth Weight (kg)</label>
        <input type="number" step="0.01" name="birth_weight" class="form-control"value="<?= $data['birth_weight'] ?? '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Breastfeeding Info</label>
        <textarea name="breastfeeding_info" class="form-control"value="<?= $data['breastfeeding_info'] ?? '' ?>"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Current Condition</label>
        <textarea name="current_condition" class="form-control"value="<?= $data['current_condition'] ?? '' ?>"></textarea>
    </div>
    

        <button type="submit" class="btn btn-pink">Add Pregnancy History</button>
    </form>

    <?php elseif ($section == 'family_health_history'): ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <form action="doctorNurse_edit_subsection_health_record.php?patient_id=<?= $patient_id ?>&section=<?= $section ?>&record_id=<?= $record_id ?>" method="POST">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">

        <div class="mb-3">
            <label for="diabetes" class="form-label">Diabetes</label>
            <input type="text" name="diabetes" class="form-control" value="<?= $data['diabetes'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="hypertension" class="form-label">Hypertension</label>
            <input type="text" name="hypertension" class="form-control" value="<?= $data['hypertension'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="heart_disease" class="form-label">Heart Disease</label>
            <input type="text" name="heart_disease" class="form-control" value="<?= $data['heart_disease'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="genetic_disorder" class="form-label">Genetic Disorder</label>
            <input type="text" name="genetic_disorder" class="form-control" value="<?= $data['genetic_disorder'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="others" class="form-label">Other Conditions</label>
            <input type="text" name="others" class="form-control" value="<?= $data['others'] ?? '' ?>">
        </div>

        <button type="submit" class="btn btn-pink">Save</button>
    </form>


<?php endif; ?>
</div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>     

</html>
