<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];

// Fetch all pets
$pets_query = mysqli_query($conn, "SELECT id, name, species FROM pets");

// Get visit ID from URL
$visit_id = $_GET['id'] ?? '';

// Fetch existing visit data
$visit = null;
if (!empty($visit_id)) {
    $visit_id_escaped = mysqli_real_escape_string($conn, $visit_id);
    $visit_query = mysqli_query($conn, "SELECT * FROM clinic_visits WHERE id = '$visit_id_escaped' AND vet_id = '$vet_id'");
    if ($visit_query && mysqli_num_rows($visit_query) > 0) {
        $visit = mysqli_fetch_assoc($visit_query);
    }
}

// Show error if visit not found
if (empty($visit_id) || !$visit) {
    $error_message = "Clinic visit not found or you do not have permission to edit it.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Clinic Visit | PetCare</title>
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
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">✏️ Update Clinic Visit</h1>
            <p style="color: #64748b; font-size: 1rem;">Update clinic visit information.</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="card" style="background: #fee; border-color: #fcc; color: #c33; padding: 20px; margin-bottom: 20px;">
                <p><?= htmlspecialchars($error_message) ?></p>
                <a href="manage_clinic_visit.php" class="btn btn-secondary">Back to Clinic Visits</a>
            </div>
        <?php else: ?>
        <div class="form-container">
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    Clinic visit updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="save_clinic_visit_update.php">
                <input type="hidden" name="visit_id" value="<?= htmlspecialchars($visit_id); ?>">
                
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <?php 
                        mysqli_data_seek($pets_query, 0);
                        while($pet = mysqli_fetch_assoc($pets_query)) { ?>
                            <option value="<?= $pet['id'] ?>" <?= ($visit && $visit['pet_id'] == $pet['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="visit_date">Visit Date</label>
                    <input type="date" id="visit_date" name="visit_date" value="<?= $visit ? htmlspecialchars($visit['visit_date']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <input type="text" id="reason" name="reason" value="<?= $visit ? htmlspecialchars($visit['reason']) : '' ?>" placeholder="Enter reason" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Scheduled" <?= ($visit && $visit['status'] == 'Scheduled') ? 'selected' : '' ?>>Scheduled</option>
                        <option value="Completed" <?= ($visit && $visit['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= ($visit && $visit['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Visit</button>
                <a href="manage_clinic_visit.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px;">Cancel</a>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>

