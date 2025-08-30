<?php
include '../../includes/db.php';

// Fetch packages for the dropdown
$packageResult = $conn->query("SELECT id, title FROM packages ORDER BY title ASC");
$packages = [];
while ($row = $packageResult->fetch_assoc()) {
    $packages[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location_map = $_POST['location_map'];
    $package_id = $_POST['package_id'] !== '' ? intval($_POST['package_id']) : null;

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO destinations (name, description, price, location_map, image_url, package_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssi", $name, $description, $price, $location_map, $image, $package_id);
    $stmt->execute();

    header("Location: destinations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Destination</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Add New Destination</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Price (₱)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Embed Map (iframe)</label>
            <textarea name="location_map" class="form-control" required></textarea>
            <small>Paste the entire Google Maps iframe code here</small>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" accept="image/*" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Package</label>
            <select name="package_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($packages as $pkg): ?>
                    <option value="<?= $pkg['id'] ?>"><?= htmlspecialchars($pkg['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Add Destination</button>
        <a href="destinations.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
