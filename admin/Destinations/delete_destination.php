<?php
include '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Check if package is linked in destinations
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM destinations WHERE package_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Package is linked, don't delete, show message and stop
        echo "Cannot delete package because it is linked to one or more destinations.";
        echo "<br><a href='packages.php'>Go back</a>";
        exit();
    }

    // Get the image filename to delete it after deleting package
    $stmt = $conn->prepare("SELECT image FROM packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();

    if ($package) {
        // Delete the package
        $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Delete the image file from the server (if exists)
        $image_path = "../uploads/" . $package['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header("Location: packages.php");
    exit();
}
?>
