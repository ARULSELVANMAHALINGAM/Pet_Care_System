<?php
include('../includes/header.php');
include('../includes/sidebar.php');
include('../includes/navbar.php');
?>

<div class="main-content">
  <h2>📤 Send Reminder</h2>
  <form method="POST" action="process_send.php">
    <input type="text" name="reminder_id" placeholder="Reminder ID" required>
    <select name="method" required>
      <option value="email">Email</option>
      <option value="sms">SMS</option>
      <option value="app">App Notification</option>
    </select>
    <button type="submit">Send Reminder</button>
  </form>
</div>

<?php include('../includes/footer.php'); ?>
