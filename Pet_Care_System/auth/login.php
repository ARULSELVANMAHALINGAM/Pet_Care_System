<?php
session_start();
include('../config/db.php');
include('../config/activity_logger.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Prepare login query
    $sql = "SELECT * FROM users WHERE email=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        // Verify password
        if (password_verify($password, $user['password'])) {

            // FIXED SESSION VARIABLE (CORRECT COLUMN = id)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Log login activity
            logActivity($conn, $user['id'], 'User logged in', 'Login successful', $user['role']);

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] == 'vet') {
                header("Location: ../veterinarian/dashboard.php");
            } else {
                header("Location: ../owner/dashboard.php");
            }
            exit;

        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or role mismatch.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PetCare System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 25%, #ede9fe 50%, #f5f3ff 75%, #faf5ff 100%);
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            display: flex;
            width: 90%;
            max-width: 1000px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.15),
                        0 10px 10px -5px rgba(139, 92, 246, 0.1);
            overflow: hidden;
            min-height: 550px;
        }
        .login-image {
            flex: 1;
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .login-box {
            flex: 1;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        h2 {
            text-align: center;
            margin-bottom: 32px;
            background: linear-gradient(135deg, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.875rem;
            font-weight: 700;
        }
        .error {
            color: #dc2626;
            text-align: center;
            padding: 12px;
            background: rgba(239,68,68,0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(239,68,68,0.2);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group:first-of-type {
            margin-top: 0;
        }
        label { 
            font-weight: 500; 
            margin-bottom: 8px; 
            display: block;
            font-size: 0.9em;
            color: #555;
        }
        input, select {
            width: 100%; padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
        }
        input:focus, select:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.15);
            outline: none;
        }
        button {
            width: 100%;
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .link {
            text-align: center;
            margin-top: 24px;
        }
        .link a { color: #a78bfa; text-decoration: none; font-weight: 600; }
        .link a:hover { color: #8b5cf6; }
        
        /* Role Selection Buttons */
        .role-selection {
            margin-bottom: 24px;
        }
        .role-selection label {
            display: block;
            font-weight: 500;
            margin-bottom: 12px;
            color: #1e293b;
            font-size: 0.9375rem;
        }
        .role-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .role-btn {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #f0f2f5;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
        }
        .role-btn i {
            font-size: 1.5rem;
            color: #888;
        }
        .role-btn span {
            font-size: 0.9375rem;
            font-weight: 500;
            color: #555;
        }
        .role-btn input[type="radio"] {
            display: none;
        }
        .role-btn:hover {
            border-color: #a78bfa;
            background: #faf5ff;
        }
        .role-btn:hover i {
            color: #8b5cf6;
        }
        .role-btn:hover span {
            color: #8b5cf6;
        }
        .role-btn.selected {
            border-color: #a78bfa;
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
        }
        .role-btn.selected i {
            color: white;
        }
        .role-btn.selected span {
            color: white;
        }
        
        @media (max-width: 800px) {
            .login-container {
                flex-direction: column;
            }
            .login-image {
                min-height: 250px;
            }
            .role-buttons {
                flex-direction: column;
            }
            .role-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-image">
        <img src="../assets/images/register2.png" alt="PetCare Illustration">
    </div>
    <div class="login-box">
        <h2 style="text-align: left; margin-bottom: 8px; font-size: 1.75rem; font-weight: 700;">Welcome back</h2>
        
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" id="loginForm">
        <div class="form-group">
            <label>Select Login Type</label>
            <div class="role-buttons">
                <label class="role-btn" for="role_owner">
                    <i class="ri-user-line"></i>
                    <span>Pet Owner</span>
                    <input type="radio" name="role" id="role_owner" value="owner" required>
                </label>
                <label class="role-btn" for="role_vet">
                    <i class="ri-user-heart-line"></i>
                    <span>Veterinarian</span>
                    <input type="radio" name="role" id="role_vet" value="vet" required>
                </label>
                <label class="role-btn" for="role_admin">
                    <i class="ri-shield-user-line"></i>
                    <span>Admin</span>
                    <input type="radio" name="role" id="role_admin" value="admin" required>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            <div style="text-align: right; margin-top: 8px;">
                <a href="#" style="color: #a78bfa; text-decoration: none; font-size: 0.875rem;">Forgot password?</a>
            </div>
        </div>

        <button type="submit">
            Sign in <i class="ri-arrow-right-line"></i>
        </button>
        
        <div class="link" style="margin-top: 24px; text-align: center;">
            Don't have an account? <a href="register.php">Register here</a>
        </div>

    </form>

    </div>
</div>

<script>
    // Handle role button selection
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove selected class from all buttons
            document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('selected'));
            // Add selected class to clicked button
            this.classList.add('selected');
            // Check the radio button
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
    
    // Check if a role is already selected on page load
    document.querySelectorAll('input[type="radio"][name="role"]').forEach(radio => {
        if (radio.checked) {
            radio.closest('.role-btn').classList.add('selected');
        }
    });
</script>

</body>
</html>
