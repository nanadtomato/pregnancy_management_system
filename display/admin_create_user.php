<?php
require_once "../config.php";
session_start();

// Check if admin
if ($_SESSION['role_id'] != 4) {
    header("Location: ../login/login.php");
    exit();
}
// Form submission logic
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name               = trim($_POST['name']);
    $email              = trim($_POST['email']);
    $role_id            = (int)$_POST['role'];
    $password           = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone              = trim($_POST['phone']);
    $address            = trim($_POST['address']);
    $date_of_birth      = $_POST['date_of_birth'];
    $identification_number = trim($_POST['identification_number']);
    $is_approved        = 1; // Auto-approve for admin-created users
    $last_menstrual_date = isset($_POST['last_menstrual_date']) ? $_POST['last_menstrual_date'] : null;
    $license_number     = isset($_POST['license_number']) ? trim($_POST['license_number']) : null;
    $nurse_license_number = isset($_POST['nurse_license_number']) ? trim($_POST['nurse_license_number']) : null;

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, date_of_birth, identification_number, role_id, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssi", $name, $email, $password, $phone, $address, $date_of_birth, $identification_number, $role_id, $is_approved);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into role-specific tables
            if ($role_id == 1) { // Patient
                $insertPatient = $conn->prepare("INSERT INTO patients (user_id, last_menstrual_date) VALUES (?, ?)");
                $insertPatient->bind_param("is", $user_id, $last_menstrual_date);
                $insertPatient->execute();
                $insertPatient->close();
            } elseif ($role_id == 2) { // Doctor
                $insertDoctor = $conn->prepare("INSERT INTO doctors (user_id, license_number) VALUES (?, ?)");
                $insertDoctor->bind_param("is", $user_id, $license_number);
                $insertDoctor->execute();
                $insertDoctor->close();
            } elseif ($role_id == 3) { // Nurse
                $insertNurse = $conn->prepare("INSERT INTO nurses (user_id, nurse_license_number) VALUES (?, ?)");
                $insertNurse->bind_param("is", $user_id, $nurse_license_number);
                $insertNurse->execute();
                $insertNurse->close();
            }

            $success = "User created successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create User</title>
    <link rel="stylesheet" href="../css/mainStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        
        h2 {
            color: #f06292;
            text-align: center;
            margin-top: 30px;
            font-size: 2rem;
        }
        .form-container {
            width: 80%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #f06292;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #c2185b;
        }
        .message {
            text-align: center;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .success {
            color: #4caf50;
        }
        .error {
            color: #e53935;
        }

    </style>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    
    <main>
    <h2>Create New User</h2>

    <div class="form-container">
            <?php if (isset($success)) echo "<p class='message success'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='message error'>$error</p>"; ?>

            <form method="POST" action="">
                <label>Full Name:</label>
                <input type="text" name="name" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <label>Phone Number:</label>
                <input type="tel" name="phone" required>

                <label>Address:</label>
                <input type="text" name="address" required>

                <label>Date of Birth:</label>
                <input type="date" name="date_of_birth" required>

                <label>Identification Number:</label>
                <input type="text" name="identification_number" required>

                <label>Role:</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="1">Pregnant Mom</option>
                    <option value="2">Doctor</option>
                    <option value="3">Nurse</option>
                </select>

                <!-- Patient-specific -->
                <div id="patient-fields" style="display: none;">
                    <label>Last Menstrual Date:</label>
                    <input type="date" name="last_menstrual_date">
                </div>

                <!-- Doctor-specific -->
                <div id="doctor-fields" style="display: none;">
                    <label>License Number:</label>
                    <input type="text" name="license_number">
                </div>

                <!-- Nurse-specific -->
                <div id="nurse-fields" style="display: none;">
                    <label>Nurse License Number:</label>
                    <input type="text" name="nurse_license_number">
                </div>

                <input type="submit" value="Create User">
            </form>
        </div>


    </main>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    const roleSelect = document.querySelector('select[name="role"]');
    const patientFields = document.getElementById('patient-fields');
    const doctorFields = document.getElementById('doctor-fields');
    const nurseFields = document.getElementById('nurse-fields');

    roleSelect.addEventListener('change', function() {
        patientFields.style.display = 'none';
        doctorFields.style.display = 'none';
        nurseFields.style.display = 'none';

        if (this.value == '1') {
            patientFields.style.display = 'block';
        } else if (this.value == '2') {
            doctorFields.style.display = 'block';
        } else if (this.value == '3') {
            nurseFields.style.display = 'block';
        }
    });
</script>


    <?php include('../includes/scripts.php'); ?>
</body>
    

</html>
