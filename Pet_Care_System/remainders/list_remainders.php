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
    <title>List of Reminders | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📋 List of Reminders</h1>
            <p style="color: #64748b; font-size: 1rem;">Manage your pet care reminders and notifications.</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Rabies Vaccine</td>
                        <td>2025-11-20</td>
                        <td><span class="badge badge-warning">Upcoming</span></td>
                        <td class="actions">
                            <a href="update_reminder.php?id=1" class="btn btn-secondary">Edit</a>
                            <a href="delete_reminder.php?id=1" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
