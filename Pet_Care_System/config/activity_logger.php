<?php
/**
 * Activity Logger Helper Function
 * Use this function throughout the application to log user activities
 */
function logActivity($conn, $user_id, $action, $details = '', $user_role = null) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'activity_logs'");
    if (mysqli_num_rows($table_check) == 0) {
        return false; // Table doesn't exist
    }
    
    // Get user role if not provided
    if ($user_role === null && isset($_SESSION['role'])) {
        $user_role = $_SESSION['role'];
    }
    
    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Check table structure
    $check_columns = mysqli_query($conn, "SHOW COLUMNS FROM activity_logs LIKE 'action'");
    $has_action = mysqli_num_rows($check_columns) > 0;
    
    if ($has_action) {
        // New schema with 'action' column
        $sql = "INSERT INTO activity_logs (user_id, user_role, action, details, ip_address) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'issss', $user_id, $user_role, $action, $details, $ip_address);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }
    } else {
        // Old schema with 'action_description'
        $sql = "INSERT INTO activity_logs (user_id, action_description, timestamp) 
                VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            $action_desc = $action . ($details ? ': ' . $details : '');
            mysqli_stmt_bind_param($stmt, 'is', $user_id, $action_desc);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }
    }
    
    return false;
}
?>

