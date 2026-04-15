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
$instruction = $_POST['instruction'] ?? '';

// Validate
if (empty($pet_id) || empty($instruction)) {
    header("Location: add_care_instruction.php?error=All fields are required.");
    exit;
}

// Sanitize inputs
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$instruction = mysqli_real_escape_string($conn, $instruction);

// INSERT into care_instructions table
$insert_sql = "INSERT INTO care_instructions (pet_id, vet_id, instruction) VALUES (?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'iis', $pet_id, $vet_id, $instruction);
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        header("Location: add_care_instruction.php?success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: add_care_instruction.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: add_care_instruction.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>

