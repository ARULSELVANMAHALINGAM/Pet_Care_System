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

// --- 2. Handle Delete Health Record Logic ---
if (isset($_POST['delete_record'])) {
    $record_id = mysqli_real_escape_string($conn, $_POST['record_id']);
    
    if (!empty($record_id) && is_numeric($record_id)) {
        $delete = mysqli_query($conn, "DELETE FROM health_records WHERE id='$record_id'");
        
        if ($delete) {
            $message = "🗑️ Health record deleted successfully!";
        } else {
            $message = "❌ Error deleting health record: " . mysqli_error($conn);
        }
    } else {
        $message = "❌ Invalid record ID for deletion.";
    }
}

// --- 3. Fetch Health Records Data ---
// Joins health_records with pets (to get pet name) and users (to get vet name)
$result = mysqli_query($conn, "
    SELECT 
        hr.id, 
        p.name AS pet_name, 
        hr.diagnosis, 
        hr.date, 
        u.username AS vet_name
    FROM health_records hr
    JOIN pets p ON hr.pet_id = p.id
    LEFT JOIN users u ON hr.vet_id = u.id
    ORDER BY hr.date DESC, hr.id DESC
");

// Check for query execution errors
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Health Records | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        /* Style for messages */
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
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">📋 Manage Health Records</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View and manage all health records in the system.</p>
        </div>
        
        <?php if (!empty($message)) echo "<div class='message " . (strpos($message, '❌') !== false ? 'error' : 'success') . "'>$message</div>"; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Diagnosis</th>
                        <th>Date</th>
                        <th>Vet</th>
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
                        <td><?= htmlspecialchars($row['pet_name']) ?></td>
                        <td><?= htmlspecialchars(substr($row['diagnosis'], 0, 40)) . (strlen($row['diagnosis']) > 40 ? '...' : '') ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['vet_name'] ?? 'N/A') ?></td>
                        <td class="actions">
                            <a href="edit_health_record.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                            
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this health record (ID: <?= $row['id'] ?>)?');" style="display: inline;">
                                <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_record" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center;'>No health records found in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>