<?php
session_start();
include('../config/db.php');

// Only vets can update
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vet') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch all pets
$pets_query = mysqli_query($conn, "SELECT id, name, species FROM pets");

// Get vaccination ID from URL
$vaccination_id = $_GET['id'] ?? '';

// Fetch existing vaccination data
$vaccination = null;
if (!empty($vaccination_id)) {
    $vaccination_id_escaped = mysqli_real_escape_string($conn, $vaccination_id);
    $vaccination_query = mysqli_query($conn, "SELECT * FROM vaccinations WHERE id = '$vaccination_id_escaped'");
    if ($vaccination_query && mysqli_num_rows($vaccination_query) > 0) {
        $vaccination = mysqli_fetch_assoc($vaccination_query);
    }
}

// Fetch all vaccinations for listing (when no ID provided)
$all_vaccinations_query = mysqli_query($conn, 
    "SELECT v.*, p.name AS pet_name, p.species 
     FROM vaccinations v 
     JOIN pets p ON v.pet_id = p.id 
     ORDER BY v.date DESC, v.id DESC"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Vaccination | PetCare</title>
    <?php include('../includes/head.php'); ?>

    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }
        .form-group { margin-bottom: 18px; }
        label { font-weight: 600; }
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
    </style>
</head>
<body>

<?php include('../includes/header.php'); ?>
<?php include('../includes/sidebar.php'); ?>

<div class="main-content">

    <h1 style="font-size: 2rem; font-weight:700; color:#1e293b;">💉 Manage Vaccinations</h1>
    <p style="color:#64748b;">Add new vaccinations or modify existing records.</p>

    <?php if (empty($vaccination_id)): ?>
        <!-- Show add form and list of vaccinations -->
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= $_GET['success'] == 'added' ? 'Vaccination added successfully!' : 'Vaccination deleted successfully!' ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Vaccination Form -->
        <div class="form-container" style="margin-bottom: 30px;">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">➕ Add New Vaccination</h2>
            <form method="POST" action="save_vaccination.php">
                <div class="form-group">
                    <label for="pet_id">Select Pet</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Select Pet</option>
                        <?php
                        mysqli_data_seek($pets_query, 0);
                        while ($pet = mysqli_fetch_assoc($pets_query)): ?>
                            <option value="<?= $pet['id'] ?>">
                                <?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vaccine_name">Vaccine Name</label>
                    <input type="text" id="vaccine_name" name="vaccine_name" placeholder="e.g., Rabies, DHPP" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Add Vaccination</button>
            </form>
        </div>
        
        <!-- Vaccinations List -->
        <div class="card">
            <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 1.5rem;">📋 Existing Vaccinations</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pet</th>
                        <th>Vaccine</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($all_vaccinations_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($all_vaccinations_query)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['pet_name']) ?> (<?= htmlspecialchars($row['species']) ?>)</td>
                            <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'Completed' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="actions" style="display: flex; gap: 8px; justify-content: center;">
                                <a href="update_vaccination.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_vaccination.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vaccination?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                                No vaccinations found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($error_message) || !$vaccination): ?>
        <div class="card" style="background: #fee; border-color: #fcc; color: #c33; padding: 20px; margin-bottom: 20px;">
            <p><?= htmlspecialchars($error_message ?? "Vaccination record not found.") ?></p>
            <a href="update_vaccination.php" class="btn btn-secondary">Back to Vaccinations List</a>
        </div>
    <?php else: ?>
    <div class="form-container">

        <?php if (isset($_GET['success'])): ?>
            <div style="background: #dfd; border: 1px solid #4a4; color: #040; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                Vaccination updated successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fdd; border: 1px solid #f44; color: #400; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="update_vaccine_save.php">
            <input type="hidden" name="vaccination_id" value="<?= htmlspecialchars($vaccination_id); ?>">

            <div class="form-group">
                <label for="pet_id">Select Pet</label>
                <select id="pet_id" name="pet_id" required>
                    <option value="">Select Pet</option>

                    <?php
                    mysqli_data_seek($pets_query, 0);
                    while ($pet = mysqli_fetch_assoc($pets_query)): ?>
                        <option value="<?= $pet['id'] ?>" <?= ($vaccination && $vaccination['pet_id'] == $pet['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)
                        </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="form-group">
                <label for="vaccine_name">Vaccine Name</label>
                <input type="text" id="vaccine_name" name="vaccine_name" value="<?= $vaccination ? htmlspecialchars($vaccination['vaccine_name']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="date_given">Date Given</label>
                <input type="date" id="date_given" name="date_given" value="<?= $vaccination ? htmlspecialchars($vaccination['date']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Pending" <?= ($vaccination && $vaccination['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Completed" <?= ($vaccination && $vaccination['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Update</button>
            <a href="update_vaccination.php" class="btn btn-secondary" style="width:100%; margin-top: 10px;">Cancel</a>

        </form>
    </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
