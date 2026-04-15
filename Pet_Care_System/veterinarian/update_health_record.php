<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];
$record_id = $_GET['id'] ?? '';

// Fetch existing health record data
$record = null;
if (!empty($record_id)) {
    $record_id_escaped = mysqli_real_escape_string($conn, $record_id);
    $record_query = mysqli_query($conn, "SELECT * FROM health_records WHERE id = '$record_id_escaped' AND vet_id = '$vet_id'");
    if ($record_query && mysqli_num_rows($record_query) > 0) {
        $record = mysqli_fetch_assoc($record_query);
    }
}

// Show error if record not found
if (empty($record_id) || !$record) {
    $error_message = "Health record not found or you do not have permission to edit it.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Health Record | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">✏️ Update Health Record</h1>
            <p style="color: #64748b; font-size: 1rem;">Update health record information.</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="card" style="background: #fee; border-color: #fcc; color: #c33; padding: 20px; margin-bottom: 20px;">
                <p><?= htmlspecialchars($error_message) ?></p>
                <a href="view_pet_history.php" class="btn btn-secondary">Back to Pet History</a>
            </div>
        <?php else: ?>
        <div class="form-container">
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    Health record updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="save_health_update.php">
                <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record_id); ?>">
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Enter diagnosis" required><?= $record ? htmlspecialchars($record['diagnosis']) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="treatment">Treatment</label>
                    <textarea id="treatment" name="treatment" rows="3" placeholder="Enter treatment plan" required><?= $record ? htmlspecialchars($record['treatment']) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?= $record ? htmlspecialchars($record['date']) : '' ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Record</button>
                <a href="view_pet_history.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px;">Cancel</a>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
