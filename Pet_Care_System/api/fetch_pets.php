<?php
include('../config/db.php');
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM pets");
$pets = [];
while($row = $result->fetch_assoc()) {
  $pets[] = $row;
}
echo json_encode($pets);
?>
