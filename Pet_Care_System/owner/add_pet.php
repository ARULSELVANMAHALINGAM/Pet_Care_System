<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Success / Error Messages
if (isset($_GET['success'])) {
    $message = "Pet added successfully!";
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Fetch Pets
$pets_query = mysqli_query($conn, "SELECT * FROM pets WHERE owner_id = '$owner_id' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet | PetCare</title>
    <?php include('../includes/head.php'); ?>

    <style>
        .form-container {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .message { text-align:center; padding:12px 20px; border-radius: var(--radius-md); margin-bottom:20px; font-weight:500; }
        .message.success { background: rgba(16,185,129,0.1); color: var(--success-color); border: 1px solid rgba(16,185,129,0.2); }
        .message.error { background: rgba(239,68,68,0.1); color: var(--danger-color); border: 1px solid rgba(239,68,68,0.2); }

        .pets-grid {
            display:grid; 
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:24px; margin-top:24px;
        }
        .pet-card {
            background:var(--bg-primary);
            padding:24px;
            border-radius:var(--radius-lg);
            box-shadow:var(--shadow-sm);
            border:1px solid var(--border-color);
            transition:0.3s;
        }
        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .pet-icon { font-size:3rem; margin-bottom:10px; }
        .pet-name { font-size:1.4rem; font-weight:700; color:var(--text-primary); }
        .pet-info { font-size:0.95rem; color:var(--text-secondary); }
        .pet-actions { margin-top:15px; display:flex; gap:10px; }
    </style>
    <script>
        function getPetEmoji(species) {
            const emojiMap = {
                'Dog': '🐕',
                'Cat': '🐱',
                'Bird': '🐦',
                'Parrot': '🦜',
                'Rabbit': '🐰',
                'Hamster': '🐹',
                'Fish': '🐠',
                'Turtle': '🐢',
                'Snake': '🐍',
                'Lizard': '🦎',
                'Guinea Pig': '🐹',
                'Ferret': '🦡',
                'Other': '🐾'
            };
            return emojiMap[species] || '🐾';
        }
        
        function updatePetIcon() {
            const species = document.getElementById('species').value;
            const preview = document.getElementById('pet-icon-preview');
            if (species) {
                preview.innerHTML = getPetEmoji(species);
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</head>

<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/sidebar.php'); ?>

<div class="main-content">

    <center>
        <h1 style="font-size: 2rem; font-weight: 700; color:#1e293b;">➕ Add Pet</h1>
        <p style="color:#64748b;">Add a new pet to your account.</p>
    </center>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2 style="margin-bottom:20px;">Add New Pet</h2>

        <form method="POST" action="save_pet.php">
            <div class="form-group">
                <label>Pet Name</label>
                <input type="text" name="pet_name" placeholder="Enter pet name" required>
            </div>

            <div class="form-group">
                <label>Species</label>
                <select name="species" id="species" required onchange="updatePetIcon()">
                    <option value="">-- Select Species --</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Bird">Bird</option>
                    <option value="Parrot">Parrot</option>
                    <option value="Rabbit">Rabbit</option>
                    <option value="Hamster">Hamster</option>
                    <option value="Fish">Fish</option>
                    <option value="Turtle">Turtle</option>
                    <option value="Snake">Snake</option>
                    <option value="Lizard">Lizard</option>
                    <option value="Guinea Pig">Guinea Pig</option>
                    <option value="Ferret">Ferret</option>
                    <option value="Other">Other</option>
                </select>
                <div id="pet-icon-preview" style="font-size: 3rem; text-align: center; margin-top: 10px; min-height: 50px;"></div>
            </div>

            <div class="form-group">
                <label>Breed</label>
                <input type="text" name="breed" placeholder="Enter breed" required>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" required>
            </div>

            <button class="btn btn-primary" style="width:100%;">Add Pet</button>
        </form>
    </div>

    <div class="card">
        <h2 style="margin-bottom:20px;">My Pets</h2>

        <?php if (mysqli_num_rows($pets_query) > 0): ?>
            <div class="pets-grid">
                <?php while($pet = mysqli_fetch_assoc($pets_query)): ?>
                    <?php
                        $species = $pet['species'];
                        $iconMap = [
                            'Dog' => '🐕',
                            'Cat' => '🐱',
                            'Bird' => '🐦',
                            'Parrot' => '🦜',
                            'Rabbit' => '🐰',
                            'Hamster' => '🐹',
                            'Fish' => '🐠',
                            'Turtle' => '🐢',
                            'Snake' => '🐍',
                            'Lizard' => '🦎',
                            'Guinea Pig' => '🐹',
                            'Ferret' => '🦡'
                        ];
                        $icon = $iconMap[$species] ?? '🐾';
                    ?>
                    <div class="pet-card">
                        <div class="pet-icon"><?= $icon ?></div>
                        <div class="pet-name"><?= htmlspecialchars($pet['name']) ?></div>
                        <div class="pet-info"><strong>Species:</strong> <?= htmlspecialchars($pet['species']) ?></div>
                        <div class="pet-info"><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']) ?></div>
                        <div class="pet-info"><strong>DOB:</strong> <?= date('M d, Y', strtotime($pet['dob'])) ?></div>

                        <div class="pet-actions">
                            <a href="update_pet_details.php?id=<?= $pet['id'] ?>" class="btn btn-secondary" style="flex:1;">Edit</a>
                            <a href="delete_pet.php?id=<?= $pet['id'] ?>" class="btn btn-danger" style="flex:1;"
                               onclick="return confirm('Delete this pet?')">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center; padding:20px; color:var(--text-muted);">
                No pets added yet.
            </p>
        <?php endif; ?>
    </div>

</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
