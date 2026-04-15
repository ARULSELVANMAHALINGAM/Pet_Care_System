<?php
session_start();
// Include database connection configuration
include('../config/db.php');

// Check if user is logged in and has 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// --- Fetch Key Dashboard Metrics ---

// a. Total Users
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users");
$total_users = mysqli_fetch_assoc($q_users)['total'] ?? 0;

// b. Total Pets
$q_pets = mysqli_query($conn, "SELECT COUNT(id) AS total FROM pets");
$total_pets = mysqli_fetch_assoc($q_pets)['total'] ?? 0;

// c. Pending Vaccinations (Status = 'Pending')
$q_pending_vaccines = mysqli_query($conn, "SELECT COUNT(id) AS total FROM vaccinations WHERE status='Pending'");
$pending_vaccines = mysqli_fetch_assoc($q_pending_vaccines)['total'] ?? 0;

// d. Total Clinic Visits (Scheduled or Completed)
$q_visits = mysqli_query($conn, "SELECT COUNT(id) AS total FROM clinic_visits");
$total_visits = mysqli_fetch_assoc($q_visits)['total'] ?? 0;

// Check for query execution errors (optional, but good practice)
if (!$q_users || !$q_pets || !$q_pending_vaccines || !$q_visits) {
    // In a real application, you'd log this error.
    // die("Database error fetching dashboard metrics: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .kpi-card {
            border-left: 4px solid;
            position: relative;
            overflow: hidden;
            padding: 20px !important;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            transition: width 0.3s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }
        .kpi-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }
        .kpi-card .card-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        .kpi-card h3 {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kpi-card .card-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .kpi-card .card-label {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0;
        }
        /* Color themes for cards */
        .color-blue { 
            border-left-color: #3b82f6; 
        }
        .color-blue::before {
            background-color: #3b82f6;
        }
        .color-green { 
            border-left-color: #10b981; 
        }
        .color-green::before {
            background-color: #10b981;
        }
        .color-red { 
            border-left-color: #ef4444; 
        }
        .color-red::before {
            background-color: #ef4444;
        }
        .color-yellow { 
            border-left-color: #f59e0b; 
        }
        .color-yellow::before {
            background-color: #f59e0b;
        }
        .overview-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
            border: 1px solid #e9d5ff;
            border-left: 4px solid #a78bfa;
            margin-top: 20px;
        }
        .overview-card h2 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .overview-card p {
            color: #64748b;
            font-size: 0.9375rem;
            line-height: 1.6;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🏠 Dashboard Stats</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Welcome to the PetCare Management System! 🐾</p>
        </div>

        <div class="card-grid">
            <div class="card kpi-card color-blue">
                <div class="card-icon">👤</div>
                <h3>Total Users</h3>
                <p class="card-value"><?= htmlspecialchars($total_users) ?></p>
                <p class="card-label">User Accounts Registered</p>
            </div>
            
            <div class="card kpi-card color-green">
                <div class="card-icon">🐶</div>
                <h3>Total Pets</h3>
                <p class="card-value"><?= htmlspecialchars($total_pets) ?></p>
                <p class="card-label">Pets under Management</p>
            </div>
            
            <div class="card kpi-card color-red">
                <div class="card-icon">💉</div>
                <h3>Pending Vaccinations</h3>
                <p class="card-value"><?= htmlspecialchars($pending_vaccines) ?></p>
                <p class="card-label">Upcoming/Overdue Shots</p>
            </div>
            
            <div class="card kpi-card color-yellow">
                <div class="card-icon">🏥</div>
                <h3>Clinic Visits</h3>
                <p class="card-value"><?= htmlspecialchars($total_visits) ?></p>
                <p class="card-label">Total Appointments/Visits</p>
            </div>
        </div>
        
        <div class="overview-card">
            <h2>System Overview</h2>
            <p>This dashboard provides quick insights into users, pets, health records, and notifications. Use the sidebar to manage modules and view reports.</p>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>