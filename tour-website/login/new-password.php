<?php
session_start();

// Validate session and reset status
if (!isset($_SESSION['email']) || empty($_SESSION['reset_code_verified']) || $_SESSION['reset_code_verified'] !== true) {
    header('Location: send-code.php');
    exit();
}

// Get error message if exists
$error = isset($_SESSION['password_error']) ? $_SESSION['password_error'] : null;
if ($error) {
    unset($_SESSION['password_error']); // Clear the error after displaying
}

// Optional: You can also set a success message if needed
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : null;
if ($successMessage) {
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Adjust path if needed -->
    <style>
        .error-message {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px 15px;
            margin: 0 0 20px 0;
            border-radius: 4px;
            border: 1px solid #ef9a9a;
            text-align: center;
        }
        .success-message {
            color: #388e3c;
            background-color: #c8e6c9;
            padding: 10px 15px;
            margin: 0 0 20px 0;
            border-radius: 4px;
            border: 1px solid #81c784;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="../../image/in.jpg" alt="This is the Logo" style="max-width: 200px; margin-bottom: 20px;">

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <form action="new-password_validate.php" method="POST">
                <input type="password" placeholder="Enter New Password" name="newPassword" required autocomplete="new-password">
                <input type="password" placeholder="Re-enter New Password" name="confirm_newPassword" required autocomplete="new-password">
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
