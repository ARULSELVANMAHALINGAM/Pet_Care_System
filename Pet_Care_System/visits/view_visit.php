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
    <title>View Visit Details | PetCare</title>
    <?php include('../includes/head.php'); ?>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">👁️ View Visit Details</h1>
            <p style="color: #64748b; font-size: 1rem;">View detailed information about clinic visits.</p>
        </div>
        
        <div class="card">
            <table>
                <tr><th>Visit ID</th><td>1</td></tr>
                <tr><th>Pet</th><td>Buddy</td></tr>
                <tr><th>Date</th><td>2025-11-10</td></tr>
                <tr><th>Reason</th><td>Vaccination</td></tr>
                <tr><th>Vet</th><td>Dr. Smith</td></tr>
                <tr><th>Status</th><td>Completed</td></tr>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
