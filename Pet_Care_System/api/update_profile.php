<?php
include('../config/db.php');
header('Content-Type: application/json');

$id = $_POST['id'];
$username = $_POST['username'];
$email = $_POST['email'];

$stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
$stmt->bind_param("ssi", $username, $email, $id);

$response = [];
if($stmt->execute()) {
  $response['status'] = 'success';
} else {
  $response['status'] = 'error';
}
echo json_encode($response);
?>
