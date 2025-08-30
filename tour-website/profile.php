<?php
session_start();
include '../includes/db.php';  // Adjust path if needed

// Redirect to login if client not logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

$client_id = $_SESSION['client_id'];
$errors = [];
$success = '';

// Fetch current client info
$sql = "SELECT name, email, phone FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$client = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Basic validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }

    // If no errors, update the client info
    if (empty($errors)) {
        $update_sql = "UPDATE clients SET name = ?, email = ?, phone = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $client_id);
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh client data
            $client['name'] = $name;
            $client['email'] = $email;
            $client['phone'] = $phone;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f0f4f8);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-3" style="max-width: 600px; margin-bottom: 240px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
    <h2 class="mb-4">Edit Profile</h2>

    <!-- Error messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Success message -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Profile form -->
    <form method="POST" action="profile.php" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($client['name']) ?>" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($client['email']) ?>" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($client['phone']) ?>" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Footer -->
<footer>
  <div class="footer-content">
    <div class="footer-section">
      <h3>Tour & Travel</h3>
      <p>Your trusted partner in travel.</p>
    </div>
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="packages.php">Packages</a></li>
        <li><a href="destinations.php">Destinations</a></li>
        <li><a href="inquiry.php">Send Inquiry</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Follow Us</h3>
      <div class="social-links">
        <a href="https://www.facebook.com/bukidnonupdates"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.tiktok.com/@bukidnonupdates?_t=ZS-8vdRO0cUXVH&_r=1"><i class="fab fa-tiktok"></i></a>
        <a href="https://www.instagram.com/bukidnonupdatess?igsh=MWUwZGw0MXEyczhoYg=="><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
  <p>&copy; 2025 Tour & Travel. All rights reserved.</p>
</footer>
</body>
</html>
