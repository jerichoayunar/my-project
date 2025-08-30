<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Signup</title>
        <link rel="stylesheet" href="assets/style.css">
        <link rel="stylesheet" href="/bootstrap-5.3.3-dist/bootstrap.min.css">
        <meta charset="utf-8">

    <style>
   body{
    background: url('bukid.jpg') no-repeat center center fixed;
    background-size: cover;
  }
</style>


    </head>
    <body>
        <div class="container">
            <div class="card">
                <img src="../../image/in.jpg" alt="This is the Logo">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="signup_validate.php" method="POST">
                    <input class="form-control" type="text" placeholder="Full Name" name="name" required>
                    <input class="form-control" type="email" placeholder="Email" name="email" required>
                    <input class="form-control" type="text" placeholder="Phone Number" name="phone" required>
                    <input class="form-control" type="password" placeholder="Password" name="password" required>
                    <input class="form-control" type="password" placeholder="Re-enter Password" name="confirm_password" required>
                    
                    <button type="submit">Signup</button>
                </form>

                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </body>
</html>
