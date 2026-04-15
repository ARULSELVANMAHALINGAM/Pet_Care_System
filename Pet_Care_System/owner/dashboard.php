<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];

// Fetch statistics
$myPets = 0;
$upcomingVaccines = 0;
$clinicVisits = 0;
$careInstructions = 0;

// Count pets
$petsResult = @mysqli_query($conn, "SELECT COUNT(*) as count FROM pets WHERE owner_id = '{$owner_id}'");
if ($petsResult && mysqli_num_rows($petsResult) > 0) {
    $row = mysqli_fetch_assoc($petsResult);
    $myPets = $row['count'] ?? 0;
}

// Count upcoming vaccinations
$vaccinesResult = @mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM vaccinations v
     INNER JOIN pets p ON v.pet_id = p.id
     WHERE p.owner_id = '{$owner_id}' 
     AND v.date >= CURDATE() 
     AND v.status = 'Pending'"
);
if ($vaccinesResult && mysqli_num_rows($vaccinesResult) > 0) {
    $row = mysqli_fetch_assoc($vaccinesResult);
    $upcomingVaccines = $row['count'] ?? 0;
}

// Count clinic visits
$visitsResult = @mysqli_query($conn,
    "SELECT COUNT(*) as count FROM clinic_visits cv
     INNER JOIN pets p ON cv.pet_id = p.id
     WHERE p.owner_id = '{$owner_id}'
     AND cv.status = 'Scheduled'"
);
if ($visitsResult && mysqli_num_rows($visitsResult) > 0) {
    $row = mysqli_fetch_assoc($visitsResult);
    $clinicVisits = $row['count'] ?? 0;
}

// Count care instructions
$instructionsResult = @mysqli_query($conn,
    "SELECT COUNT(*) as count FROM care_instructions ci
     INNER JOIN pets p ON ci.pet_id = p.id
     WHERE p.owner_id = '{$owner_id}'"
);
if ($instructionsResult && mysqli_num_rows($instructionsResult) > 0) {
    $row = mysqli_fetch_assoc($instructionsResult);
    $careInstructions = $row['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Owner Dashboard | PetCare</title>
    <?php include('../includes/head.php'); ?>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🐾 Pet Owner Dashboard</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Welcome back! Here's an overview of your pet's health and care.</p>
        </div>
        
        <div class="card-grid">
            <div class="card">
                <div class="card-icon">🐕</div>
                <h3>My Pets</h3>
                <p class="card-value"><?php echo $myPets; ?></p>
                <p class="card-label">Active Pets</p>
            </div>
            <div class="card">
                <div class="card-icon">💉</div>
                <h3>Upcoming Vaccinations</h3>
                <p class="card-value"><?php echo $upcomingVaccines; ?></p>
                <p class="card-label">Scheduled Doses</p>
            </div>
            <div class="card">
                <div class="card-icon">🏥</div>
                <h3>Clinic Visits</h3>
                <p class="card-value"><?php echo $clinicVisits; ?></p>
                <p class="card-label">Scheduled Visits</p>
            </div>
            <div class="card">
                <div class="card-icon">📋</div>
                <h3>Care Instructions</h3>
                <p class="card-value"><?php echo $careInstructions; ?></p>
                <p class="card-label">Active Instructions</p>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
