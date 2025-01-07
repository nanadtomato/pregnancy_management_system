<?php
require_once "../config.php";
session_start();

// Display success or error messages if available
if (isset($_SESSION['success_message'])) {
    echo "<p class='success'>" . htmlspecialchars($_SESSION['success_message']) . "</p>";
    unset($_SESSION['success_message']); // Clear it after displaying
}

if (isset($_SESSION['error_message'])) {
    echo "<p class='error'>" . htmlspecialchars($_SESSION['error_message']) . "</p>";
    unset($_SESSION['error_message']); // Clear it after displaying
}

$error = "";
$role_id = isset($_POST['role_id']) ? $_POST['role_id'] : ''; // Get the selected role ID

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($role_id)) {
        $error = "Please choose a role first.";
    } elseif (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND role_id = ?");
        $stmt->bind_param("si", $email, $role_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['role_id'] = $role_id;
                $_SESSION['userFirstName'] = $name;
                $_SESSION['userRole'] = ['Patient', 'Doctor', 'Nurse', 'Admin'][$role_id - 1];
                $_SESSION['userStatus'] = $role_id;

                switch ($role_id) {
                    case 1:
                        header("Location: ../display/patient_dashboard.php");
                        break;
                    case 2:
                        header("Location: ../display/doctor_dashboard.php");
                        break;
                    case 3:
                        header("Location: ../display/nurse_dashboard.php");
                        break;
                    case 4:
                        header("Location: ../display/admin_dashboard.php");
                        break;
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" class="login-form">
            <h2 style="text-align: center; color: #333;">Sign In</h2>

            <!-- Role Selection -->
            <div class="role-selection">
                <button type="button" onclick="setRole(1)">Patient</button>
                <button type="button" onclick="setRole(2)">Doctor</button>
                <button type="button" onclick="setRole(3)">Nurse</button>
                <button type="button" onclick="setRole(4)">Admin</button>
            </div>

            <!-- Hidden Input -->
            <input type="hidden" name="role_id" id="role_id" value="<?php echo htmlspecialchars($role_id); ?>">

            <!-- Email and Password -->
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="user@email.com" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="password" required>

            <!-- Submit Button -->
            <input type="submit" name="submit" id="submitBtn" 
                value="<?php echo !empty($role_id) 
                    ? 'Sign In (' . ['Patient', 'Doctor', 'Nurse', 'Admin'][$role_id - 1] . ')' 
                    : 'Sign In'; ?>" 
                class="btn btn-green">
              
            <p class="register-link">Don't have an account yet? <a href="register.php">Sign up here</a>.</p>
        </form>
    </div>
    <!-- Bootstrap Bundle JS (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- JavaScript -->
    <script>
        function setRole(roleId) {
            console.log("Role ID Selected: " + roleId); // Debug log
            document.getElementById('role_id').value = roleId; // Update hidden input
            const roleNames = ['Patient', 'Doctor', 'Nurse', 'Admin'];
            document.getElementById('submitBtn').value = 'Sign In (' + roleNames[roleId - 1] + ')'; // Update button
        }
    </script>
</body>
</html>
