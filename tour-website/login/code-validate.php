<?php
session_start();
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = trim($_POST['code']);
    $email = $_SESSION['email'] ?? null;

    if (!$email) {
        $_SESSION['error'] = "Session expired. Try again.";
        header('Location: forgot-password.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT reset_code FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (strcasecmp(trim($user['reset_code']), $enteredCode) === 0) {
            $_SESSION['reset_code_verified'] = true;  // Use this consistently
            header('Location: new-password.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid code. Try again.";
            header('Location: send-code.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email.";
        header('Location: forgot-password.php');
        exit();
    }
}
?>
