<?php
include('../includes/header.php');
include('../includes/sidebar.php');
include('../includes/navbar.php');
?>

<div class="main-content">
  <h2>➕ Create Reminder</h2>
  <form method="POST" action="save_reminder.php">
    <input type="text" name="title" placeholder="Reminder Title" required>
    <input type="date" name="reminder_date" required>
    <textarea name="message" placeholder="Reminder Message" required></textarea>
    <button type="submit">Create Reminder</button>
  </form>
</div>

<?php include('../includes/footer.php'); ?>
