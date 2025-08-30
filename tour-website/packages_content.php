<?php
// Include the database connection
include '../includes/db.php'; // Adjust path as needed

// Query to fetch destination name and its associated package info (if any)
$sql = "SELECT 
            d.name AS destination_name, 
            p.id AS package_id, 
            p.title AS package_title,
            p.image AS package_image
        FROM destinations d
        LEFT JOIN packages p ON d.package_id = p.id
        ORDER BY p.title, d.name";

// Execute the query
$result = $conn->query($sql);

// Array to group destinations under their respective packages
$groupedDestinations = [];

while ($row = $result->fetch_assoc()) {
    // Use 'None' for destinations without a package
    $packageTitle = $row['package_title'] ?? 'None';
    $packageImage = $row['package_image'] ?? '';
    $destinationName = $row['destination_name'];

    // Initialize package group if not already set
    if (!isset($groupedDestinations[$packageTitle])) {
        $groupedDestinations[$packageTitle] = [
            'image' => $packageImage,
            'destinations' => []
        ];
    }

    // Add destination to the appropriate package group
    $groupedDestinations[$packageTitle]['destinations'][] = $destinationName;
}

// Move the 'None' group (unassigned destinations) to the bottom
if (isset($groupedDestinations['None'])) {
    $noneGroup = ['None' => $groupedDestinations['None']];
    unset($groupedDestinations['None']);
    $groupedDestinations = array_merge($groupedDestinations, $noneGroup);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Packages</title>
  <style>
    /* General body styling */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      color: #333;
    }

    .popular-destinations {
      padding: 20px;
    }

    .popular-destinations h2 {
      font-size: 28px;
      margin-bottom: 30px;
    }

    /* Each package section */
    .place {
      margin-bottom: 40px;
    }

    /* Banner at the top of each package */
    .place-banner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-radius: 15px;
      color: white;
      background-size: cover;
      background-position: center;
      position: relative;
      margin-bottom: 15px;
    }

    .place-banner h3 {
      font-size: 24px;
      font-weight: bold;
      z-index: 1;
    }

    /* Container for action buttons */
    .button-container {
      display: flex;
      gap: 10px;
      z-index: 1;
    }

    .button-container button {
      padding: 8px 12px;
      border-radius: 25px;
      border: none;
      background-color: rgba(255, 255, 255, 0.3);
      color: white;
      transition: background 0.3s ease-in-out;
      cursor: pointer;
    }

    .button-container button:hover {
      background-color: rgba(255, 255, 255, 0.5);
    }

    /* List of destinations */
    .place-list {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      padding-left: 15px;
    }

    .place-list p {
      margin: 0;
      width: 180px;
      font-weight: bold;
    }

    /* Styling for 'None' package group */
    .none-banner {
      background: linear-gradient(135deg, #999, #bbb);
      color: #fff !important;
    }

    .none-banner h3 {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
    }

    /* Hide action buttons for unassigned destinations */
    .none-banner .button-container {
      display: none;
    }
  </style>
</head>
<body>
  <div class="popular-destinations">
    <h2>Our Tour Packages</h2>

    <!-- Loop through each grouped package and display its destinations -->
    <?php foreach ($groupedDestinations as $packageTitle => $data): ?>
      <div class="place">
        <?php
          // Check if this is the 'None' group
          $isNone = ($packageTitle === 'None');
          $bannerClass = $isNone ? 'place-banner none-banner' : 'place-banner';

          // Build image path for background
          $imagePath = $data['image'] ? "../admin/uploads/" . htmlspecialchars($data['image']) : '';

          // Apply background image style if applicable
          $backgroundStyle = $imagePath && !$isNone ? "background-image: url('$imagePath');" : '';
        ?>
        <!-- Banner section for the package -->
        <div class="<?= $bannerClass ?>" style="<?= $backgroundStyle ?>">
          <h3><?= $isNone ? 'Unassigned Destinations' : htmlspecialchars($packageTitle) ?></h3>

          <!-- Buttons only shown if this is not the 'None' group -->
          <?php if (!$isNone): ?>
            <div class="button-container">
              <a href="destinations.php"><button>Destinations</button></a>
              <a href="packages.php"><button>Packages</button></a>
            </div>
          <?php endif; ?>
        </div>

        <!-- Destination list under this package -->
        <div class="place-list">
          <?php foreach ($data['destinations'] as $destination): ?>
            <p><?= htmlspecialchars($destination) ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
