<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

// Read form values
$pet_id = $_POST['pet_id'] ?? '';
$vaccine_name = $_POST['vaccine_name'] ?? '';
$date = $_POST['date'] ?? '';
$status = $_POST['status'] ?? 'Pending';

// Validate
if (empty($pet_id) || empty($vaccine_name) || empty($date)) {
    header("Location: update_vaccination.php?error=All required fields must be filled.");
    exit;
}

// Sanitize inputs
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$vaccine_name = mysqli_real_escape_string($conn, $vaccine_name);
$date = mysqli_real_escape_string($conn, $date);
$status = mysqli_real_escape_string($conn, $status);

// INSERT into vaccinations table
$insert_sql = "INSERT INTO vaccinations (pet_id, vaccine_name, date, status) VALUES (?, ?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'isss', $pet_id, $vaccine_name, $date, $status);
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        header("Location: update_vaccination.php?success=added");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: update_vaccination.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: update_vaccination.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>

