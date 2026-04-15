<?php
session_start();
// 1. Include DB connection file
include('../config/db.php');

// Check if user is logged in as owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

// Secure check for valid user ID
if (empty($user_id) || !is_numeric($user_id)) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

// 2. Fetch Vaccination Schedules (Securely using Prepared Statement)
$sql = "
    SELECT 
        v.id,
        v.vaccine_name, 
        v.date, 
        v.status,
        p.name AS pet_name 
    FROM 
        vaccinations v
    JOIN 
        pets p ON v.pet_id = p.id
    WHERE 
        p.owner_id = ? 
    ORDER BY 
        v.date DESC
";

$schedules_query = false;
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $schedules_query = mysqli_stmt_get_result($stmt);
    } else {
        error_log("Vaccination schedule query failed: " . mysqli_stmt_error($stmt));
    }
} else {
    error_log("Vaccination schedule statement preparation failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Schedule | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .card { 
            background: #fff; 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08); 
            border: 1px solid #e9d5ff;
            overflow-x: auto;
        }
        .card table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .card th, .card td { 
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9; 
            text-align: left; 
        }
        .card th { 
            background: #f8fafc; 
            color: #475569; 
            font-weight: 600; 
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .card td {
            color: #1e293b;
            font-size: 0.95rem;
        }
        .card tbody tr:hover { 
            background-color: #f8fafc; 
        }
        .card tbody tr:last-child td {
            border-bottom: none;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 6px;
        }
        .badge-warning {
            color: #92400e;
            background-color: #fef3c7;
        }
        .badge-success {
            color: #065f46;
            background-color: #d1fae5;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">💉 Vaccination Schedule</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View your pet's vaccination schedule and status.</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Pet Name</th> 
                        <th>Vaccine</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($schedules_query && $schedules_query->num_rows > 0): 
                        while ($schedule = $schedules_query->fetch_assoc()):
                            $status = htmlspecialchars($schedule['status']);
                            $badge_class = ($status === 'Completed') ? 'badge-success' : 'badge-warning';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($schedule['pet_name']) ?></td>
                        <td><?= htmlspecialchars($schedule['vaccine_name']) ?></td>
                        <td><?= date('Y-m-d', strtotime($schedule['date'])) ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= $status ?></span></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 40px;">
                            <div style="font-size: 3rem; margin-bottom: 15px;">💉</div>
                            <p style="font-size: 1.1rem; margin: 0;">No vaccination schedules found for your pets.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php 
    if (isset($stmt)) {
        $stmt->close();
    }
    include('../includes/footer.php'); 
    ?>
</body>
</html>