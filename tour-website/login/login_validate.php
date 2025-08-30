<?php
session_start();

// Include the PDO database connection
include('../../includes/db.php'); // Make sure this path is correct for your project structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'];
    $recaptcharesponse = $_POST['g-recaptcha-response'];

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecSecret}&response={$recaptchaResponse}");
    $captchaSuccess = json_decode($verify);

 
  // sam ni

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];



    try {
        // Fetch user with matching email using PDO
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['client_id'] = $user['id']; // Store client id in session
                $_SESSION['name'] = $user['name']; // Store client name in session
                $_SESSION['success'] = "Login successful!";
                header('Location: ../index.php'); // Redirect to homepage or dashboard
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header('Location: login.php'); // Redirect back to login page
                exit();
            }
        } else {
            $_SESSION['error'] = "No account found with that email!";
            header('Location: login.php'); // Redirect back to login page
            exit();
        }
    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again.";
        header('Location: login.php'); // Redirect back to login page
        exit();
    }
}
