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
        
        <div class="form-container">
            <form method="POST" action="save_update.php">
                <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record_id); ?>">
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Updated Diagnosis"></textarea>
                </div>
                <div class="form-group">
                    <label for="treatment">Treatment</label>
                    <textarea id="treatment" name="treatment" rows="3" placeholder="Updated Treatment"></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Record</button>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
