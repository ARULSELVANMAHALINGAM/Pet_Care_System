<?php
session_start();
// Include database connection configuration
include('../config/db.php');

// Check if user is logged in and has 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// --- 1. Fetch Report Data (Metrics) ---

// a. Total Users (All Roles)
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users");
$total_users = mysqli_fetch_assoc($q_users)['total'] ?? 0;

// b. Total Pets
$q_pets = mysqli_query($conn, "SELECT COUNT(id) AS total FROM pets");
$total_pets = mysqli_fetch_assoc($q_pets)['total'] ?? 0;

// c. Total Veterinarians
$q_vets = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users WHERE role='vet'");
$total_vets = mysqli_fetch_assoc($q_vets)['total'] ?? 0;

// d. Vaccinations Completed
$q_vaccinations = mysqli_query($conn, "SELECT COUNT(id) AS completed FROM vaccinations WHERE status='Completed'");
$vaccinations_completed = mysqli_fetch_assoc($q_vaccinations)['completed'] ?? 0;

// e. Clinic Visits Logged (Completed status)
$q_visits = mysqli_query($conn, "SELECT COUNT(id) AS completed FROM clinic_visits WHERE status='Completed'");
$visits_completed = mysqli_fetch_assoc($q_visits)['completed'] ?? 0;


// Check for query execution errors (optional, but good practice)
if (!$q_users || !$q_pets || !$q_vets || !$q_vaccinations || !$q_visits) {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .report-table th, .report-table td {
            text-align: left;
        }
        .metric-value {
            font-weight: 700;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📈 View Reports</h1>
            <p style="color: #64748b; font-size: 1rem;">System statistics and analytics overview.</p>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 16px; color: #475569;">Key Performance Indicators</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total System Users (Admins, Vets, Owners)</td>
                        <td class="metric-value"><?= htmlspecialchars($total_users) ?></td>
                    </tr>
                    <tr>
                        <td>Total Pet Accounts</td>
                        <td class="metric-value"><?= htmlspecialchars($total_pets) ?></td>
                    </tr>
                    <tr>
                        <td>Total Veterinarian Accounts</td>
                        <td class="metric-value"><?= htmlspecialchars($total_vets) ?></td>
                    </tr>
                    <tr>
                        <td>Vaccinations Completed (Lifetime)</td>
                        <td class="metric-value"><?= htmlspecialchars($vaccinations_completed) ?></td>
                    </tr>
                    <tr>
                        <td>Clinic Visits Completed (Logged)</td>
                        <td class="metric-value"><?= htmlspecialchars($visits_completed) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>