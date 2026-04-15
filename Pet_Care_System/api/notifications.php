<?php
include('../config/db.php');
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM reminders ORDER BY reminder_date ASC");
$reminders = [];
while($row = $result->fetch_assoc()) {
  $reminders[] = $row;
}
echo json_encode($reminders);
?>
