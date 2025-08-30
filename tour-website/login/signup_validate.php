<?php
session_start();

// Include the database connection file (PDO)
include('../../includes/db.php');

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header('Location: signup.php');
        exit();
    }

    // Hash the password for secure storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the database (using PDO)
    try {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email is already registered!";
            header('Location: signup.php');
            exit();
        }

        // Insert new user into the database (using PDO)
        $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashed_password]);

        $_SESSION['success'] = "Account created successfully! Please log in.";
        header('Location: login.php');

    } catch (PDOException $e) {
        // Log the PDO error for debugging
        error_log("PDO Error: " . $e->getMessage());  // Logs to PHP error log
        $_SESSION['error'] = "There was an error creating your account. Please try again.";
        header('Location: signup.php');
    }
    exit();
}
