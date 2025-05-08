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

// Fetch unapproved users
$result = $conn->query("SELECT id, name, email, role_id FROM users WHERE is_approved = 0");
?>

<h2>Pending Approvals</h2>
<?php if ($result->num_rows > 0): ?>
    <table>
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= ['Patient', 'Doctor', 'Nurse', 'Admin'][$row['role_id'] - 1] ?></td>
                <td><a href="?approve_id=<?= $row['id'] ?>">Approve</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No users pending approval.</p>
<?php endif; ?>
