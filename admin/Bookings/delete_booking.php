<?php
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['booking_ids'])) {
    $booking_ids_raw = $_POST['booking_ids'];
    // Sanitize input: split and convert to int
    $booking_ids_arr = array_filter(array_map('intval', explode(',', $booking_ids_raw)));

    if (empty($booking_ids_arr)) {
        echo "No valid booking IDs provided.";
        exit;
    }

    // Prepare placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($booking_ids_arr), '?'));

    // Prepare the statement
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id IN ($placeholders)");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    // Bind parameters dynamically
    $types = str_repeat('i', count($booking_ids_arr));
    $stmt->bind_param($types, ...$booking_ids_arr);

    if ($stmt->execute()) {
        header("Location: bookings.php?msg=Booking(s) deleted successfully");
        exit;
    } else {
        echo "Error deleting bookings: " . $conn->error;
    }

    $stmt->close();

} else {
    echo "Invalid request.";
}
?>
