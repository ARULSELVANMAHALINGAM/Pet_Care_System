<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Add Record
if (isset($_POST['add_record'])) {
    $pet_id = mysqli_real_escape_string($conn, $_POST['pet_id']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    $insert = mysqli_query($conn, "INSERT INTO health_records (pet_id, diagnosis, treatment, date) VALUES ('$pet_id', '$diagnosis', '$treatment', '$date')");
    $message = $insert ? "Health record added successfully!" : "Error adding record!";
}

// Handle Edit Record
if (isset($_POST['edit_record'])) {
    $id = $_POST['record_id'];
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    $update = mysqli_query($conn, "UPDATE health_records SET diagnosis='$diagnosis', treatment='$treatment', date='$date' WHERE id='$id'");
    $message = $update ? "Record updated successfully!" : "Error updating record!";
}

// Handle Delete Record
if (isset($_POST['delete_record'])) {
    $id = $_POST['record_id'];
    mysqli_query($conn, "DELETE FROM health_records WHERE id='$id'");
    $message = "Record deleted successfully!";
}

// Fetch records
$result = mysqli_query($conn, "SELECT hr.*, p.name AS pet_name FROM health_records hr JOIN pets p ON hr.pet_id = p.id ORDER BY hr.id DESC");

// Fetch pets for dropdown
$pets_query = mysqli_query($conn, "SELECT id, name FROM pets");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Health Records | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            max-width: 600px;
            margin-bottom: 30px;
        }
        
        .message {
            padding: 12px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .table-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow-x: auto;
        }
        
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .inline-form {
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Manage Health Records</h1>
            <p style="color: #64748b; font-size: 1rem;">Add, edit, or delete health records for pets.</p>
        </div>

        <?php if (!empty($message)) echo "<div class='message success'>$message</div>"; ?>

        <div class="form-container">
            <h2 style="margin-bottom: 20px; color: var(--text-primary);">Add New Health Record</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="pet_id">Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <?php 
                        // Reset pointer for pets query
                        mysqli_data_seek($pets_query, 0);
                        while($pet = mysqli_fetch_assoc($pets_query)) { ?>
                            <option value="<?= $pet['id'] ?>"><?= htmlspecialchars($pet['name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="treatment">Treatment</label>
                    <textarea id="treatment" name="treatment" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <button type="submit" name="add_record" class="btn btn-primary">Add Record</button>
            </form>
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 20px; color: var(--text-primary);">Health Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['pet_name']) ?></td>
                        <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($row['treatment']) ?></td>
                        <td><?= $row['date'] ?></td>
                        <td class="actions">
                            <form method="POST" class="inline-form" onsubmit="return confirm('Delete this record?');">
                                <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_record" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="../admin/dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
