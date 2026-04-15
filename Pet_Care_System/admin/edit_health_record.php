<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$record_id = $_GET['id'] ?? null;
$error = '';
$message = '';

if (empty($record_id) || !is_numeric($record_id)) {
    header("Location: manage_records.php?error=" . urlencode("Invalid record ID."));
    exit;
}

// Fetch health record data
$record_query = mysqli_query($conn, "
    SELECT hr.*, p.name AS pet_name 
    FROM health_records hr
    JOIN pets p ON hr.pet_id = p.id
    WHERE hr.id = '$record_id'
");
if (!$record_query || mysqli_num_rows($record_query) == 0) {
    header("Location: manage_records.php?error=" . urlencode("Health record not found."));
    exit;
}
$record = mysqli_fetch_assoc($record_query);

// Fetch pets for dropdown
$pets_query = mysqli_query($conn, "SELECT id, name FROM pets");
$vets_query = mysqli_query($conn, "SELECT id, username FROM users WHERE role='vet'");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_id = mysqli_real_escape_string($conn, $_POST['pet_id'] ?? '');
    $vet_id = mysqli_real_escape_string($conn, $_POST['vet_id'] ?? '');
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis'] ?? '');
    $date = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
    
    if (empty($pet_id) || empty($diagnosis) || empty($date)) {
        $error = "Pet, Diagnosis, and Date are required.";
    } else {
        $vet_id = empty($vet_id) ? 'NULL' : "'$vet_id'";
        $update_sql = "UPDATE health_records SET pet_id='$pet_id', vet_id=$vet_id, diagnosis='$diagnosis', date='$date' WHERE id='$record_id'";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: manage_records.php?success=" . urlencode("Health record updated successfully!"));
            exit;
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['success'])) {
    $message = rawurldecode($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Health Record | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
            border: 1px solid #e9d5ff;
            max-width: 600px;
            margin: 0 auto;
        }
        .message {
            text-align: center;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">✏️ Edit Health Record</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Update health record information.</p>
        </div>
        
        <?php if ($message) echo "<div class='message success'>$message</div>"; ?>
        <?php if ($error) echo "<div class='message error'>$error</div>"; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="pet_id">Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <?php 
                        mysqli_data_seek($pets_query, 0);
                        while($pet = mysqli_fetch_assoc($pets_query)): 
                        ?>
                            <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $record['pet_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pet['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="vet_id">Veterinarian (Optional)</label>
                    <select id="vet_id" name="vet_id">
                        <option value="">-- Select Vet --</option>
                        <?php 
                        mysqli_data_seek($vets_query, 0);
                        while($vet = mysqli_fetch_assoc($vets_query)): 
                        ?>
                            <option value="<?= $vet['id'] ?>" <?= $vet['id'] == $record['vet_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($vet['username']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="4" required><?= htmlspecialchars($record['diagnosis']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($record['date']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                <a href="manage_records.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; display: block; text-align: center;">Cancel</a>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>

