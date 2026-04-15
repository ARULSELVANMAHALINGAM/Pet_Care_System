<?php
// PHP backend logic remains unchanged.
include('../config/db.php');
include('../config/activity_logger.php');

// Initialize variables for messages
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation and sanitization
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        $error = "All fields are required!";
    } else {
        // Assume $conn is connected successfully
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            $sql = "INSERT INTO users (username, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
            if (mysqli_query($conn, $sql)) {
                // Get the newly created user ID
                $new_user_id = mysqli_insert_id($conn);
                
                // Log registration activity
                logActivity($conn, $new_user_id, 'User registered', "New $role account created: $email", $role);
                
                $success = "Registration successful! You can now login.";
                // Optional redirect
                // header('Location: login.php');
                // exit();
            } else {
                $error = "Error during registration: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PetCare System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

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

        .register-container {
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

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .left-panel img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .right-panel {
            flex: 1;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            text-align: left;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #1e293b, #475569);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .error-message, .success-message {
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid;
            font-weight: 500;
            font-size: 0.9375rem;
        }
        .error-message {
            color: #dc2626;
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.2);
        }
        .success-message {
            color: #10b981;
            background: rgba(16,185,129,0.1);
            border-color: rgba(16,185,129,0.2);
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

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.15);
            outline: none;
        }

        /* Removed terms-checkbox styling as the element was removed from HTML */

        .create-account-btn {
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
            margin-top: 20px;
        }

        .create-account-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .create-account-btn:active {
            transform: translateY(0);
        }

        .bottom-links {
            text-align: center;
            margin-top: 16px;
            color: #64748b;
            font-size: 0.875rem;
        }
        .bottom-links a {
            color: #a78bfa;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .bottom-links a:hover {
            color: #8b5cf6;
        }
        
        .error-message, .success-message {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9375rem;
        }
        .error-message {
            color: #dc2626;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
        }
        .success-message {
            color: #10b981;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
        }

        /* Role Selection Buttons - Matching Login Page */
        .role-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .role-btn {
            flex: 1;
            min-width: 90px;
            padding: 16px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
        }
        .role-btn i {
            font-size: 2rem;
            color: #94a3b8;
        }
        .role-btn span {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #475569;
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
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }
        .role-btn.selected i {
            color: white;
        }
        .role-btn.selected span {
            color: white;
            font-weight: 600;
        }

        @media (max-width: 800px) {
            .register-container {
                flex-direction: column;
            }
            .left-panel {
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
    <div class="register-container">
        <div class="left-panel">
            <img src="../assets/images/register2.png" alt="PetCare Registration">
        </div>

        <div class="right-panel">
            <h2>Create Account</h2>
            
            <?php
            if (!empty($error)) echo "<p class='error-message'>$error</p>";
            if (!empty($success)) echo "<p class='success-message'>$success</p>";
            ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Role</label>
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
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>

                <button type="submit" class="create-account-btn">
                    Create Account
                </button>
            </form>

            <div class="bottom-links">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
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