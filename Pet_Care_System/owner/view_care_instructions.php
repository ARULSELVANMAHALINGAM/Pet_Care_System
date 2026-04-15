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

// 2. Fetch care instructions (Securely using Prepared Statement)
$sql = "
    SELECT 
        ci.instruction, 
        ci.created_at,
        p.name AS pet_name, 
        u.username AS vet_name 
    FROM 
        care_instructions ci
    JOIN 
        pets p ON ci.pet_id = p.id
    LEFT JOIN
        users u ON ci.vet_id = u.id
    WHERE 
        p.owner_id = ? 
    ORDER BY 
        ci.created_at DESC
";

$instructions_query = false;
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $instructions_query = mysqli_stmt_get_result($stmt);
    } else {
        error_log("Care instructions query failed: " . mysqli_stmt_error($stmt));
    }
} else {
    error_log("Statement preparation failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Care Instructions | PetCare</title>
  <?php include('../includes/head.php'); ?>
    <style>
        .card { 
            background: #fff; 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08); 
            border: 1px solid #e9d5ff; 
        }
        .instructions-list { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }
        .instructions-list li { 
            padding: 18px; 
            margin-bottom: 14px; 
            background: #faf5ff; 
            border-radius: 10px; 
            border-left: 4px solid #a78bfa; 
            box-shadow: 0 1px 3px rgba(139, 92, 246, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .instructions-list li:hover {
            transform: translateX(4px);
            box-shadow: 0 2px 6px rgba(139, 92, 246, 0.15);
        }
        .instruction-text { 
            font-size: 0.9375rem; 
            color: #1e293b; 
            font-weight: 500; 
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .instruction-meta { 
            font-size: 0.8125rem; 
            color: #64748b; 
            display: flex; 
            justify-content: space-between; 
            flex-wrap: wrap;
            gap: 10px;
        }
        .instruction-meta span { 
            margin-right: 10px; 
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>

    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">📋 Care Instructions</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">Follow these care instructions for your pet's health.</p>
        </div>

        <div class="card">
            <ul class="instructions-list">
                <?php if ($instructions_query && mysqli_num_rows($instructions_query) > 0): ?>
                    <?php while ($instruction = mysqli_fetch_assoc($instructions_query)): ?>
                        <li>
                            <div class="instruction-text"><?= htmlspecialchars($instruction['instruction']) ?></div>
                            <div class="instruction-meta">
                                <span>Pet: <?= htmlspecialchars($instruction['pet_name']) ?></span>
                                <span>Vet: <?= htmlspecialchars($instruction['vet_name'] ?: 'N/A') ?> | <?= date('Y-m-d', strtotime($instruction['created_at'])) ?></span>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li style="text-align: center; border-left: 4px solid #e2e8f0; background: #fff; padding: 40px;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                        <p style="font-size: 1.1rem; color: #64748b; margin: 0;">No care instructions found for your pets.</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <?php 
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    include('../includes/footer.php'); 
    ?>
</body>
</html>