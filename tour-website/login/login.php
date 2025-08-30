<?php
session_start();

require_once __DIR__ .'/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'];
$secretKey = $_ENV['RECAPTCHA_SECRET_KEY'];

?>

<style>
   body{
    background: url('bukid.jpg') no-repeat center center fixed;
    background-size: cover;
  }
</style>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <img src="../../image/bukidnonupdates.jpg" alt="This is the logo">
            
            <!-- Display error message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Display success message -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
   
            <!-- Login Form -->
            <form action="login_validate.php" method="POST">
                <input required class="form-control" type="email" placeholder="Email" name="email">
                <input required class="form-control" type="password" placeholder="Password" name="password">
                
                
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>

            

                
                <button class="btn btn-primary" type="submit">Login</button>
            </form>

                <div class="text-center mt-3">
                    <a href="googleAuth/google-login.php" class="btn btn-outline-danger w-100">
                        <i class="mdi mdi-google me-2"></i>Sign up with Google
                    </a>
                </div>


            <p>Don't have an account? <a href="signup.php">Sign-up</a></p>
            <p>Forgot password? <a href="forgot-password.php">Forgot-Password</a></p>
        </div>
    </div>


<script src="https://www.google.com/recaptcha/api.js"> </script>


</body>

</html>
