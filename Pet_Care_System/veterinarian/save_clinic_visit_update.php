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
$visit_id = $_POST['visit_id'] ?? '';
$pet_id = $_POST['pet_id'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';
$reason = $_POST['reason'] ?? '';
$status = $_POST['status'] ?? '';

// Validate
if (empty($visit_id) || empty($pet_id) || empty($visit_date) || empty($reason) || empty($status)) {
    header("Location: update_clinic_visit.php?id=" . $visit_id . "&error=All required fields must be filled.");
    exit;
}

// Sanitize inputs
$visit_id = mysqli_real_escape_string($conn, $visit_id);
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$visit_date = mysqli_real_escape_string($conn, $visit_date);
$reason = mysqli_real_escape_string($conn, $reason);
$status = mysqli_real_escape_string($conn, $status);

// Verify that this visit belongs to this vet
$check_sql = "SELECT id FROM clinic_visits WHERE id=? AND vet_id=?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'ii', $visit_id, $vet_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($check_result) == 0) {
    mysqli_stmt_close($check_stmt);
    header("Location: update_clinic_visit.php?id=" . $visit_id . "&error=You do not have permission to update this visit.");
    exit;
}
mysqli_stmt_close($check_stmt);

// UPDATE clinic_visits table
$update_sql = "UPDATE clinic_visits SET pet_id=?, visit_date=?, reason=?, status=? WHERE id=? AND vet_id=?";
$update_stmt = mysqli_prepare($conn, $update_sql);
if ($update_stmt) {
    mysqli_stmt_bind_param($update_stmt, 'isssii', $pet_id, $visit_date, $reason, $status, $visit_id, $vet_id);
    if (mysqli_stmt_execute($update_stmt)) {
        mysqli_stmt_close($update_stmt);
        header("Location: update_clinic_visit.php?id=" . $visit_id . "&success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($update_stmt);
        mysqli_stmt_close($update_stmt);
        header("Location: update_clinic_visit.php?id=" . $visit_id . "&error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: update_clinic_visit.php?id=" . $visit_id . "&error=" . urlencode('Database Error: could not prepare update.' . mysqli_error($conn)));
    exit;
}
?>

