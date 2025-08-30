<?php
include '../../includes/db.php';

$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$phone = $_GET['phone'] ?? '';

$conditions = [];

if (!empty($name)) {
    $conditions[] = "name LIKE '%" . $conn->real_escape_string($name) . "%'";
}
if (!empty($email)) {
    $conditions[] = "email LIKE '%" . $conn->real_escape_string($email) . "%'";
}
if (!empty($phone)) {
    $conditions[] = "phone LIKE '%" . $conn->real_escape_string($phone) . "%'";
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
$query = "SELECT * FROM clients $whereClause ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Clients</title>
    <link rel="stylesheet" href="../styles.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            background-color: #f9f9f9;
            margin-left: 250px;      /* Same as sidebar width */
            padding: 20px;
        }

        .btn {
            margin-right: 5px;
        }

        .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .filter-form .col-md-3 {
            padding-right: 1rem;
        }

        .btn, .form-select, .form-control {
            font-size: 0.9rem;
        }

        .filter-form .d-flex {
            align-items: end;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <?php include '../sidebar.php'; ?>

        <div class="main-content">
            <?php include '../topbar.php'; ?>

            <div class="container mt-4">
                <h2 class="mb-3">Manage Clients</h2>

                <!-- Filter Form -->
                <form method="GET" class="row g-3 filter-form mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name..." value="<?= htmlspecialchars($name) ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter by Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter email..." value="<?= htmlspecialchars($email) ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter by Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone..." value="<?= htmlspecialchars($phone) ?>" />
                    </div>
                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <a href="clients.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td>
                                        <a href="edit_client.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                        <a href="delete_client.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this client?');">🗑️ Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No clients found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
