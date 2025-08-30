<?php
include '../../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$client = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();
    $stmt->close();
}

if (!$client) {
    die("⚠️ Client not found or invalid ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update = $conn->prepare("UPDATE clients SET name = ?, email = ?, phone = ? WHERE id = ?");
    $update->bind_param("sssi", $name, $email, $phone, $id);
    $update->execute();
    $update->close();

    header("Location: clients.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Client</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Client</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($client['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($client['phone']) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="clients.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
