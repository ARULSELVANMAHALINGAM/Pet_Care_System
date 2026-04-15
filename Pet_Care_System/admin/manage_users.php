<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $insert = mysqli_query($conn, "INSERT INTO users (username, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
    $message = $insert ? "✅ User added successfully!" : "❌ Error adding user!";
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    $message = "🗑️ User deleted successfully!";
}

// Fetch users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
            max-width: 500px;
        }
        
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
        
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .actions form {
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    <center>
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">👥 Manage Users</h1>
            <p style="color: #64748b; font-size: 1rem;">Add, edit, or remove user accounts from the system.</p>
        </div>

        <?php if (!empty($message)) echo "<div class='message success'>$message</div>"; ?>

        <div class="form-container">
            <h2 style="margin-bottom: 15px; color: var(--text-primary);">Add New User</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="name">NAME</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">EMAIL</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">PASSWORD</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">ROLE</label>
                    <select id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="vet">Veterinarian</option>
                        <option value="owner">Pet Owner</option>
                    </select>
                </div>
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>
        </>
        <div class="card">
            <h2 style="margin-bottom: 20px;">Users List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id'] ?? $row['user_id'] ?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td class="actions">
                            <form method="POST" onsubmit="return confirm('Delete this user?');" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?? $row['user_id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
