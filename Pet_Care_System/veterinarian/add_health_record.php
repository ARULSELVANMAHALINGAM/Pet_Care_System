<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

include('../config/db.php');
$vet_id = $_SESSION['user_id'];

// Fetch pets for dropdown
$pets_query = mysqli_query($conn, "SELECT id, name, species FROM pets");

// Fetch health records for this vet
$records_query = mysqli_query($conn, 
    "SELECT hr.*, p.name AS pet_name, p.species 
     FROM health_records hr 
     JOIN pets p ON hr.pet_id = p.id 
     WHERE hr.vet_id = '$vet_id' 
     ORDER BY hr.date DESC, hr.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Health Record | PetCare</title>
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
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">➕ Add Health Record</h1>
            <p style="color: #64748b; font-size: 1rem;">Add a new health record for a pet.</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                Health record added successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="save_health_record.php">
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <?php 
                        mysqli_data_seek($pets_query, 0);
                        while($pet = mysqli_fetch_assoc($pets_query)) { ?>
                            <option value="<?= $pet['id'] ?>"><?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Enter diagnosis" required></textarea>
                </div>
                <div class="form-group">
                    <label for="treatment">Treatment Plan</label>
                    <textarea id="treatment" name="treatment" rows="3" placeholder="Enter treatment plan" required></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <input type="hidden" name="vet_id" value="<?= $vet_id ?>">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add Record</button>
            </form>
        </div>
        
        <!-- Health Records List -->
        <div class="card" style="margin-top: 30px;">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">📋 Existing Health Records</h2>
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
                            <td><?= htmlspecialchars(substr($row['diagnosis'], 0, 50)) ?><?= strlen($row['diagnosis']) > 50 ? '...' : '' ?></td>
                            <td><?= htmlspecialchars(substr($row['treatment'], 0, 50)) ?><?= strlen($row['treatment']) > 50 ? '...' : '' ?></td>
                            <td class="actions" style="display: flex; gap: 8px; justify-content: center;">
                                <a href="update_health_record.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_health_record.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                                No health records found. Add your first record above!
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
