<?php
session_start();
// Include database connection configuration
include('../config/db.php');

// Check if user is logged in and has 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$message = '';

// 2. Handle Delete Vet Logic
if (isset($_POST['delete_vet'])) {
    $vet_id = mysqli_real_escape_string($conn, $_POST['vet_id']);
    
    if (!empty($vet_id) && is_numeric($vet_id)) {
        // Start a transaction for safe deletion
        mysqli_begin_transaction($conn);
        $success = true;

        try {
            // Delete associated record from vet_details first
            $delete_vet_details = mysqli_query($conn, "DELETE FROM vet_details WHERE user_id='$vet_id'");
            
            if (!$delete_vet_details) {
                throw new Exception(mysqli_error($conn));
            }

            // Then delete the user account
            $delete_user = mysqli_query($conn, "DELETE FROM users WHERE id='$vet_id' AND role='vet'");
            
            if (!$delete_user) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);
            $message = "🗑️ Veterinarian deleted successfully!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "❌ Error deleting veterinarian: " . $e->getMessage();
        }
    } else {
        $message = "❌ Invalid Veterinarian ID for deletion.";
    }
}

// 3. Fetch Veterinarian Data using JOIN to get specialization
$result = mysqli_query($conn, "
    SELECT 
        u.id, 
        u.username AS name, 
        u.email, 
        vd.specialization 
    FROM users u
    LEFT JOIN vet_details vd ON u.id = vd.user_id 
    WHERE u.role = 'vet' 
    ORDER BY u.id ASC
");

// Check for query execution errors
if (!$result) {
    // If vet_details doesn't exist, this error will show
    die("Database query failed. Ensure the 'vet_details' table exists. Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Veterinarians | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .message {
            text-align: center;
            padding: 12px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
            border: 1px solid #e9d5ff;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🩺 Manage Veterinarians</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View and manage veterinarian accounts.</p>
        </div>
        
        <?php if (!empty($message)) echo "<div class='message " . (strpos($message, '❌') !== false ? 'error' : 'success') . "'>$message</div>"; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) { 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['specialization'] ?? 'N/A') ?></td>
                        <td class="actions">
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                            
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete Dr. <?= htmlspecialchars($row['name']) ?>? This action cannot be undone.');" style="display: inline;">
                                <input type="hidden" name="vet_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_vet" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>No veterinarians found in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>