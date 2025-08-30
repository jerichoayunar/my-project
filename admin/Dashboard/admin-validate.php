<?php
session_start();    // Start session to manage login state and flash messages
include('../../includes/db.php'); // Ensure this path is correct for your setup

// Check if the request method is POST (form submission)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize input values
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login-admin.php");
        exit();
    }

    // Prepare a SQL statement to retrieve admin user by username
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    if (!$stmt) {
        // Handle SQL preparation error
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: login-admin.php");
        exit();
    }

    // Bind the username parameter to the SQL statement and execute
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if exactly one user was found
    if ($stmt->num_rows === 1) {
        // Bind result to variables
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify entered password against the hashed password from the database
        if (password_verify($password, $hashed_password)) {
            // Password is correct; set session variables
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_username'] = $username;
            $_SESSION['success'] = "Welcome, $username!";
            header("Location: dashboard.php");  // Redirect to admin dashboard
            exit();
        } else {
            // Password does not match
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        // No user found with that username
        $_SESSION['error'] = "Admin user not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to login page with error
    header("Location: login-admin.php");
    exit();
} else {
    // If someone tries to access it directly
    $_SESSION['error'] = "Invalid request.";
    header("Location: login-admin.php");
    exit();
}
