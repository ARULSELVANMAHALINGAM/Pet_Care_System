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
$visit_date = $_POST['visit_date'] ?? '';
$reason = $_POST['reason'] ?? '';
$status = $_POST['status'] ?? 'Scheduled';

// Validate
if (empty($pet_id) || empty($visit_date) || empty($reason)) {
    header("Location: manage_clinic_visit.php?error=All required fields must be filled.");
    exit;
}

// Sanitize inputs
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$visit_date = mysqli_real_escape_string($conn, $visit_date);
$reason = mysqli_real_escape_string($conn, $reason);
$status = mysqli_real_escape_string($conn, $status);

// INSERT into clinic_visits table
$insert_sql = "INSERT INTO clinic_visits (pet_id, vet_id, visit_date, reason, status) VALUES (?, ?, ?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'iisss', $pet_id, $vet_id, $visit_date, $reason, $status);
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        header("Location: manage_clinic_visit.php?success=added");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: manage_clinic_visit.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: manage_clinic_visit.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>

