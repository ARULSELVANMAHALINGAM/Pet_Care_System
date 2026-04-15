<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];
$visit_id = $_GET['id'] ?? '';

if (empty($visit_id)) {
    header("Location: manage_clinic_visit.php?error=Invalid visit ID.");
    exit;
}

// Sanitize
$visit_id = mysqli_real_escape_string($conn, $visit_id);

// Verify that this visit belongs to this vet
$check_sql = "SELECT id FROM clinic_visits WHERE id=? AND vet_id=?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'ii', $visit_id, $vet_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($check_result) == 0) {
    mysqli_stmt_close($check_stmt);
    header("Location: manage_clinic_visit.php?error=You do not have permission to delete this visit.");
    exit;
}
mysqli_stmt_close($check_stmt);

// DELETE clinic visit
$delete_sql = "DELETE FROM clinic_visits WHERE id=? AND vet_id=?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
if ($delete_stmt) {
    mysqli_stmt_bind_param($delete_stmt, 'ii', $visit_id, $vet_id);
    if (mysqli_stmt_execute($delete_stmt)) {
        mysqli_stmt_close($delete_stmt);
        header("Location: manage_clinic_visit.php?success=deleted");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($delete_stmt);
        mysqli_stmt_close($delete_stmt);
        header("Location: manage_clinic_visit.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: manage_clinic_visit.php?error=" . urlencode('Database Error: could not prepare delete.' . mysqli_error($conn)));
    exit;
}
?>

