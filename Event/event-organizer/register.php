<?php
require_once('../api/shared.php');

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Register the user
    $response = registerUser($name, $email, $username, $password, $confirm_password);
    
    // If registration is successful, redirect to login page
    if ($response['success']) {
        header("Location: login.php");
        exit();
    } else {
        // Show error message
        $error_message = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    /* Right side with the registration form */
    .right-side {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Styling for the registration form container */
    .registration-container {
        max-width: 450px;
        width: 100%;
        background-color: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .registration-container h2 {
        font-size: 32px;
        margin-bottom: 20px;
        font-weight: 600;
        color: #333;
    }

    .registration-container p {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
    }

    .registration-container .form-label {
        font-weight: 600;
        color: #555;
    }

    .registration-container input {
        font-size: 16px;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        transition: border-color 0.3s ease;
    }

    .registration-container input:focus {
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
  </style>
</head>
<body>
  <div class="container-fluid row full-height">
    <!-- Left side with logo and gradient background -->
    <div class="col-md-6 left-side">
      <!-- Logo Image -->
      <img src="../logo.png" alt="Logo" class="logo-img">
    </div>

    <!-- Right side with registration form -->
    <div class="col-md-6 right-side">
      <div class="registration-container">
        <!-- Registration Message -->
        <h2>Create an Account</h2>
        <p class="text-center">Please fill in the details to create your account.</p>

        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
          <!-- Full Name Field -->
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required />
          </div>

          <!-- Email Field -->
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required />
          </div>

          <!-- Username Field -->
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required />
          </div>

          <!-- Password Field -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8" />
          </div>

          <!-- Confirm Password Field -->
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8" />
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="mt-3 text-center">
          <a href="login.php">Already have an account? Login here</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
