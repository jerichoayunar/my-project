<?php
include '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: inquiries.php?msg=Inquiry deleted successfully");
    } else {
        echo "Error deleting inquiry: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
