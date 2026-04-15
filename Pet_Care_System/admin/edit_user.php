<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_GET['id'] ?? null;
$error = '';
$message = '';

if (empty($user_id) || !is_numeric($user_id)) {
    header("Location: manage_veterinarians.php?error=" . urlencode("Invalid user ID."));
    exit;
}

// Fetch user data
$user_query = mysqli_query($conn, "
    SELECT u.*, vd.specialization 
    FROM users u
    LEFT JOIN vet_details vd ON u.id = vd.user_id
    WHERE u.id = '$user_id' AND u.role = 'vet'
");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    header("Location: manage_veterinarians.php?error=" . urlencode("User not found."));
    exit;
}
$user = mysqli_fetch_assoc($user_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization'] ?? '');
    
    if (empty($username) || empty($email)) {
        $error = "Username and Email are required.";
    } else {
        // Update user
        $update_sql = "UPDATE users SET username='$username', email='$email' WHERE id='$user_id'";
        if (mysqli_query($conn, $update_sql)) {
            // Update or insert specialization
            $check_vet_details = mysqli_query($conn, "SELECT * FROM vet_details WHERE user_id='$user_id'");
            if (mysqli_num_rows($check_vet_details) > 0) {
                mysqli_query($conn, "UPDATE vet_details SET specialization='$specialization' WHERE user_id='$user_id'");
            } else {
                mysqli_query($conn, "INSERT INTO vet_details (user_id, specialization) VALUES ('$user_id', '$specialization')");
            }
            header("Location: manage_veterinarians.php?success=" . urlencode("Veterinarian updated successfully!"));
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
    <title>Edit Veterinarian | PetCare</title>
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
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">✏️ Edit Veterinarian</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Update veterinarian information.</p>
        </div>
        
        <?php if ($message) echo "<div class='message success'>$message</div>"; ?>
        <?php if ($error) echo "<div class='message error'>$error</div>"; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($user['specialization'] ?? '') ?>" placeholder="e.g., Small Animals, Surgery, etc.">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                <a href="manage_veterinarians.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; display: block; text-align: center;">Cancel</a>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>

