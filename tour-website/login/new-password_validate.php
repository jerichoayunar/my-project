<?php
session_start();

// Adjust this path based on your folder structure
require '../../includes/db.php';

// ✅ Check session for security
if (!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) || $_SESSION['reset_code_verified'] !== true) {
    header('Location: ../login.php'); // Use relative path
    exit();
}

// ✅ Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpassword = trim($_POST['newPassword']);
    $confirmPassword = trim($_POST['confirm_newPassword']);

    // ✅ Password strength validation
if (
    strlen($newpassword) < 6 ||
    !preg_match("/[0-9]/", $newpassword)
) {
    $_SESSION['password_error'] = "Password must be at least 6 characters long and contain at least one number.";
    header('Location: new-password.php'); // Go back to new-password page
    exit();
}

    // ✅ Match confirmation
    if ($newpassword !== $confirmPassword) {
        $_SESSION['password_error'] = "Passwords don't match. Please try again.";
        header('Location: new-password.php');
        exit();
    }

    // ✅ Hash and update
    $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE clients SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $_SESSION['email']]);

    // ✅ Clear session
    unset($_SESSION['email']);
    unset($_SESSION['reset_code_verified']);

    // ✅ Redirect with success
    $_SESSION['success'] = 'Your password has been reset successfully.';
    header('Location: login.php');
    exit();
}
?>
