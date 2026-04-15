<?php
include('../includes/header.php');
include('../includes/sidebar.php');
include('../includes/navbar.php');
?>

<div class="main-content">
  <h2>✏️ Update Clinic Visit</h2>
  <form method="POST" action="save_update.php">
    <input type="text" name="visit_id" placeholder="Visit ID" required>
    <input type="date" name="visit_date">
    <input type="text" name="reason" placeholder="Updated Reason">
    <select name="status">
      <option value="Scheduled">Scheduled</option>
      <option value="Completed">Completed</option>
      <option value="Cancelled">Cancelled</option>
    </select>
    <button type="submit">Update Visit</button>
  </form>
</div>

<?php include('../includes/footer.php'); ?>
