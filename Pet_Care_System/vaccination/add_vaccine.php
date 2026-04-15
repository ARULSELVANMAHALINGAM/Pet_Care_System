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
    <title>Add Vaccine | PetCare</title>
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
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">➕ Add Vaccine</h1>
            <p style="color: #64748b; font-size: 1rem;">Add a new vaccination record for your pet.</p>
        </div>
        
        <div class="form-container">
            <form method="POST" action="save_vaccine.php">
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <option value="1">Buddy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vaccine_name">Vaccine Name</label>
                    <input type="text" id="vaccine_name" name="vaccine_name" placeholder="Enter vaccine name" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add Vaccine</button>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
