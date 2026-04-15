<?php
// Basic security functions

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function check_session() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function check_role($role) {
    if ($_SESSION['role'] !== $role) {
        header("Location: ../auth/login.php?error=unauthorized");
        exit;
    }
}
?>
