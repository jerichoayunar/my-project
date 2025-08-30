<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/db.php';

// rest of your code...


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $valid_statuses = ['Pending', 'Confirmed', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        echo "Invalid status value.";
        exit;
    }

    if (!empty($_POST['booking_ids']) && is_array($_POST['booking_ids'])) {
        $booking_ids = array_filter(array_map('intval', $_POST['booking_ids']));
        if (empty($booking_ids)) {
            echo "No valid booking IDs provided.";
            exit;
        }
    } else {
        echo "No booking IDs provided.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    foreach ($booking_ids as $id) {
        $stmt->bind_param("si", $status, $id);
        if (!$stmt->execute()) {
            echo "Failed to update booking ID: $id";
            exit;
        }
    }
    $stmt->close();

    header("Location: bookings.php?msg=Booking(s) updated successfully");
    exit;
} else {
    echo "Invalid request method.";
    exit;
}
