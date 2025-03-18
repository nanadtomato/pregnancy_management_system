<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($patient = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $patient['name']; ?></td>
            <td><?php echo $patient['phone']; ?></td>
            <td>
                <a href="edit_patient.php?id=<?php echo $patient['id']; ?>">Edit</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</html>