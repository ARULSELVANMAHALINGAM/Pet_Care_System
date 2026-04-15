<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Clinic Visit | PetCare</title>
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
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📅 Schedule Clinic Visit</h1>
            <p style="color: #64748b; font-size: 1rem;">Schedule a new clinic visit for your pet.</p>
        </div>
        
        <div class="form-container">
            <form method="POST" action="save_visit.php">
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <option value="1">Buddy (Dog)</option>
                        <option value="2">Kitty (Cat)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="visit_date">Visit Date</label>
                    <input type="date" id="visit_date" name="visit_date" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <input type="text" id="reason" name="reason" placeholder="Enter reason" required>
                </div>
                <div class="form-group">
                    <label for="vet_id">Select Veterinarian</label>
                    <select id="vet_id" name="vet_id" required>
                        <option value="">Select Veterinarian</option>
                        <option value="1">Dr. Smith</option>
                        <option value="2">Dr. Meena</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Schedule Visit</button>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
