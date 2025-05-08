<?php
require_once "../config.php";
session_start();

// Check if admin
if ($_SESSION['role_id'] != 4) {
    header("Location: ../login/login.php");
    exit();
}

// Handle approval
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    $_SESSION['success_message'] = "User approved successfully!";
    header("Location: admin_manage_user.php");
    exit();
}

// Handle rejection
if (isset($_GET['reject_id'])) {
    $reject_id = intval($_GET['reject_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $reject_id);
    $stmt->execute();
    $_SESSION['success_message'] = "User rejected and removed successfully.";
    header("Location: admin_manage_user.php");
    exit();
}


// Fetch unapproved users
$result = $conn->query("SELECT id, name, email, role_id FROM users WHERE is_approved = 0");
$pendingCount = 0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Approval</title>
    <link rel="stylesheet" href="../css/mainStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        
        h2 {
            color: #f06292;
            text-align: center;
            margin-top: 30px;
            font-size: 2rem;
        }
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #ffffff;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f8bbd0;
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #fce4ec;
        }
        a.approve-btn {
            background-color: #f06292;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin: 2px;
        }
        a.approve-btn:hover {
            background-color: #c2185b;
        }
        a.reject-btn {
            background-color: #e57373;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin: 2px;
        }
        a.reject-btn:hover {
            background-color: #c62828;
        }
        .alert-message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
            color: #f06292;
        }
        @media (max-width: 768px) {
            table {
                width: 95%;
            }
            a.approve-btn, a.reject-btn {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }

    </style>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    
    <main>
        <h2>Pending Approvals</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-message"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= ['Patient', 'Doctor', 'Nurse', 'Admin'][$row['role_id'] - 1] ?></td>
                    <td>
                        <a href="?approve_id=<?= $row['id'] ?>" class="approve-btn">Approve</a>
                        <a href="?reject_id=<?= $row['id'] ?>" class="reject-btn" onclick="return confirm('Are you sure you want to reject this user?');">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="alert-message">No users pending approval.</p>
    <?php endif; ?>

    </main>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php include('../includes/scripts.php'); ?>
</body>
    

</html>
