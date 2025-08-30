<?php
include '../../includes/db.php';

// Handle search and filter
$search = $_GET['search'] ?? '';
$package = $_GET['package'] ?? '';

// Build SQL query
$sql = "SELECT destinations.*, packages.title AS package_title 
        FROM destinations 
        LEFT JOIN packages ON destinations.package_id = packages.id 
        WHERE 1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND destinations.name LIKE '%$search%'";
}

if (!empty($package)) {
    $package = (int)$package;
    $sql .= " AND destinations.package_id = $package";
}

$sql .= " ORDER BY destinations.name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Destinations</title>
<link rel="stylesheet" href="../styles.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    .dashboard {
        display: flex;
        min-height: 100vh;
    }
    .main-content {
    margin-left: 250px;      /* Same as sidebar width */
    padding: 20px;
    }

    table {
        table-layout: fixed;
        width: 100%;
        word-break: break-word;
    }
    .table img {
        width: 100px;
        border-radius: 5px;
        object-fit: cover;
    }
    .btn {
        margin-right: 5px;
        font-size: 0.9rem;
    }
    .map-preview-container iframe {
        width: 250px !important;
        height: 200px !important;
        border-radius: 5px;
        border: 1px solid #ccc;
        display: block;
    }
    td.description-cell {
        max-width: 300px;
        vertical-align: top;
        padding: 8px;
        /* Important: fix height to avoid resize on toggle */
        height: auto;
    }
    .desc-wrapper {
        display: block;
        max-height: 4.8em; /* ~3 lines */
        overflow: hidden;
        word-break: break-word;
        white-space: normal;
        font-size: 0.9rem;
        color: #444;
        transition: max-height 0.3s ease;
    }
    td.description-cell.expanded .desc-wrapper {
        max-height: none;
        overflow: visible;
    }
    .toggle-description {
        color: #007bff;
        cursor: pointer;
        font-size: 0.9rem;
        user-select: none;
        margin-top: 4px;
        display: inline-block;
    }

    /* Search & Filter Form */
    .filter-form .form-label {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .filter-form .form-control,
    .filter-form .form-select {
        font-size: 0.9rem;
    }

    .filter-form .btn {
        font-size: 0.9rem;
    }
</style>
</head>

<body>
<div class="dashboard">
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>

    <div class="main-content">

    <!-- top bar -->
        <?php include '../topbar.php'; ?>

        <div class="container mt-4">
            <h2 class="mb-3">Manage Destinations</h2>
            <a href="add_destination.php" class="btn btn-primary mb-3">➕ Add New Destination</a>

            <!-- Search and Filter Form -->
            <form method="GET" class="row g-3 mb-3 align-items-end filter-form">
                <div class="col-md-4">
                    <label class="form-label" for="search">Search Destination</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Enter destination name..." value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="package">Filter by Package</label>
                    <select id="package" name="package" class="form-select">
                        <option value="">All Packages</option>
                        <?php
                        $packageQuery = $conn->query("SELECT id, title FROM packages ORDER BY title ASC");
                        while ($pkg = $packageQuery->fetch_assoc()):
                            $selected = ($package == $pkg['id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $pkg['id'] ?>" <?= $selected ?>><?= htmlspecialchars($pkg['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary me-2">Search / Filter</button>
                        <a href="destinations.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Destination Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 10%;">Name</th>
                            <th style="width: 25%;">Description</th>
                            <th style="width: 10%;">Image</th>
                            <th style="width: 7%;">Price</th>
                            <th style="width: 22%;">Map</th>
                            <th style="width: 8%;">Package</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): 
                            $desc = htmlspecialchars($row['description']);
                            $needsToggle = strlen($desc) > 150;
                        ?>
                        <tr>
                            <td><?= (int)$row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td class="description-cell">
                                <div class="desc-wrapper" title="<?= $desc ?>">
                                    <?= nl2br($desc) ?>
                                </div>
                                <?php if ($needsToggle): ?>
                                    <span class="toggle-description">Show more</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <img src="../uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="Image" />
                            </td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                            <td>
                                <div class="map-preview-container">
                                    <?= $row['location_map'] ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['package_title'] ?? 'None') ?></td>
                            <td>
                                <a href="edit_destination.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-warning w-100 mb-1">✏️ Edit</a>
                                <a href="delete_destination.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Delete this destination?');">🗑️ Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.toggle-description').click(function(){
        const $toggle = $(this);
        const $cell = $toggle.closest('td.description-cell');
        const $descWrapper = $cell.find('.desc-wrapper');

        if ($cell.hasClass('expanded')) {
            $cell.removeClass('expanded');
            $toggle.text('Show more');
        } else {
            $cell.addClass('expanded');
            $toggle.text('Show less');
        }
    });
});
</script>
</body>
</html>
