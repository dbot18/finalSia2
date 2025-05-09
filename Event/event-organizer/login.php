<?php
// Start session to manage user login
session_start();

// Include shared database connection file
require_once('../api/shared.php');

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Query to check the user credentials
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Full height for the body */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Container for the split layout */
        .row.full-height {
            height: 100%;
        }

        /* Left section with logo and gradient background */
        .left-side {
            background: linear-gradient(to right, #6a00ff, #00aaff);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }

        /* Add logo image */
        .logo-img {
            max-width: 100%;
            height: auto;
            margin-bottom: 30px;
        }

        /* Right side with the login form */
        .right-side {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Styling for the login form container */
        .login-container {
            max-width: 450px;
            width: 100%;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .login-container h2 {
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        .login-container p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .login-container .form-label {
            font-weight: 600;
            color: #555;
        }

        .login-container input {
            font-size: 16px;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }

        .login-container input:focus {
            border-color: #6a00ff;
            outline: none;
        }

        .alert-danger {
            color: white;
            background-color: #f44336;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-primary {
            background-color: #6a00ff;
            border-color: #6a00ff;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4e00b3;
        }

        .mt-3 a {
            font-size: 14px;
            color: #6a00ff;
            text-decoration: none;
        }

        .mt-3 a:hover {
            text-decoration: underline;
        }

        /* Password visibility toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
            color: #6a00ff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid row full-height">
        <!-- Left side with logo and gradient background -->
        <div class="col-md-6 left-side">
            <!-- Logo Image -->
            <img src="../logo.png" alt="Logo" class="logo-img">

        </div>

        <!-- Right side with login form -->
        <div class="col-md-6 right-side">
            <div class="login-container">
                <!-- Login Message -->
                <h2>Login</h2>
                <p class="text-center">Welcome back! Please enter your credentials to log in.</p>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span id="toggle-password" class="password-toggle">Show</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="register.php">Don't have an account? Register here</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle functionality
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePassword.textContent = 'Hide'; // Change to "Hide" text
            } else {
                passwordInput.type = 'password';
                togglePassword.textContent = 'Show'; // Change to "Show" text
            }
        });
    </script>
</body>
</html>
