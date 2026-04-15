<?php
session_start();
// 1. Include DB connection file
include('../config/db.php');

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (empty($user_id) || !is_numeric($user_id)) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

// 2. Fetch Reminders (Securely using Prepared Statement)
// Selects reminders linked either directly to the user_id OR linked to a pet owned by the user.
$sql = "
    SELECT 
        r.title, 
        r.reminder_date, 
        r.status, 
        p.name AS pet_name 
    FROM 
        reminders r
    LEFT JOIN
        pets p ON r.pet_id = p.id
    WHERE 
        r.user_id = ? OR p.owner_id = ? 
    ORDER BY 
        r.reminder_date ASC
";

$reminders_query = false;
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind the user_id twice for the OR condition
    $stmt->bind_param("ii", $user_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $reminders_query = mysqli_stmt_get_result($stmt);
    } else {
        error_log("Reminders query failed: " . mysqli_stmt_error($stmt));
    }
} else {
    error_log("Statement preparation failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminders | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .card { 
            background: #fff; 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08); 
            border: 1px solid #e9d5ff; 
        }
        .card table { width: 100%; border-collapse: collapse; }
        .card th, .card td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .card th { 
            background: #f8fafc; 
            color: #475569; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .card td {
            color: #1e293b;
            font-size: 0.9375rem;
        }
        .card tbody tr:hover { background-color: #faf5ff; }
        .card tbody tr:last-child td { border-bottom: none; }
        
        /* Status Badge Styling */
        .badge {
            display: inline-block;
            padding: 0.3em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .badge-info { /* Upcoming */
            color: #0c5460;
            background-color: #d1ecf1;
        }
        .badge-success { /* Completed/Sent */
            color: #155724;
            background-color: #d4edda;
        }
        .badge-secondary { /* Completed/Sent but less urgent */
            color: #495057;
            background-color: #e2e3e5;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🔔 Reminders</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Stay on top of your pet's care schedule.</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Pet Name</th>
                        <th>Reminder/Title</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // 3. Loop through results and display data
                    if ($reminders_query && $reminders_query->num_rows > 0): 
                        while ($reminder = $reminders_query->fetch_assoc()):
                            $status = htmlspecialchars($reminder['status']);
                            $pet_name = htmlspecialchars($reminder['pet_name'] ?: 'General');
                            
                            // Determine badge style based on status
                            if ($status === 'Upcoming') {
                                $badge_class = 'badge-info';
                            } else {
                                $badge_class = 'badge-secondary'; // Sent or Completed
                            }
                    ?>
                    <tr>
                        <td><?= $pet_name ?></td>
                        <td><?= htmlspecialchars($reminder['title']) ?></td>
                        <td><?= date('Y-m-d', strtotime($reminder['reminder_date'])) ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= $status ?></span></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">
                            No reminders found for your schedule.
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