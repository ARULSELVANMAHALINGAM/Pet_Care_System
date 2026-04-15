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
$pet_id = $_POST['pet_id'] ?? '';
$diagnosis = $_POST['diagnosis'] ?? '';
$treatment = $_POST['treatment'] ?? '';
$date = $_POST['date'] ?? '';

// Validate
if (empty($pet_id) || empty($diagnosis) || empty($treatment) || empty($date)) {
    header("Location: add_health_record.php?error=All fields are required.");
    exit;
}

// Sanitize inputs
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$diagnosis = mysqli_real_escape_string($conn, $diagnosis);
$treatment = mysqli_real_escape_string($conn, $treatment);
$date = mysqli_real_escape_string($conn, $date);

// INSERT into health_records table
$insert_sql = "INSERT INTO health_records (pet_id, diagnosis, treatment, date, vet_id) VALUES (?, ?, ?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'isssi', $pet_id, $diagnosis, $treatment, $date, $vet_id);
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        header("Location: add_health_record.php?success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: add_health_record.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: add_health_record.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>

