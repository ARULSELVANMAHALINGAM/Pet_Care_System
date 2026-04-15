<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];
$instruction_id = $_GET['id'] ?? '';

if (empty($instruction_id)) {
    header("Location: add_care_instruction.php?error=Invalid instruction ID.");
    exit;
}

// Sanitize
$instruction_id = mysqli_real_escape_string($conn, $instruction_id);

// Verify that this instruction belongs to this vet
$check_sql = "SELECT id FROM care_instructions WHERE id=? AND vet_id=?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'ii', $instruction_id, $vet_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($check_result) == 0) {
    mysqli_stmt_close($check_stmt);
    header("Location: add_care_instruction.php?error=You do not have permission to delete this instruction.");
    exit;
}
mysqli_stmt_close($check_stmt);

// DELETE care instruction
$delete_sql = "DELETE FROM care_instructions WHERE id=? AND vet_id=?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
if ($delete_stmt) {
    mysqli_stmt_bind_param($delete_stmt, 'ii', $instruction_id, $vet_id);
    if (mysqli_stmt_execute($delete_stmt)) {
        mysqli_stmt_close($delete_stmt);
        header("Location: add_care_instruction.php?success=deleted");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($delete_stmt);
        mysqli_stmt_close($delete_stmt);
        header("Location: add_care_instruction.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: add_care_instruction.php?error=" . urlencode('Database Error: could not prepare delete.' . mysqli_error($conn)));
    exit;
}
?>

