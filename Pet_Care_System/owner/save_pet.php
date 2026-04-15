<?php
session_start();
include('../config/db.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    die("Error: User not logged in.");
}

$owner_id = $_SESSION['user_id'];

// Read form values (MATCHING add_pet.php)
$pet_name = $_POST['pet_name'] ?? '';
$species  = $_POST['species'] ?? '';
$breed    = $_POST['breed'] ?? '';
$dob      = $_POST['dob'] ?? '';

// Validate
if (empty($pet_name) || empty($species) || empty($breed) || empty($dob)) {
    header("Location: add_pet.php?error=All fields are required.");
    exit;
}

// Check owner exists
$checkUser = mysqli_query($conn, "SELECT id FROM users WHERE id = '$owner_id'");
if (mysqli_num_rows($checkUser) == 0) {
    header("Location: add_pet.php?error=Owner ID does not exist.");
    exit;
}

// INSERT into pets table using a prepared statement
$insert_sql = "INSERT INTO pets (owner_id, name, species, breed, dob) VALUES (?, ?, ?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'issss', $owner_id, $pet_name, $species, $breed, $dob);
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        header("Location: add_pet.php?success=1");
        exit;
    } else {
        $dbErr = mysqli_stmt_error($insert_stmt);
        mysqli_stmt_close($insert_stmt);
        header("Location: add_pet.php?error=" . urlencode('Database Error: ' . $dbErr));
        exit;
    }
} else {
    header("Location: add_pet.php?error=" . urlencode('Database Error: could not prepare insert.' . mysqli_error($conn)));
    exit;
}
?>
