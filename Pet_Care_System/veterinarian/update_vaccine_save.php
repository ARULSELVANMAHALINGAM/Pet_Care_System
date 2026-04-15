<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

// Read form values
$vaccination_id = $_POST['vaccination_id'] ?? '';
$pet_id = $_POST['pet_id'] ?? '';
$vaccine_name = $_POST['vaccine_name'] ?? '';
$date_given = $_POST['date_given'] ?? '';
$status = $_POST['status'] ?? '';

// Validate
if (empty($vaccination_id) || empty($pet_id) || empty($vaccine_name) || empty($date_given) || empty($status)) {
    header("Location: update_vaccination.php?id=" . $vaccination_id . "&error=All fields are required.");
    exit;
}

// Sanitize inputs
$vaccination_id = mysqli_real_escape_string($conn, $vaccination_id);
$pet_id = mysqli_real_escape_string($conn, $pet_id);
$vaccine_name = mysqli_real_escape_string($conn, $vaccine_name);
$date_given = mysqli_real_escape_string($conn, $date_given);
$status = mysqli_real_escape_string($conn, $status);

// UPDATE vaccinations table
$update_sql = "UPDATE vaccinations SET pet_id=?, vaccine_name=?, date=?, status=? WHERE id=?";
$update_stmt = mysqli_prepare($conn, $update_sql);
if ($update_stmt) {
    mysqli_stmt_bind_param($update_stmt, 'isssi', $pet_id, $vaccine_name, $date_given, $status, $vaccination_id);
    if (mysqli_stmt_execute($update_stmt)) {
        mysqli_stmt_close($update_stmt);
        header("Location: update_vaccination.php?id=" . $vaccination_id . "&success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($update_stmt);
        mysqli_stmt_close($update_stmt);
        header("Location: update_vaccination.php?id=" . $vaccination_id . "&error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: update_vaccination.php?id=" . $vaccination_id . "&error=" . urlencode('Database Error: could not prepare update.' . mysqli_error($conn)));
    exit;
}
?>

