<?php
session_start();
// Include database connection configuration
include('../config/db.php');

// Check if user is logged in and has 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$message = '';

// --- 2. Handle Delete Notification/Reminder Logic ---
if (isset($_POST['delete_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    if (!empty($reminder_id) && is_numeric($reminder_id)) {
        $delete = mysqli_query($conn, "DELETE FROM reminders WHERE id='$reminder_id'");
        
        if ($delete) {
            $message = "🗑️ Notification/Reminder deleted successfully!";
        } else {
            $message = "❌ Error deleting notification: " . mysqli_error($conn);
        }
    } else {
        $message = "❌ Invalid Reminder ID for deletion.";
    }
}

// --- 3. Fetch Notifications/Reminders Data ---
// Note: Assuming the 'title' column from the 'reminders' table acts as the 'Type'.
$result = mysqli_query($conn, "
    SELECT 
        id, 
        title AS type, 
        message, 
        reminder_date AS date,
        status 
    FROM reminders
    ORDER BY reminder_date DESC, id DESC
");

// Check for query execution errors
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        /* Style for messages */
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
        .status-upcoming { color: var(--warning-color); font-weight: 600; }
        .status-sent { color: var(--success-color); font-weight: 600; }
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
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🔔 Manage Notifications & Reminders</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Create reminders for pet owners and manage system notifications.</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, '❌') !== false ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">Reminder created successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- Add Reminder Form -->
        <div class="form-container" style="max-width: 600px; margin: 0 auto 30px; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08); border: 1px solid #e9d5ff;">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">➕ Create New Reminder</h2>
            <form method="POST" action="save_reminder.php">
                <?php 
                // Fetch pets for dropdown
                $pets_query = mysqli_query($conn, "SELECT id, name, species FROM pets");
                // Fetch users for dropdown
                $users_query = mysqli_query($conn, "SELECT id, username, role FROM users WHERE role = 'owner'");
                ?>
                <div class="form-group">
                    <label for="pet_id">Select Pet (Optional)</label>
                    <select id="pet_id" name="pet_id">
                        <option value="">General Reminder</option>
                        <?php 
                        while($pet = mysqli_fetch_assoc($pets_query)) { ?>
                            <option value="<?= $pet['id'] ?>"><?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user_id">Select User (Optional)</label>
                    <select id="user_id" name="user_id">
                        <option value="">All Users</option>
                        <?php 
                        mysqli_data_seek($users_query, 0);
                        while($user = mysqli_fetch_assoc($users_query)) { ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role']) ?>)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Reminder Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Vaccination Due, Checkup Scheduled" required>
                </div>
                <div class="form-group">
                    <label for="message">Reminder Message</label>
                    <textarea id="message" name="message" rows="3" placeholder="Enter reminder message" required></textarea>
                </div>
                <div class="form-group">
                    <label for="reminder_date">Reminder Date</label>
                    <input type="date" id="reminder_date" name="reminder_date" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Reminder</button>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">📋 Existing Reminders</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Fetch reminders with pet names
                    $reminders_result = mysqli_query($conn, "
                        SELECT 
                            r.id, 
                            r.title,
                            r.message, 
                            r.reminder_date AS date,
                            r.status,
                            p.name AS pet_name
                        FROM reminders r
                        LEFT JOIN pets p ON r.pet_id = p.id
                        ORDER BY r.reminder_date DESC, r.id DESC
                    ");
                    
                    if ($reminders_result && mysqli_num_rows($reminders_result) > 0) {
                        while($row = mysqli_fetch_assoc($reminders_result)) { 
                            $status_class = strtolower(str_replace(' ', '-', $row['status']));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['pet_name'] ?: 'General') ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : '') ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 'Upcoming' ? 'badge-warning' : ($row['status'] == 'Sent' ? 'badge-success' : 'badge-secondary') ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this reminder?');" style="display: inline;">
                                <input type="hidden" name="reminder_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_reminder" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align: center; padding: 20px; color: #64748b;'>No reminders found. Create your first reminder above!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>