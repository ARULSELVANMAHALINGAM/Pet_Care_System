<?php
if (!isset($_SESSION)) session_start();
$app_name = "PetCare Management System";
$user_name = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? 'guest';
?>
<header class="topbar">
  <div class="topbar-left">
    <span class="logo"><span style="color: #000;">🐾</span> <?php echo $app_name; ?></span>
  </div>

  <div class="topbar-right">
    <span class="user">
      <i class="ri-user-line"></i>
      <span><?php echo ucfirst($user_name); ?></span>
      <span class="user-badge"><?php echo ucfirst($user_role); ?></span>
    </span>
    <a href="../auth/logout.php" class="logout-btn">
      <i class="ri-logout-box-r-line"></i>
      <span>Logout</span>
    </a>
  </div>
</header>
