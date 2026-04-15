<?php
session_start();
include('../config/db.php'); // Ensure this path is correct

// 1. Authentication and Authorization Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'] ?? null;
$pet_id = $_GET['id'] ?? null;
$error = '';
$message = '';

// Check if PET ID is provided and is a valid number
if (empty($pet_id) || !is_numeric($pet_id)) {
    header("Location: add_pet.php?error=" . urlencode("Invalid pet ID provided for editing."));
    exit;
}

// 2. Fetch the specific pet's details (Ensure owner owns the pet)
// Use prepared statement for fetching to ensure security, though simple fetching is safer than INSERT/UPDATE
$sql_fetch = "SELECT id, name, species, breed, dob 
              FROM pets 
              WHERE id = ? AND owner_id = ?";
              
$stmt_fetch = mysqli_prepare($conn, $sql_fetch);
mysqli_stmt_bind_param($stmt_fetch, "ii", $pet_id, $owner_id);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$pet = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$pet) {
    header("Location: add_pet.php?error=" . urlencode("Pet not found or unauthorized access attempt."));
    exit;
}

// 3. Handle Form Submission (POST Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['pet_name'] ?? '');
    $new_species = trim($_POST['species'] ?? '');
    $new_breed = trim($_POST['breed'] ?? '');
    $new_dob = trim($_POST['dob'] ?? '');
    
    if (empty($new_name) || empty($new_species) || empty($new_breed) || empty($new_dob)) {
        $error = "All fields are required to update the pet details.";
    } else {
        // --- SECURE UPDATE USING PREPARED STATEMENT ---
        $update_sql = "UPDATE pets SET 
                       name = ?, 
                       species = ?, 
                       breed = ?, 
                       dob = ? 
                       WHERE id = ? AND owner_id = ?";
                       
        $stmt_update = mysqli_prepare($conn, $update_sql);
        
        // Bind parameters: 4 strings (name, species, breed, dob), 2 integers (pet_id, owner_id)
        mysqli_stmt_bind_param($stmt_update, "ssssii", 
                               $new_name, $new_species, $new_breed, $new_dob, 
                               $pet_id, $owner_id);
                       
        if (mysqli_stmt_execute($stmt_update)) {
            // Success: Redirect to self with success message to prevent resubmission
            header("Location: update_pet_details.php?id=$pet_id&success=" . urlencode("Pet details updated successfully!"));
            exit;
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_update);
    }
}

// Handle success messages after update (Need to refetch $pet data if successful POST occurred)
if (isset($_GET['success'])) {
    $message = rawurldecode($_GET['success']);
    // Since we redirected, the $pet data fetched at the top will be fresh.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet: <?= htmlspecialchars($pet['name']) ?></title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            max-width: 500px;
            margin: 30px auto;
        }
        /* Add CSS for messages here if they are not in your general head.php or styles */
        .message {
            text-align: center;
            padding: 12px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: green;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: red;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <h1 style="margin-bottom: 20px;">✏️ Edit Pet: <?= htmlspecialchars($pet['name']) ?></h1>

        <?php if ($message) echo "<div class='message success'>$message</div>"; ?>
        <?php if ($error) echo "<div class='message error'>$error</div>"; ?>

        <div class="form-container">
            <form method="POST" action="update_pet_details.php?id=<?= $pet['id'] ?>">
                
                <div class="form-group">
                    <label for="pet_name">Pet Name</label>
                    <input type="text" id="pet_name" name="pet_name" class="form-control" 
                           value="<?= htmlspecialchars($pet['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="species">Species</label>
                    <input type="text" id="species" name="species" class="form-control" 
                           value="<?= htmlspecialchars($pet['species']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" class="form-control" 
                           value="<?= htmlspecialchars($pet['breed']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-control" 
                           value="<?= htmlspecialchars($pet['dob']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                <a href="add_pet.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; display: block; text-align: center;">Cancel / Back to Pet List</a>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>