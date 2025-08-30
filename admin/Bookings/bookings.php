<?php
include '../../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Bookings</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>

        .dashboard {
            display: flex;
        }

        .main-content {
            margin-left: 250px;      /* Same as sidebar width */
            padding: 20px;
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

        .btn {
            margin-right: 5px;
        }

        .package-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .package-list li {
            margin-bottom: 0.4rem;
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
                <h2 class="mb-3">Client Bookings</h2>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
                <?php endif; ?>

                <!-- Filter/Search Form -->
                <form method="GET" class="row g-3 filter-form mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Booking Date</label>
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter by Client Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter client name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter by Status</label>
                        <select name="status" class="form-select">
                            <option value="" <?= empty($_GET['status']) ? 'selected' : '' ?>>All</option>
                            <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Confirmed" <?= ($_GET['status'] ?? '') === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="Cancelled" <?= ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="bookings.php" class="btn btn-outline-secondary me-2">Reset</a>
                            <a href="export_bookings_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-outline-secondary me-2" target="_blank">
                                🖨️ Print PDF
                            </a>
                        </div>
                </form>


                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Packages</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $date = $_GET['date'] ?? '';
                            $name = $_GET['name'] ?? '';
                            $status = $_GET['status'] ?? '';

                            $conditions = [];

                            if (!empty($date)) {
                                $date_escaped = $conn->real_escape_string($date);
                                $conditions[] = "DATE(bookings.booking_date) = '$date_escaped'";
                            }

                            if (!empty($name)) {
                                $conditions[] = "clients.name LIKE '%" . $conn->real_escape_string($name) . "%'";
                            }

                            if (!empty($status)) {
                                $conditions[] = "bookings.status = '" . $conn->real_escape_string($status) . "'";
                            }

                            $whereClause = '';
                            if (!empty($conditions)) {
                                $whereClause = "WHERE " . implode(' AND ', $conditions);
                            }

                            $sql = "SELECT 
                                        bookings.client_id,
                                        clients.name AS client_name,
                                        bookings.booking_date,
                                        bookings.status,
                                        GROUP_CONCAT(packages.title SEPARATOR '||') AS package_titles,
                                        GROUP_CONCAT(packages.details SEPARATOR '||') AS package_details,
                                        GROUP_CONCAT(packages.price SEPARATOR '||') AS package_prices,
                                        GROUP_CONCAT(bookings.id SEPARATOR ',') AS booking_ids
                                    FROM bookings 
                                    LEFT JOIN clients ON bookings.client_id = clients.id 
                                    LEFT JOIN packages ON bookings.package_id = packages.id 
                                    $whereClause
                                    GROUP BY bookings.client_id, bookings.booking_date, bookings.status
                                    ORDER BY bookings.booking_date DESC";

                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                $counter = 1;
                                while ($row = $result->fetch_assoc()) {
                                    $titles = explode('||', $row['package_titles']);
                                    $details = explode('||', $row['package_details']);
                                    $prices = explode('||', $row['package_prices']);
                                    $booking_ids_arr = explode(',', $row['booking_ids']);

                                    echo "<tr>
                                            <td>{$counter}</td>
                                            <td>" . htmlspecialchars($row['client_name']) . "</td>
                                            <td><ul class='package-list'>";

                                    for ($i = 0; $i < count($titles); $i++) {
                                        $title = htmlspecialchars($titles[$i]);
                                        $price = number_format(floatval($prices[$i]), 2);
                                        echo "<li>
                                                <strong>$title</strong> - <span class='text-success'>₱$price</span>
                                            </li>";
                                    }


                                    echo "</ul></td>
                                            <td>" . htmlspecialchars($row['booking_date']) . "</td>
                                            <td>" . htmlspecialchars($row['status']) . "</td>
                                            <td>
                                                <form method='POST' action='update_booking_status.php' class='d-flex'>";

                                    foreach ($booking_ids_arr as $bid) {
                                        echo "<input type='hidden' name='booking_ids[]' value='" . htmlspecialchars(trim($bid)) . "'>";
                                    }

                                    echo "<select name='status' class='form-select form-select-sm me-2'>
                                                    <option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                                    <option value='Confirmed'" . ($row['status'] == 'Confirmed' ? ' selected' : '') . ">Confirmed</option>
                                                    <option value='Cancelled'" . ($row['status'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                                                </select>
                                                <button type='submit' class='btn btn-sm btn-primary'>Update</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form method='POST' action='delete_booking.php' onsubmit='return confirm(\"Delete all these bookings?\");'>
                                                    <input type='hidden' name='booking_ids' value='" . htmlspecialchars($row['booking_ids']) . "'>
                                                    <button type='submit' class='btn btn-sm btn-danger'>🗑️ Delete</button>
                                                </form>
                                            </td>
                                        </tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No bookings found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
