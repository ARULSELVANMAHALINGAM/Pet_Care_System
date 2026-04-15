<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pet_id = $_GET['id'] ?? null;
$error = '';
$message = '';

if (empty($pet_id) || !is_numeric($pet_id)) {
    header("Location: manage_pets.php?error=" . urlencode("Invalid pet ID."));
    exit;
}

// Fetch pet data
$pet_query = mysqli_query($conn, "SELECT * FROM pets WHERE id = '$pet_id'");
if (!$pet_query || mysqli_num_rows($pet_query) == 0) {
    header("Location: manage_pets.php?error=" . urlencode("Pet not found."));
    exit;
}
$pet = mysqli_fetch_assoc($pet_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $species = mysqli_real_escape_string($conn, $_POST['species'] ?? '');
    $breed = mysqli_real_escape_string($conn, $_POST['breed'] ?? '');
    $dob = mysqli_real_escape_string($conn, $_POST['dob'] ?? '');
    
    if (empty($name) || empty($species) || empty($breed) || empty($dob)) {
        $error = "All fields are required.";
    } else {
        $update_sql = "UPDATE pets SET name='$name', species='$species', breed='$breed', dob='$dob' WHERE id='$pet_id'";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: manage_pets.php?success=" . urlencode("Pet updated successfully!"));
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
    <title>Edit Pet | PetCare</title>
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
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">✏️ Edit Pet</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Update pet information.</p>
        </div>
        
        <?php if ($message) echo "<div class='message success'>$message</div>"; ?>
        <?php if ($error) echo "<div class='message error'>$error</div>"; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Pet Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($pet['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="species">Species</label>
                    <input type="text" id="species" name="species" value="<?= htmlspecialchars($pet['species']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($pet['dob']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                <a href="manage_pets.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; display: block; text-align: center;">Cancel</a>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>

