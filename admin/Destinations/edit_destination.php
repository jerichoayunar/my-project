<?php
include '../../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: destinations.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch destination
$stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$destination = $result->fetch_assoc();
if (!$destination) {
    die("Destination not found.");
}

// Fetch packages
$packageResult = $conn->query("SELECT id, title FROM packages ORDER BY title ASC");
$packages = [];
while ($row = $packageResult->fetch_assoc()) {
    $packages[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $location_map = $_POST['location_map'];
    $package_id = $_POST['package_id'] !== '' ? intval($_POST['package_id']) : null;

    if ($_FILES['image']['name']) {
        $image = basename($_FILES['image']['name']);
        $target = "../uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $stmt = $conn->prepare("UPDATE destinations SET name=?, description=?, price=?, location_map=?, image_url=?, package_id=? WHERE id=?");
        $stmt->bind_param("ssdssii", $name, $description, $price, $location_map, $image, $package_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE destinations SET name=?, description=?, price=?, location_map=?, package_id=? WHERE id=?");
        $stmt->bind_param("ssdsii", $name, $description, $price, $location_map, $package_id, $id);
    }

    $stmt->execute();
    header("Location: destinations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Destination</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Edit Destination</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($destination['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($destination['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Price (₱)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $destination['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Embed Map (iframe)</label>
            <textarea name="location_map" class="form-control" required><?= htmlspecialchars($destination['location_map']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Current Image</label><br>
            <img src="../uploads/<?= htmlspecialchars($destination['image_url']) ?>" alt="Image" width="100">
        </div>
        <div class="mb-3">
            <label>New Image (optional)</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <div class="mb-3">
            <label>Package</label>
            <select name="package_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($packages as $pkg): ?>
                    <option value="<?= $pkg['id'] ?>" <?= $destination['package_id'] == $pkg['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pkg['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Destination</button>
        <a href="destinations.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
