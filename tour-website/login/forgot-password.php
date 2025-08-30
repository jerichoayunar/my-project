<?php 
session_start();
require '../../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_code = rand(100000, 999999);
        $update = $pdo->prepare("UPDATE clients SET reset_code = ? WHERE email = ?");
        $update->execute([$reset_code, $email]);
        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '2301105785@student.buksu.edu.ph'; // Your Gmail
            $mail->Password = 'auyq iajw hqee oyfu ';    // App Password (must be 16-digit from Google)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('2301105785@student.buksu.edu.ph', 'Ayunar, Jericho V.');
            $mail->addAddress($email, 'User');

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is: <strong>$reset_code</strong>";
            $mail->AltBody = "Your password reset code is: $reset_code";

            $mail->send();

            $_SESSION['success'] = "Code sent! Please check your email.";
            header('Location: send-code.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="card mt-5 p-4">
            <img src="../../image/in.jpg" alt="Company Logo" class="mb-3" style="max-width: 200px;">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Code</button>
            </form>
        </div>
    </div>
</body>
</html>
