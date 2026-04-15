<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];

// Fetch health records for pets assigned to this vet
$records_query = mysqli_query($conn, 
    "SELECT hr.*, p.name AS pet_name, p.species, u.username AS vet_name 
     FROM health_records hr 
     JOIN pets p ON hr.pet_id = p.id 
     JOIN users u ON hr.vet_id = u.id 
     WHERE hr.vet_id = '$vet_id' 
     ORDER BY hr.date DESC, hr.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet History | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📜 Pet History</h1>
            <p style="color: #64748b; font-size: 1rem;">View complete medical history for pets under your care.</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                Record deleted successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Date</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($records_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($records_query)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['pet_name']) ?> (<?= htmlspecialchars($row['species']) ?>)</td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                            <td><?= htmlspecialchars($row['treatment']) ?></td>
                            <td class="actions" style="display: flex; gap: 8px; justify-content: center;">
                                <a href="update_health_record.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_health_record.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                                No health records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
