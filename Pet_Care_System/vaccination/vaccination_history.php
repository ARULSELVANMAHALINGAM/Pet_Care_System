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
    <title>Vaccination History | PetCare</title>
    <?php include('../includes/head.php'); ?>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📜 Vaccination History</h1>
            <p style="color: #64748b; font-size: 1rem;">View complete vaccination history for all pets.</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr><th>ID</th><th>Pet</th><th>Vaccine</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Buddy</td><td>Distemper</td><td>2025-09-15</td><td><span class="badge badge-success">Completed</span></td></tr>
                    <tr><td>2</td><td>Buddy</td><td>Rabies</td><td>2024-11-20</td><td><span class="badge badge-success">Completed</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
