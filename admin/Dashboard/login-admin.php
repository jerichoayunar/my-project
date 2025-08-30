<?php
session_start(); // Start the session to manage login state

// If user is already logged in, redirect to admin-dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-dashboard.php");
    exit();
}
?>

<style>
  body{
    background: url('bukid.jpg') no-repeat center center fixed;
    background-size: cover;
  }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  <!-- Set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">     <!-- Apply Bootstrap light background -->
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">        <!-- Login card with a small shadow -->
                    <div class="card-body">
                        <img src="../../image/bukidnonupdates.jpg" alt="This is the logo" class="mx-auto d-block mb-3" style="max-width:120px; border-radius: 50%;">
                        <!-- Login Title -->
                        <h3 class="text-center mb-4">Admin Login</h3>

                        <!-- Display error message if login fails -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Display success message if login succeeded previously -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Login form -->
                        <form action="admin-validate.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
