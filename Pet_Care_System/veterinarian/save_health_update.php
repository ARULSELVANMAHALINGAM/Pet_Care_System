<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];

// Read form values
$record_id = $_POST['record_id'] ?? '';
$diagnosis = $_POST['diagnosis'] ?? '';
$treatment = $_POST['treatment'] ?? '';
$date = $_POST['date'] ?? '';

// Validate
if (empty($record_id) || empty($diagnosis) || empty($treatment) || empty($date)) {
    header("Location: update_health_record.php?id=" . $record_id . "&error=All fields are required.");
    exit;
}

// Sanitize inputs
$record_id = mysqli_real_escape_string($conn, $record_id);
$diagnosis = mysqli_real_escape_string($conn, $diagnosis);
$treatment = mysqli_real_escape_string($conn, $treatment);
$date = mysqli_real_escape_string($conn, $date);

// Verify that this record belongs to this vet
$check_sql = "SELECT id FROM health_records WHERE id=? AND vet_id=?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'ii', $record_id, $vet_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($check_result) == 0) {
    mysqli_stmt_close($check_stmt);
    header("Location: update_health_record.php?id=" . $record_id . "&error=You do not have permission to update this record.");
    exit;
}
mysqli_stmt_close($check_stmt);

// UPDATE health_records table
$update_sql = "UPDATE health_records SET diagnosis=?, treatment=?, date=? WHERE id=? AND vet_id=?";
$update_stmt = mysqli_prepare($conn, $update_sql);
if ($update_stmt) {
    mysqli_stmt_bind_param($update_stmt, 'sssii', $diagnosis, $treatment, $date, $record_id, $vet_id);
    if (mysqli_stmt_execute($update_stmt)) {
        mysqli_stmt_close($update_stmt);
        header("Location: update_health_record.php?id=" . $record_id . "&success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($update_stmt);
        mysqli_stmt_close($update_stmt);
        header("Location: update_health_record.php?id=" . $record_id . "&error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: update_health_record.php?id=" . $record_id . "&error=" . urlencode('Database Error: could not prepare update.' . mysqli_error($conn)));
    exit;
}
?>

