<?php
// Include the configuration file for APP_NAME
include('./config/constants.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        /* Modern White Theme Landing Page */
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f1f5f9 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Animated background elements */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.03) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            z-index: 0;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Container with modern white design */
        .page-container {
            background: #ffffff;
            border-radius: 24px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 60px 50px; 
            width: 90%;
            max-width: 1000px;
            display: flex;
            flex-direction: row; 
            min-height: 500px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: fadeInUp 0.8s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .page-container:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .text-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding-right: 60px;
        }

        .text-section h1 {
            font-size: 2.75rem; 
            background: linear-gradient(135deg, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
            animation: slideInLeft 0.8s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .text-section p {
            font-size: 1.125rem;
            color: #64748b;
            margin-bottom: 40px;
            max-width: 90%;
            line-height: 1.7;
            animation: slideInLeft 1s ease-out;
        }

        /* --- Interactive Button Styling --- */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 16px;
            animation: slideInLeft 1.2s ease-out;
        }

        .action-buttons a {
            text-decoration: none;
            width: 280px;
            display: block;
        }

        .action-buttons button {
            width: 100%;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.0625rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        /* Login Button - Primary Gradient */
        .action-buttons a:first-child button {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        
        .action-buttons a:first-child button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .action-buttons a:first-child button:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .action-buttons a:first-child button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
        }
        
        .action-buttons a:first-child button:active {
            transform: translateY(-1px);
        }

        /* Register Button - Outline Style */
        .action-buttons a:last-child button {
            background: transparent;
            color: #6366f1;
            border: 2px solid #6366f1;
            box-shadow: none;
        }
        
        .action-buttons a:last-child button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            transition: left 0.3s ease;
            z-index: -1;
        }
        
        .action-buttons a:last-child button:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .action-buttons a:last-child button:hover::before {
            left: 0;
        }
        
        .action-buttons button i {
            font-size: 1.2rem;
        }


        /* Image/Visual section with modern design */
        .visual-section {
            flex: 0 0 400px;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            margin-left: 30px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            overflow: hidden;
            animation: slideInRight 0.8s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .visual-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .visual-section img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        
        .visual-section:hover img {
            transform: scale(1.05);
        }
        
        /* Responsive Design */
        @media (max-width: 968px) {
            .page-container {
                flex-direction: column;
                padding: 40px 30px;
            }
            
            .visual-section {
                flex: 1;
                width: 100%;
                margin-left: 0;
                margin-top: 30px;
                min-height: 300px;
            }
            
            .text-section {
                padding-right: 0;
            }
            
            .text-section h1 {
                font-size: 2rem;
            }
            
            .action-buttons {
                width: 100%;
            }
            
            .action-buttons a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="text-section">
            <h1>Manage Your Pet's Health Effortlessly.</h1>
            <p>Welcome to **<?php echo APP_NAME; ?>**, the official digital platform for managing pet health records, vaccinations, clinic visits, and timely reminders securely.</p>
            
            <div class="action-buttons">
                <a href="./auth/login.php">
                    <button>
                        <i class="ri-login-box-line"></i>
                        <span>Login to Your Account</span>
                    </button>
                </a>
                <a href="./auth/register.php">
                    <button>
                        <i class="ri-user-add-line"></i>
                        <span>Create a New Account</span>
                    </button>
                </a>
            </div>
        </div>

        <div class="visual-section">
            <img src="./assets/images/logo.png" alt="PetCare Management System Logo">
        </div>
    </div>
</body>
</html>