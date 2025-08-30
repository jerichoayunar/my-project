<?php
include '../../includes/db.php';

$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

$conditions = [];

if (!empty($name)) {
    $conditions[] = "name LIKE '%" . $conn->real_escape_string($name) . "%'";
}

if (!empty($email)) {
    $conditions[] = "email LIKE '%" . $conn->real_escape_string($email) . "%'";
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
$order = ($sort === 'asc') ? 'ASC' : 'DESC';

$query = "SELECT * FROM inquiries $whereClause ORDER BY created_at $order";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inquiries</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        .dashboard {
            display: flex;
        }
        .main-content {
            flex: 1;
            margin-left: 250px;      /* Same as sidebar width */
            padding: 20px;
        }
        .table td {
            vertical-align: top;
        }
        .desc-wrapper {
            max-height: 2.9em;
            overflow: hidden;
            position: relative;
            transition: max-height 0.3s ease;
            word-wrap: break-word;
        }
        .desc-wrapper.expanded {
            max-height: none;
        }
        .toggle-description {
            display: block;
            color: #007bff;
            cursor: pointer;
            margin-top: 5px;
            font-size: 0.875rem;
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
    </style>
</head>

<body>
<div class="dashboard">
    <?php include '../sidebar.php'; ?>
    <div class="main-content">
        <?php include '../topbar.php'; ?>

        <div class="container mt-4">
            <h2 class="mb-3">Client Inquiries</h2>

            <!-- Filter/Search Form -->
            <form method="GET" class="row g-3 filter-form mb-3">
                <div class="col-md-3">
                    <label class="form-label">Search by Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter name..." value="<?= htmlspecialchars($name) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search by Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter email..." value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Newest First</option>
                        <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Oldest First</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Search / Filter</button>
                    <a href="inquiries.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td>
                                    <div class="desc-wrapper">
                                        <?= nl2br(htmlspecialchars($row['message'])) ?>
                                    </div>
                                    <?php if (strlen($row['message']) > 150): ?>
                                        <span class="toggle-description">Show more</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="delete_inquiry.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this inquiry?');">
                                       🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No inquiries found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggles = document.querySelectorAll(".toggle-description");

        toggles.forEach(toggle => {
            toggle.addEventListener("click", function () {
                const descWrapper = this.previousElementSibling;
                const isExpanded = descWrapper.classList.toggle("expanded");
                this.textContent = isExpanded ? "Show less" : "Show more";
            });
        });
    });
</script>
</body>
</html>
