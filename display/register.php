<?php
require_once "../config.php";

$error = ""; // Initialize error variable
$success = ""; // Initialize success variable
$role_id = isset($_POST['role_id']) ? $_POST['role_id'] : ''; // Get selected role

session_start();

// Clear error message if set
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear it after displaying
}

// Clear success message if set
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear it after displaying
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Collect input values
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'] ?? null;
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $identification_number = $_POST['identification_number'] ?? null;
    $role_id = $_POST['role_id'] ?? null;

    // Role-specific fields
    $last_menstrual_date = $_POST['last_menstrual_date'] ?? null;
    $license_number = $_POST['license_number'] ?? null;
    $nurse_license_number = $_POST['nurse_license_number'] ?? null;

    // Validate required fields
    if (!empty($name) && !empty($email) && !empty($password) && !empty($role_id)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error_message'] = "Email already exists. Please use a different email.";
            header("Location: register.php"); // Redirect back to registration page
            exit();
        } else {
            // Proceed with insertion
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the `users` table
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, phone, address, date_of_birth, identification_number, role_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssssi", $name, $email, $hashed_password, $phone, $address, $date_of_birth, $identification_number, $role_id);

            if ($stmt->execute()) {
                // Set success message and redirect to login page
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: login.php"); // Redirect to the login page
                exit();
            } else {
                $_SESSION['error_message'] = "Failed to register user. Please try again.";
                header("Location: register.php"); // Redirect back to registration page
                exit();
            }
            
        }
    } else {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: register.php"); // Redirect back to registration page
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <h1>Pregnancy Management System</h1>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <!-- Role Selection -->
        <form method="POST" class="register-form">
            <h2 style="text-align: center; color: #333;">Create Account</h2>

            <div class="role-selection">
                <button type="button" onclick="setRole(1)">Patient</button>
                <button type="button" onclick="setRole(2)">Doctor</button>
                <button type="button" onclick="setRole(3)">Nurse</button>
                <button type="button" onclick="setRole(4)">Admin</button>
            </div>

            <input type="hidden" name="role_id" id="role_id" value="<?php echo htmlspecialchars($role_id); ?>">

            <!-- Common Fields -->
            <label for="name">Name:</label>
            <input type="text" name="name" placeholder="Your Name" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="user@email.com" required>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" placeholder="Your Phone Number" required>

            <label for="address">Address:</label>
            <input type="text" name="address" placeholder="Your Address" required>

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" name="date_of_birth" required>

            <label for="identification_number">Identification Number:</label>
            <input type="text" name="identification_number" placeholder="e.g., IC Number or Passport Number" required>

            <!-- Role-Specific Fields -->
            <div id="patientFields" style="display:none;">
                <label for="last_menstrual_date">Last Menstrual Date:</label>
                <input type="date" name="last_menstrual_date">
            </div>

            <div id="doctorFields" style="display:none;">
                <label for="license_number">Medical License Number:</label>
                <input type="text" name="license_number">
            </div>

            <div id="nurseFields" style="display:none;">
                <label for="nurse_license_number">Nurse License Number:</label>
                <input type="text" name="nurse_license_number">
            </div>

            <!-- Submit Button -->
            <input type="submit" name="submit" id="submitBtn" value="Register" class="btn btn-green">
            <p class="login-link">Already have an account? <a href="login.php">Log in here</a>.</p>
        </form>
    </div>

    <!-- JavaScript to handle role selection -->
    <script>
        function setRole(roleId) {
            document.getElementById('role_id').value = roleId;

            // Show relevant fields based on selected role
            document.getElementById('patientFields').style.display = (roleId == 1) ? 'block' : 'none';
            document.getElementById('doctorFields').style.display = (roleId == 2) ? 'block' : 'none';
            document.getElementById('nurseFields').style.display = (roleId == 3) ? 'block' : 'none';

            // Change button text based on selected role
            const roleNames = ['User', 'Patient', 'Doctor', 'Nurse', 'Admin'];
            document.getElementById('submitBtn').value = `Register as ${roleNames[roleId]}`; // Update button text
        }

        // Show alert on successful registration if session variable is set
        window.onload = function() {
           <?php if (!empty($success)): ?>
               alert("<?php echo addslashes($success); ?>");
           <?php endif; ?>
       };
    </script>
</body>
</html>
