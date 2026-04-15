<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$record_id = $_GET['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Health Record | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }
        .warning-message {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">🗑️ Delete Health Record</h1>
            <p style="color: #64748b; font-size: 1rem;">Permanently delete a health record.</p>
        </div>
        
        <div class="form-container">
            <div class="warning-message">
                ⚠️ Warning: This action cannot be undone!
            </div>
            <form method="POST" action="confirm_delete.php" onsubmit="return confirm('Are you sure you want to delete this record? This action cannot be undone.');">
                <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record_id); ?>">
                <p style="color: var(--text-secondary); margin-bottom: 20px;">Record ID: <?php echo htmlspecialchars($record_id); ?></p>
                <button type="submit" class="btn btn-danger" style="width: 100%;">Delete Record</button>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
