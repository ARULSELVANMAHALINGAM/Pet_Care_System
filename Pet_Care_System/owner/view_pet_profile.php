<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$pets_query = mysqli_query($conn, "SELECT * FROM pets WHERE owner_id = $user_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Pets | PetCare</title>
  <?php include('../includes/head.php'); ?>
  <style>
    .pets-wrapper {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
      margin-top: 24px;
    }
    .pet-card {
      background: #fff;
      padding: 24px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
      border: 1px solid #e9d5ff;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .pet-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    }
    .pet-card h2 {
      font-size: 1.75rem;
      margin-bottom: 20px;
      color: #1e293b;
      padding-bottom: 15px;
      border-bottom: 2px solid #f1f5f9;
    }
    .pet-card table {
      width: 100%;
      border-collapse: collapse;
    }
    .pet-card th {
      text-align: left;
      padding: 12px 0;
      color: #64748b;
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      width: 35%;
    }
    .pet-card td {
      padding: 12px 0;
      color: #1e293b;
      font-size: 1rem;
    }
    .pet-card tr {
      border-bottom: 1px solid #f1f5f9;
    }
    .pet-card tr:last-child {
      border-bottom: none;
    }
  </style>
</head>
<body>
  <?php include('../includes/header.php'); ?>
  <?php include('../includes/sidebar.php'); ?>

<div class="main-content">
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">🐾 My Pets</h1>
    <p style="color: #64748b; font-size: 0.9375rem;">View all your registered pets.</p>
  </div>

  <?php if (mysqli_num_rows($pets_query) > 0): ?>
    <div class="pets-wrapper">
      <?php while ($pet = mysqli_fetch_assoc($pets_query)): ?>
        <div class="pet-card">
          <h2>🐶 <?= htmlspecialchars($pet['name']) ?>'s Profile</h2>
          <table>
            <tr><th>Name</th><td><?= htmlspecialchars($pet['name']) ?></td></tr>
            <tr><th>Type</th><td><?= htmlspecialchars($pet['species']) ?></td></tr>
            <tr><th>Breed</th><td><?= htmlspecialchars($pet['breed']) ?></td></tr>
            <tr><th>Birthdate</th><td><?= htmlspecialchars($pet['dob']) ?></td></tr>
            <tr><th>Owner</th><td><?= htmlspecialchars($username) ?></td></tr>
          </table>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="card" style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08); border: 1px solid #e9d5ff;">
      <div style="font-size: 3rem; margin-bottom: 15px;">🐾</div>
      <h2 style="color: #64748b; margin-bottom: 8px; font-size: 1.25rem;">No Pets Found</h2>
      <p style="color: #94a3b8; margin-bottom: 20px; font-size: 0.9375rem;">Get started by adding your first pet!</p>
      <a href="add_pet.php" class="btn btn-primary">Add Pet</a>
    </div>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
