<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function get_current_page() {
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    return basename($path);
}
$current_page = get_current_page();
$role = $_SESSION['role'] ?? 'guest';
?>

<div class="sidebar-logo">
    <img src="../assets/images/login.png" alt="PetCare Logo">
</div>
<div class="sidebar">
  <ul>
    <?php if ($role === 'admin') { ?>
      <li><a href="../admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="ri-dashboard-line"></i> Dashboard</a></li>
      <li><a href="../admin/manage_users.php" class="<?= $current_page == 'manage_users.php' ? 'active' : '' ?>"><i class="ri-group-line"></i> Manage Users</a></li>
      <li><a href="../admin/manage_pets.php" class="<?= $current_page == 'manage_pets.php' ? 'active' : '' ?>"><i class="ri-pet-line"></i> Manage Pets</a></li>
      <li><a href="../admin/manage_veterinarians.php" class="<?= $current_page == 'manage_veterinarians.php' ? 'active' : '' ?>"><i class="ri-user-star-line"></i> Manage Vets</a></li>
      <li><a href="../admin/manage_records.php" class="<?= $current_page == 'manage_records.php' ? 'active' : '' ?>"><i class="ri-file-list-3-line"></i> Health Records</a></li>
      <li><a href="../admin/manage_notifications.php" class="<?= $current_page == 'manage_notifications.php' ? 'active' : '' ?>"><i class="ri-notification-3-line"></i> Notifications</a></li>
      <li><a href="../admin/view_reports.php" class="<?= $current_page == 'view_reports.php' ? 'active' : '' ?>"><i class="ri-bar-chart-box-line"></i> Reports</a></li>
      <li><a href="../admin/activity_logs.php" class="<?= $current_page == 'activity_logs.php' ? 'active' : '' ?>"><i class="ri-history-line"></i> Activity Logs</a></li>
    <?php } elseif ($role === 'vet') { ?>
      <li><a href="../veterinarian/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="ri-dashboard-line"></i> Dashboard</a></li>
      <li><a href="../veterinarian/update_vaccination.php" class="<?= $current_page == 'update_vaccination.php' ? 'active' : '' ?>"><i class="ri-syringe-line"></i> Update Vaccination</a></li>
      <li><a href="../veterinarian/add_health_record.php" class="<?= $current_page == 'add_health_record.php' ? 'active' : '' ?>"><i class="ri-file-add-line"></i> Add Health Record</a></li>
      <li><a href="../veterinarian/manage_clinic_visit.php" class="<?= $current_page == 'manage_clinic_visit.php' ? 'active' : '' ?>"><i class="ri-calendar-check-line"></i> Clinic Visits</a></li>
      <li><a href="../veterinarian/add_care_instruction.php" class="<?= $current_page == 'add_care_instruction.php' ? 'active' : '' ?>"><i class="ri-booklet-line"></i> Care Instructions</a></li>
      <li><a href="../veterinarian/view_pet_history.php" class="<?= $current_page == 'view_pet_history.php' ? 'active' : '' ?>"><i class="ri-archive-line"></i> Pet History</a></li>
      <li><a href="../veterinarian/generate_report.php" class="<?= $current_page == 'generate_report.php' ? 'active' : '' ?>"><i class="ri-file-chart-line"></i> Generate Report</a></li>
    <?php } elseif ($role === 'owner') { ?>
      <li><a href="../owner/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="ri-dashboard-line"></i> Dashboard</a></li>
      <li><a href="../owner/add_pet.php" class="<?= $current_page == 'add_pet.php' ? 'active' : '' ?>"><i class="ri-add-line"></i> Add Pet</a></li>
      <li><a href="../owner/view_pet_profile.php" class="<?= $current_page == 'view_pet_profile.php' ? 'active' : '' ?>"><i class="ri-profile-line"></i> Pet Profile</a></li>
      <li><a href="../owner/view_health_records.php" class="<?= $current_page == 'view_health_records.php' ? 'active' : '' ?>"><i class="ri-file-list-line"></i> Health Records</a></li>
      <li><a href="../owner/view_vaccination_schedule.php" class="<?= $current_page == 'view_vaccination_schedule.php' ? 'active' : '' ?>"><i class="ri-calendar-line"></i> Vaccination Schedule</a></li>
      <li><a href="../owner/view_clinic_visits.php" class="<?= $current_page == 'view_clinic_visits.php' ? 'active' : '' ?>"><i class="ri-hospital-line"></i> Clinic Visits</a></li>
      <li><a href="../owner/view_care_instructions.php" class="<?= $current_page == 'view_care_instructions.php' ? 'active' : '' ?>"><i class="ri-book-read-line"></i> Care Instructions</a></li>
      <li><a href="../owner/reminders.php" class="<?= $current_page == 'reminders.php' ? 'active' : '' ?>"><i class="ri-alarm-line"></i> Reminders</a></li>
      <li><a href="../owner/view_notifications.php" class="<?= $current_page == 'view_notifications.php' ? 'active' : '' ?>"><i class="ri-notification-3-line"></i> Notifications</a></li>
    <?php } ?>
    <li><a href="../auth/logout.php"><i class="ri-logout-box-line"></i> Logout</a></li>
  </ul>
</div>
