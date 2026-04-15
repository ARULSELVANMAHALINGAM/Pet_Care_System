<?php
session_start();
// 1. Include Database Connection
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$message = '';

// 2. Handle Delete Pet Logic
if (isset($_POST['delete_pet'])) {
    $pet_id = mysqli_real_escape_string($conn, $_POST['pet_id']);
    
    // Check if pet_id is provided and valid
    if (!empty($pet_id) && is_numeric($pet_id)) {
        // Prepare and execute the delete statement
        $delete = mysqli_query($conn, "DELETE FROM pets WHERE id='$pet_id'");
        
        if ($delete) {
            $message = "🗑️ Pet deleted successfully!";
        } else {
            $message = "❌ Error deleting pet: " . mysqli_error($conn);
        }
    } else {
        $message = "❌ Invalid pet ID for deletion.";
    }
}

// 3. Fetch Pet Data (including owner's username)
// Assuming a 'pets' table with columns: id, name, species, breed, owner_id
// and a 'users' table with columns: id, username
$result = mysqli_query($conn, "
    SELECT 
        p.id, 
        p.name, 
        p.species, 
        p.breed, 
        u.username AS owner_name 
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    ORDER BY p.id ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pets | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        /* Style for the confirmation message */
        .message {
            text-align: center;
            padding: 12px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
            border: 1px solid #e9d5ff;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🐶 Manage Pets</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View and manage all pets in the system.</p>
        </div>
        
        <?php if (!empty($message)) echo "<div class='message " . (strpos($message, '❌') !== false ? 'error' : 'success') . "'>$message</div>"; ?>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Species</th>
                        <th>Breed</th>
                        <th>Owner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) { 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['species']) ?></td>
                        <td><?= htmlspecialchars($row['breed']) ?></td>
                        <td><?= htmlspecialchars($row['owner_name']) ?></td>
                        <td class="actions">
                            <a href="edit_pet.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                            
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete the pet: <?= htmlspecialchars($row['name']) ?>?');" style="display: inline;">
                                <input type="hidden" name="pet_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_pet" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center;'>No pets found in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>