<?php
// Start the session to access session variables
session_start();

// Check if the client_id is NOT set in the session (meaning user is not logged in)
if (!isset($_SESSION['client_id'])) {
    // Set an error message in session to notify the user they must log in
    $_SESSION['error'] = "Please log in to continue.";
    
    // Redirect the user to the login page
    header("Location: login/login.php");
    exit(); // Stop further script execution after redirect
}
?>
