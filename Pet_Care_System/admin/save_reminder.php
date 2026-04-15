<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Read form values
$pet_id = $_POST['pet_id'] ?? null;
$user_id = $_POST['user_id'] ?? null;
$title = $_POST['title'] ?? '';
$message = $_POST['message'] ?? '';
$reminder_date = $_POST['reminder_date'] ?? '';

// Validate
if (empty($title) || empty($message) || empty($reminder_date)) {
    header("Location: manage_notifications.php?error=All required fields must be filled.");
    exit;
}

// Sanitize inputs
$title = mysqli_real_escape_string($conn, $title);
$message = mysqli_real_escape_string($conn, $message);
$reminder_date = mysqli_real_escape_string($conn, $reminder_date);
$pet_id = !empty($pet_id) ? mysqli_real_escape_string($conn, $pet_id) : null;
$user_id = !empty($user_id) ? mysqli_real_escape_string($conn, $user_id) : null;

// INSERT into reminders table
if ($pet_id && $user_id) {
    $insert_sql = "INSERT INTO reminders (pet_id, user_id, title, message, reminder_date, status) VALUES (?, ?, ?, ?, ?, 'Upcoming')";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, 'iisss', $pet_id, $user_id, $title, $message, $reminder_date);
} elseif ($pet_id) {
    $insert_sql = "INSERT INTO reminders (pet_id, title, message, reminder_date, status) VALUES (?, ?, ?, ?, 'Upcoming')";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, 'isss', $pet_id, $title, $message, $reminder_date);
} elseif ($user_id) {
    $insert_sql = "INSERT INTO reminders (user_id, title, message, reminder_date, status) VALUES (?, ?, ?, ?, 'Upcoming')";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, 'isss', $user_id, $title, $message, $reminder_date);
} else {
    $insert_sql = "INSERT INTO reminders (title, message, reminder_date, status) VALUES (?, ?, ?, 'Upcoming')";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, 'sss', $title, $message, $reminder_date);
}

if ($insert_stmt) {
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        
        // Create notification for the user if specified
        if ($user_id) {
            $notif_sql = "INSERT INTO notifications (user_id, type, title, message, is_read) VALUES (?, 'reminder', ?, ?, 0)";
            $notif_stmt = mysqli_prepare($conn, $notif_sql);
            mysqli_stmt_bind_param($notif_stmt, 'iss', $user_id, $title, $message);
            mysqli_stmt_execute($notif_stmt);
            mysqli_stmt_close($notif_stmt);
        }
        
        header("Location: manage_notifications.php?success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: manage_notifications.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: manage_notifications.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>

