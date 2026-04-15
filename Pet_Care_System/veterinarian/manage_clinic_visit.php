<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];

// Fetch clinic visits for this vet
$visits_query = mysqli_query($conn, 
    "SELECT cv.*, p.name AS pet_name, p.species 
     FROM clinic_visits cv 
     JOIN pets p ON cv.pet_id = p.id 
     WHERE cv.vet_id = '$vet_id' 
     ORDER BY cv.visit_date DESC, cv.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clinic Visits | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">🏥 Manage Clinic Visits</h1>
            <p style="color: #64748b; font-size: 1rem;">Schedule new visits or manage existing appointments.</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= $_GET['success'] == 'added' ? 'Clinic visit scheduled successfully!' : 'Visit deleted successfully!' ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Clinic Visit Form -->
        <div class="form-container" style="max-width: 600px; margin: 0 auto 30px; background: var(--bg-primary); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">➕ Schedule New Visit</h2>
            <form method="POST" action="save_clinic_visit.php">
                <?php 
                // Fetch pets for dropdown
                $pets_query = mysqli_query($conn, "SELECT id, name, species FROM pets");
                ?>
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <?php 
                        while($pet = mysqli_fetch_assoc($pets_query)) { ?>
                            <option value="<?= $pet['id'] ?>"><?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="visit_date">Visit Date</label>
                    <input type="date" id="visit_date" name="visit_date" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <input type="text" id="reason" name="reason" placeholder="e.g., Annual checkup, Vaccination" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Schedule Visit</button>
            </form>
        </div>
        
        <!-- Clinic Visits List -->
        <div class="card">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">📋 Existing Clinic Visits</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($visits_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($visits_query)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['pet_name']) ?> (<?= htmlspecialchars($row['species']) ?>)</td>
                            <td><?= htmlspecialchars($row['visit_date']) ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'Completed' ? 'badge-success' : ($row['status'] == 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="update_clinic_visit.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_clinic_visit.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this visit?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                                No clinic visits found.
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
