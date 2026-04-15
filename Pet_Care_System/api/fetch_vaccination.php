<?php
include('../config/db.php');
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM vaccinations");
$vaccines = [];
while($row = $result->fetch_assoc()) {
  $vaccines[] = $row;
}
echo json_encode($vaccines);
?>
