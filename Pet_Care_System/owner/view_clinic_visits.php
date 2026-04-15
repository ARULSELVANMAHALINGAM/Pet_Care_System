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

// 2. Fetch Clinic Visits (Securely using Prepared Statement)
// Joins: clinic_visits, pets (to filter by owner_id), users (to get Vet's username)
$sql = "
    SELECT 
        cv.visit_date, 
        cv.reason, 
        cv.status,
        p.name AS pet_name, 
        u.username AS vet_name 
    FROM 
        clinic_visits cv
    JOIN 
        pets p ON cv.pet_id = p.id
    LEFT JOIN
        users u ON cv.vet_id = u.id -- Vets are stored in the 'users' table
    WHERE 
        p.owner_id = ? 
    ORDER BY 
        cv.visit_date DESC
";

$visits_query = false;
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $visits_query = mysqli_stmt_get_result($stmt);
    } else {
        error_log("Clinic visits query failed: " . mysqli_stmt_error($stmt));
    }
} else {
    error_log("Clinic visits statement preparation failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Visits | PetCare</title>
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
            background-color: #faf5ff; 
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
        .badge-info {
            color: #0c5460;
            background-color: #d1ecf1;
        }
        .badge-success {
            color: #065f46;
            background-color: #d1fae5;
        }
        .badge-danger {
            color: #991b1b;
            background-color: #fee2e2;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🏥 Clinic Visits</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View your pet's clinic visit history.</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Reason</th>
                        <th>Vet</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // 3. Loop through results and display data
                    if ($visits_query && $visits_query->num_rows > 0): 
                        while ($visit = $visits_query->fetch_assoc()):
                            $status = htmlspecialchars($visit['status']);
                            
                            // Determine badge style based on status
                            if ($status === 'Completed') {
                                $badge_class = 'badge-success';
                            } elseif ($status === 'Cancelled') {
                                $badge_class = 'badge-danger';
                            } else {
                                $badge_class = 'badge-info'; // Scheduled
                            }
                    ?>
                    <tr>
                        <td><?= date('Y-m-d', strtotime($visit['visit_date'])) ?></td>
                        <td><?= htmlspecialchars($visit['pet_name']) ?></td>
                        <td><?= htmlspecialchars($visit['reason']) ?></td>
                        <td><?= htmlspecialchars($visit['vet_name'] ?: 'N/A') ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= $status ?></span></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 40px;">
                            <div style="font-size: 3rem; margin-bottom: 15px;">🏥</div>
                            <p style="font-size: 1.1rem; margin: 0;">No clinic visits found for your pets.</p>
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