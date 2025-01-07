<?php

class User {
    private $db; // Database connection
    private $token; // User token for session management
    public $id; // User ID
    public $firstName;
    public $lastName;
    public $email;
    public $role_id; // Role ID to determine user type

    // Constructor to initialize database connection
    public function __construct($database) {
        $this->db = $database; // Dependency injection of database object
    }

    // Check if the user is logged in
    public static function loggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Login method
    public function login($email, $password) {
        // Prepare and execute query to fetch user data
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email, role_id FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, md5($password)); // Hashing password for security
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch user data
            $stmt->bind_result($this->id, $this->firstName, $this->lastName, $this->email, $this->role_id);
            $stmt->fetch();

            // Set session variables
            $_SESSION['user_id'] = $this->id;
            $_SESSION['name'] = $this->firstName;
            $_SESSION['role_id'] = $this->role_id;

            return true; // Login successful
        } else {
            return false; // Login failed
        }
    }

    // Logout method
    public function logout() {
        session_destroy(); // Destroy session data
        header("Location: login.php"); // Redirect to login page
        exit();
    }

    // Method to get user's full name
    public function getFullName() {
        return "{$this->firstName} {$this->lastName}";
    }

    
    
    


}

?>
