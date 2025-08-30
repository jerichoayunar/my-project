<?php
session_start();

// Delay for 2-3 seconds
sleep(3);

// Destroy session
session_unset();
session_destroy();

// Redirect after logout
header('Location: index.php'); // Adjust path if needed
exit();
?>
