<?php
session_start();
// Include database connection configuration
include('../config/db.php');

// Check if user is logged in and has 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// --- 1. Fetch Activity Log Data ---
// Check if activity_logs table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'activity_logs'");
$table_exists = mysqli_num_rows($table_check) > 0;

$result = false;
$error_message = '';

if ($table_exists) {
    // Check table structure first
    $check_columns = mysqli_query($conn, "SHOW COLUMNS FROM activity_logs LIKE 'action'");
    $has_action = mysqli_num_rows($check_columns) > 0;
    
    if ($has_action) {
        // Use 'action' column (newer schema)
        $result = @mysqli_query($conn, "
            SELECT 
                al.id, 
                COALESCE(u.username, 'System') AS user_name, 
                al.action AS action_description,
                al.created_at AS timestamp
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT 100 
        ");
    } else {
        // Try 'action_description' column (older schema)
        $result = @mysqli_query($conn, "
            SELECT 
                al.id, 
                COALESCE(u.username, 'System') AS user_name, 
                al.action_description,
                al.timestamp
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.timestamp DESC
            LIMIT 100 
        ");
    }
    
    if (!$result) {
        $error_message = "Error fetching activity logs: " . mysqli_error($conn);
        $result = false;
    }
} else {
    // Table doesn't exist - create it with correct schema
    $create_table_sql = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        user_role VARCHAR(50),
        action VARCHAR(255) NOT NULL,
        details TEXT,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (mysqli_query($conn, $create_table_sql)) {
        // Insert a sample log entry
        $user_id = $_SESSION['user_id'] ?? null;
        $user_role = $_SESSION['role'] ?? 'admin';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        mysqli_query($conn, "INSERT INTO activity_logs (user_id, user_role, action, details, ip_address) 
                            VALUES ($user_id, '$user_role', 'Activity logs table created', 'System initialized activity logging', '$ip')");
        $error_message = "Activity logs table created successfully. Refresh to see logs.";
        $result = false;
    } else {
        $error_message = "Could not create activity_logs table: " . mysqli_error($conn);
        $result = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .log-timestamp {
            white-space: nowrap; /* Prevent date/time wrapping */
            font-size: 0.9em;
            color: #64748b;
        }
        .error-box {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">📜 Activity Logs</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">
                <strong>Purpose:</strong> This page tracks all system activities and user actions for security, auditing, and monitoring purposes. 
                It helps administrators monitor user behavior, track changes, and maintain system integrity. 
                View the last 100 logged activities including logins, data modifications, and administrative actions.
            </p>
        </div>
        
        <?php if (isset($error_message)) echo "<div class='error-box'>$error_message</div>"; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) { 
                            // Handle both column name variations
                            $action = $row['action_description'] ?? $row['action'] ?? 'N/A';
                            $timestamp = $row['timestamp'] ?? $row['created_at'] ?? 'N/A';
                            $user_name = $row['user_name'] ?? 'System';
                            // Format timestamp if it exists
                            if ($timestamp != 'N/A' && $timestamp) {
                                $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
                            }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($user_name) ?></td>
                        <td><?= htmlspecialchars($action) ?></td>
                        <td class="log-timestamp"><?= htmlspecialchars($timestamp) ?></td>
                    </tr>
                    <?php 
                        }
                    } else if ($result !== false) { // Check if the query ran without a fatal error
                        echo "<tr><td colspan='4' style='text-align: center; padding: 40px; color: #64748b;'>No recent activity logs found. Activities will appear here as users interact with the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>