<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark notification as read if requested
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notif_id = mysqli_real_escape_string($conn, $_GET['mark_read']);
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE id = '$notif_id' AND user_id = '$user_id'");
    header("Location: view_notifications.php");
    exit;
}

// Fetch notifications for this user
$notifications_query = mysqli_query($conn, 
    "SELECT * FROM notifications 
     WHERE user_id = '$user_id' 
     ORDER BY created_at DESC, is_read ASC"
);

// Count unread notifications
$unread_count = 0;
$unread_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM notifications WHERE user_id = '$user_id' AND is_read = 0");
if ($unread_query) {
    $unread_row = mysqli_fetch_assoc($unread_query);
    $unread_count = $unread_row['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .notification-item {
            background: var(--bg-primary);
            padding: 20px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .notification-item:hover {
            box-shadow: var(--shadow-md);
        }
        .notification-item.unread {
            border-left: 4px solid #a78bfa;
            background: rgba(167, 139, 250, 0.08);
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 1.1rem;
        }
        .notification-date {
            color: #64748b;
            font-size: 0.9rem;
        }
        .notification-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .type-reminder { background: #fef3c7; color: #92400e; }
        .type-vaccination { background: #dbeafe; color: #1e40af; }
        .type-visit { background: #d1fae5; color: #065f46; }
        .type-general { background: #f3f4f6; color: #374151; }
        .notification-message {
            color: #475569;
            line-height: 1.6;
        }
        .mark-read-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🔔 Notifications</h1>
            <p style="color: #64748b; font-size: 0.9375rem;">
                <?php if ($unread_count > 0): ?>
                    You have <strong><?= $unread_count ?></strong> unread notification<?= $unread_count > 1 ? 's' : '' ?>.
                <?php else: ?>
                    All caught up! No new notifications.
                <?php endif; ?>
            </p>
        </div>
        
        <?php if (mysqli_num_rows($notifications_query) > 0): ?>
            <?php while($notif = mysqli_fetch_assoc($notifications_query)): ?>
            <div class="notification-item <?= $notif['is_read'] == 0 ? 'unread' : '' ?>">
                <div class="notification-header">
                    <div>
                        <span class="notification-type type-<?= strtolower($notif['type'] ?? 'general') ?>">
                            <?= htmlspecialchars(ucfirst($notif['type'] ?? 'General')) ?>
                        </span>
                        <div class="notification-title"><?= htmlspecialchars($notif['title']) ?></div>
                    </div>
                    <div class="notification-date">
                        <?= date('M d, Y H:i', strtotime($notif['created_at'])) ?>
                    </div>
                </div>
                <div class="notification-message">
                    <?= nl2br(htmlspecialchars($notif['message'])) ?>
                </div>
                <?php if ($notif['is_read'] == 0): ?>
                    <a href="view_notifications.php?mark_read=<?= $notif['id'] ?>" class="btn btn-secondary mark-read-btn" style="font-size: 0.9rem; padding: 6px 12px;">
                        Mark as Read
                    </a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 40px;">
                <div style="font-size: 3rem; margin-bottom: 20px;">📭</div>
                <h2 style="color: #64748b; margin-bottom: 10px;">No Notifications</h2>
                <p style="color: #94a3b8;">You don't have any notifications yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>

