<?php
session_start();
include('../config/db.php');

// --- 1. Authorization Check ---
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'vet')) {
    header("Location: ../auth/login.php");
    exit;
}

$vaccine_id = $_GET['id'] ?? null;
$vaccine_data = null;
$message = '';

// --- 2. Fetch Existing Vaccination Data ---
if ($vaccine_id) {
    $sql_fetch = "SELECT pet_id, vaccine_name, date, status, notes FROM vaccinations WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);

    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $vaccine_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();

        if ($result->num_rows > 0) {
            $vaccine_data = $result->fetch_assoc();
        } else {
            $message = "Error: Vaccination record not found.";
            $vaccine_id = null;
        }
        $stmt_fetch->close();
    } else {
        $message = "Database error preparing fetch statement.";
    }
} else {
    $message = "Error: No Vaccination ID provided for update.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $vaccine_id ? 'Update' : 'View' ?> Vaccine | PetCare</title>
<?php include('../includes/head.php'); ?>
<style>
.main-content { margin-left: 250px; margin-top: 60px; padding: 30px; }
.form-container {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border: 1px solid #e0e0e0;
    max-width: 600px;
    margin: 0 auto;
}
.form-group { margin-bottom: 20px; }
.form-group label { font-weight: 600; color: #333; display: block; margin-bottom: 6px; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%; padding: 10px; border-radius: 4px;
    border: 1px solid #ccc; box-sizing: border-box;
}
</style>
</head>

<body>

<?php include('../includes/header.php'); ?>
<?php include('../includes/sidebar.php'); ?>

<div class="main-content">

    <h1 style="font-size: 2rem; font-weight: 700; color:#1e293b; margin-bottom: 8px;">
        ✏️ Update Vaccine
    </h1>
    <p style="color: #64748b;">Update vaccination information for the pet.</p>

    <?php if ($message): ?>
        <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;max-width:600px;margin:10px auto;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <?php if ($vaccine_data): ?>
        
        <form action="save_update.php" method="POST">
            
            <input type="hidden" name="vaccine_id" value="<?= htmlspecialchars($vaccine_id) ?>">

            <div class="form-group">
                <label>Pet ID</label>
                <p style="font-weight:700;color:#4f46e5;"><?= htmlspecialchars($vaccine_data['pet_id']) ?></p>
                <input type="hidden" name="pet_id" value="<?= htmlspecialchars($vaccine_data['pet_id']) ?>">
            </div>

            <div class="form-group">
                <label>Vaccine Name</label>
                <input type="text" name="vaccine_name" required
                       value="<?= htmlspecialchars($vaccine_data['vaccine_name']) ?>">
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" required
                       value="<?= htmlspecialchars($vaccine_data['date']) ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <?php 
                        $options = ['Pending', 'Completed'];
                        foreach ($options as $opt) {
                            $sel = ($opt === $vaccine_data['status']) ? 'selected' : '';
                            echo "<option value='$opt' $sel>$opt</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" rows="3"><?= htmlspecialchars($vaccine_data['notes']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Update Vaccine</button>
        </form>

        <?php else: ?>
            <p>Cannot load the vaccination record. Please check the ID and try again.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
