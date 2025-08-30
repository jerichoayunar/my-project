<?php
// Include authentication check to ensure user is logged in
include 'login/auth_check.php';

// Include the database connection file
include '../includes/db.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and trim whitespace from user inputs for name, email, and message
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Validate that none of the fields are empty
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Prepare a SQL INSERT statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        
        // Bind the user inputs as strings to the prepared statement
        $stmt->bind_param("sss", $name, $email, $message);

        // Execute the statement and check if it was successful
        if ($stmt->execute()) {
            // If successful, alert user and redirect to index page
            echo "<script>alert('Your inquiry is sent successfully!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            // If there was an error executing the statement, output the error
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // If any field is empty, notify the user
        echo "All fields are required.";
    }
} else {
    // If accessed directly without POST, redirect to index page
    header("Location: index.php");
    exit();
}
?>
