<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report | PetCare</title>
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
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📈 Generate Report</h1>
            <p style="color: #64748b; font-size: 1rem;">Generate reports for pets under your care.</p>
        </div>
        
        <div class="form-container">
            <form method="POST" action="create_report.php">
                <div class="form-group">
                    <label for="report_type">Report Type</label>
                    <select id="report_type" name="report_type" required>
                        <option value="">Select Report Type</option>
                        <option value="vaccination">Vaccination Report</option>
                        <option value="health">Health Records Report</option>
                        <option value="visits">Clinic Visits Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Generate</button>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
