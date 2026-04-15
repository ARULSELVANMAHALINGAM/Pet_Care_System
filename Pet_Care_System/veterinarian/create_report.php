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
$report_type = $_POST['report_type'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Validate
if (empty($report_type) || empty($start_date) || empty($end_date)) {
    header("Location: generate_report.php?error=All fields are required.");
    exit;
}

// Redirect to reports view with parameters
header("Location: ../reports/view_report.php?type=" . urlencode($report_type) . "&start_date=" . urlencode($start_date) . "&end_date=" . urlencode($end_date) . "&vet_id=" . $vet_id);
exit;
?>

