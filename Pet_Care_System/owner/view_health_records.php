<?php
session_start();
include('../config/db.php');

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

$sql = "
    SELECT hr.date, hr.diagnosis, p.name AS pet_name, v.username AS vet_name, hr.id AS record_id
    FROM health_records hr
    JOIN pets p ON hr.pet_id = p.id
    LEFT JOIN users v ON hr.vet_id = v.id
    WHERE p.owner_id = ?
    ORDER BY hr.date DESC
";

$records_query = false;
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $records_query = $stmt->get_result();
    } else {
        error_log("Health records query failed: " . $stmt->error);
    }
} else {
    error_log("Statement preparation failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Health Records | PetCare</title>
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
            padding: 14px 16px;
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
            font-size: 0.9375rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .card tbody tr:hover {
            background-color: #faf5ff;
        }
        .card tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>

    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">📋 Health Records</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">View your pet's complete health history.</p>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Diagnosis</th>
                        <th>Vet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records_query && $records_query->num_rows > 0): ?>
                        <?php while ($record = $records_query->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('Y-m-d', strtotime($record['date'])) ?></td>
                                <td><?= htmlspecialchars($record['pet_name']) ?></td>
                                <td><?= htmlspecialchars($record['diagnosis']) ?></td>
                                <td><?= htmlspecialchars($record['vet_name'] ?: 'N/A') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; padding: 40px;">
                                <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                                <p style="font-size: 1.1rem; margin: 0;">No health records found for your pets.</p>
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
