<?php
session_start();
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    $code = trim($_GET['code']); // Keep original value
    $email = $_SESSION['email'] ?? '';

    if ($email) {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ? AND reset_code = ? LIMIT 1");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Set verified session variable
            $_SESSION['reset_code_verified'] = true;
            // Code is correct, redirect to new-password page
            header("Location: new-password.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid code. Please try again.";
            header("Location: send-code.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Session expired. Please start over.";
        header("Location: forgot-password.php");
        exit();
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Code</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <img src="../../image/in.jpg" alt="This is the logo">


    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="color: green; margin-top: 10px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

            <form action="send-code.php" method="GET">
                 <input type="text" placeholder="Enter code" name="code" required>
                 <button type="submit">Submit</button>
            </form>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="color: red; margin-top: 10px;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>


        </div>
    </div>
</body>
</html>