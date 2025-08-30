<?php
// Start the session to access session variables
session_start();

// Include the database connection file
include '../includes/db.php';

// Check if the form was submitted via POST and the client is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['client_id'])) {
    // Get the logged-in client's ID as an integer for safety
    $client_id = intval($_SESSION['client_id']);
    
    // Get and sanitize tour date from the POST data
    $tour_date = $conn->real_escape_string($_POST['tour_date']);
    
    // Get and sanitize tour time from the POST data (new field)
    $tour_time = $conn->real_escape_string($_POST['tour_time']);
    
    // Combine the tour date and time into one datetime string for booking_date
    $booking_date = $tour_date . ' ' . $tour_time;
    
    // Set default booking status to 'Pending'
    $status = 'Pending';

    // Check if package_ids is set and is an array (one or more selected packages)
    if (!empty($_POST['package_ids']) && is_array($_POST['package_ids'])) {
        // Prepare an SQL statement to insert bookings into the database
        $stmt = $conn->prepare("INSERT INTO bookings (client_id, package_id, booking_date, status) VALUES (?, ?, ?, ?)");
        
        // Flag to track whether all inserts succeed
        $insertSuccess = true;

        // Loop through each selected package ID
        foreach ($_POST['package_ids'] as $package_id) {
            // Convert package_id to integer to avoid SQL injection
            $pid = intval($package_id);
            
            // Bind parameters: client ID, package ID, booking date-time, status
            $stmt->bind_param("iiss", $client_id, $pid, $booking_date, $status);

            // Execute the prepared statement; if any insert fails, set flag false and break loop
            if (!$stmt->execute()) {
                $insertSuccess = false;
                break;
            }
        }

        // If all inserts were successful, alert success and redirect to homepage
        if ($insertSuccess) {
            echo "<script>alert('Booking submitted successfully!'); window.location.href = 'index.php';</script>";
        } else {
            // If any insert failed, alert error and go back
            echo "<script>alert('Error saving booking(s).'); history.back();</script>";
        }

    } else {
        // If no packages selected, alert user and go back to form
        echo "<script>alert('Please select at least one package.'); history.back();</script>";
    }

} else {
    // If not logged in or not POST request, alert user and redirect to login page
    echo "<script>alert('You must be logged in to book.'); window.location.href = 'login.php';</script>";
}
?>
