<?php
include '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure client is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

$client_id = $_SESSION['client_id'];

// Check if booking_ids are sent via POST
if (isset($_POST['booking_ids'])) {
    // Sanitize and explode the booking IDs into an array
    $booking_ids = array_filter(array_map('trim', explode(',', $_POST['booking_ids'])));

    if (count($booking_ids) > 0) {
        // Prepare placeholders for SQL IN clause
        $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));

        // Prepare statement to verify bookings belong to this client
        $sql_check = "SELECT COUNT(*) as count FROM bookings WHERE id IN ($placeholders) AND client_id = ?";
        $stmt_check = $conn->prepare($sql_check);

        // Bind booking IDs and client_id dynamically
        $types = str_repeat('i', count($booking_ids)) . 'i';
        $params = array_merge($booking_ids, [$client_id]);

        // PHP 7+ way to bind params dynamically
        $stmt_check->bind_param($types, ...$params);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['count'] == count($booking_ids)) {
            // All bookings belong to client, proceed to update status
            $sql_update = "UPDATE bookings SET status = 'Cancelled' WHERE id IN ($placeholders) AND client_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param($types, ...$params);
            $stmt_update->execute();

            // Redirect back with success message
            header('Location: booking_history.php?msg=Booking(s) cancelled successfully.');
            exit;
        } else {
            // Some booking does not belong to this client - error
            header('Location: booking_history.php?msg=Error: Invalid booking ID(s).');
            exit;
        }
    } else {
        // No valid booking IDs provided
        header('Location: booking_history.php?msg=Error: No booking IDs provided.');
        exit;
    }
} else {
    // No booking_ids POSTed
    header('Location: booking_history.php?msg=Error: Invalid request.');
    exit;
}
