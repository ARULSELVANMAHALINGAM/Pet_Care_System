<?php
// CRITICAL: Suppress database warnings (for cleaner front-end during testing)
error_reporting(E_ALL & ~E_WARNING); 

session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

$vet_id = $_SESSION['user_id'];

// --- Corrected Database Queries based on the provided Schema ---

// 1. Assigned Pets: Count DISTINCT pets this vet has created a health record for.
$assignedPets = 0;
$assignedPetsResult = @mysqli_query($conn, "SELECT COUNT(DISTINCT pet_id) as count FROM health_records WHERE vet_id = '{$vet_id}'");
if ($assignedPetsResult && mysqli_num_rows($assignedPetsResult) > 0) {
    $row = mysqli_fetch_assoc($assignedPetsResult);
    $assignedPets = $row['count'] ?? 0;
}

// 2. Upcoming Vaccinations: Count vaccinations for the vet's assigned pets
$upcomingVaccines = 0;
$vaccinesResult = @mysqli_query($conn,
    "SELECT COUNT(v.id) as count 
     FROM vaccinations v
     WHERE v.pet_id IN (
         SELECT pet_id FROM health_records WHERE vet_id = '{$vet_id}'
     ) 
     AND v.date >= CURDATE()
     AND v.status = 'Pending'"
);
if ($vaccinesResult && mysqli_num_rows($vaccinesResult) > 0) {
    $row = mysqli_fetch_assoc($vaccinesResult);
    $upcomingVaccines = $row['count'] ?? 0;
}

// 3. Clinic Visits Today: Count visits scheduled for this specific vet today
$clinicVisits = 0;
$visitsResult = @mysqli_query($conn,
    "SELECT COUNT(*) as count FROM clinic_visits 
     WHERE vet_id = '{$vet_id}' 
     AND DATE(visit_date) = CURDATE()
     AND status = 'Scheduled'"
);
if ($visitsResult && mysqli_num_rows($visitsResult) > 0) {
    $row = mysqli_fetch_assoc($visitsResult);
    $clinicVisits = $row['count'] ?? 0;
}

// 4. Reports Generated: Count health records created by this vet as reports
$reportsGenerated = 0;
$reportsResult = @mysqli_query($conn, "SELECT COUNT(*) as count FROM health_records WHERE vet_id = '{$vet_id}'");
if ($reportsResult && mysqli_num_rows($reportsResult) > 0) {
    $row = mysqli_fetch_assoc($reportsResult);
    $reportsGenerated = $row['count'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarian Dashboard | PetCare</title>
    <?php include('../includes/head.php'); ?>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Dashboard Stats</h1>
            <p style="color: #64748b; font-size: 1rem;">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Veterinarian'); ?> 🩺</p>
        </div>

            <div class="card-grid">
                <div class="card">
                    <div class="card-icon">🐕</div>
                    <h3>Assigned Pets</h3>
                    <p class="card-value"><?php echo $assignedPets; ?></p>
                    <p class="card-label">Pets under your care</p>
                </div>

                <div class="card">
                    <div class="card-icon">💉</div>
                    <h3>Upcoming Vaccines</h3>
                    <p class="card-value"><?php echo $upcomingVaccines; ?></p>
                    <p class="card-label">Scheduled doses (Pending)</p>
                </div>

                <div class="card">
                    <div class="card-icon">📅</div>
                    <h3>Visits Scheduled Today</h3>
                    <p class="card-value"><?php echo $clinicVisits; ?></p>
                    <p class="card-label">Today's appointments</p>
                </div>

                <div class="card">
                    <div class="card-icon">📊</div>
                    <h3>Reports Generated</h3>
                    <p class="card-value"><?php echo $reportsGenerated; ?></p>
                    <p class="card-label">Total submitted reports</p>
                </div>
            </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>