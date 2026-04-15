<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vaccination_id = $_GET['id'] ?? '';

if (empty($vaccination_id)) {
    header("Location: update_vaccination.php?error=Invalid vaccination ID.");
    exit;
}

// Sanitize
$vaccination_id = mysqli_real_escape_string($conn, $vaccination_id);

// DELETE vaccination
$delete_sql = "DELETE FROM vaccinations WHERE id=?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
if ($delete_stmt) {
    mysqli_stmt_bind_param($delete_stmt, 'i', $vaccination_id);
    if (mysqli_stmt_execute($delete_stmt)) {
        mysqli_stmt_close($delete_stmt);
        header("Location: update_vaccination.php?success=deleted");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($delete_stmt);
        mysqli_stmt_close($delete_stmt);
        header("Location: update_vaccination.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: update_vaccination.php?error=" . urlencode('Database Error: could not prepare delete.' . mysqli_error($conn)));
    exit;
}
?>

