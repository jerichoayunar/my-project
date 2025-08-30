<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .dashboard {
            display: flex;
            height: 100vh;
        }

        .main-content {
        margin-left: 250px;      /* Same as sidebar width */
        padding: 20px;
        }


        .card {
            margin: 10px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }


        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a237e;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .card-text.fs-3 {
            font-size: 1.5rem !important;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .card.table-primary-bg {
            background-color: #cfe2ff !important;
            color: #000 !important;
            border: none;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.08);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .description-cell {
            max-width: 250px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            position: relative;
        }

        .desc-wrapper {
            max-height: 3em; /* ~3 lines */
            overflow: hidden;
            position: relative;
            transition: max-height 0.3s ease;
        }

        .desc-wrapper.expanded {
            max-height: none;
        }

        .toggle-description {
            display: block;
            color: #007bff;
            cursor: pointer;
            margin-top: 5px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <?php include '../sidebar.php'; ?>

        <div class="main-content">
            <?php include '../topbar.php'; ?>

            <?php
            include '../../includes/db.php';

            $bookings_count = $conn->query("SELECT COUNT(DISTINCT client_id, booking_date) AS total FROM bookings WHERE status IN ('pending', 'confirmed')")->fetch_assoc()['total'];
            $clients_count = $conn->query("SELECT COUNT(*) AS total FROM clients")->fetch_assoc()['total'];
            $destinations_count = $conn->query("SELECT COUNT(*) AS total FROM destinations")->fetch_assoc()['total'];
            $inquiries_count = $conn->query("SELECT COUNT(*) AS total FROM inquiries")->fetch_assoc()['total'];
            $packages_count = $conn->query("SELECT COUNT(*) AS total FROM packages")->fetch_assoc()['total'];

            $recent_inquiries = $conn->query("SELECT name, email, message, created_at FROM inquiries ORDER BY created_at DESC LIMIT 5");
            ?>

            <div class="content p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card table-primary-bg">
                            <div class="card-body">
                                <h5 class="card-title">Total Bookings</h5>
                                <p class="card-text fs-3"><?= $bookings_count ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card table-primary-bg">
                            <div class="card-body">
                                <h5 class="card-title">Destinations</h5>
                                <p class="card-text fs-3"><?= $destinations_count ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card table-primary-bg">
                            <div class="card-body">
                                <h5 class="card-title">Active Clients</h5>
                                <p class="card-text fs-3"><?= $clients_count ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card table-primary-bg">
                            <div class="card-body">
                                <h5 class="card-title">Inquiries</h5>
                                <p class="card-text fs-3"><?= $inquiries_count ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card table-primary-bg">
                            <div class="card-body">
                                <h5 class="card-title">Tour Packages</h5>
                                <p class="card-text fs-3"><?= $packages_count ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Inquiries Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Recent Inquiries</h5>
                        <table class="table">
                            <thead class="table-primary">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_inquiries->num_rows > 0): ?>
                                    <?php while ($row = $recent_inquiries->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td class="description-cell">
                                                <div class="desc-wrapper" title="<?= htmlspecialchars($row['message']) ?>">
                                                    <?= nl2br(htmlspecialchars($row['message'])) ?>
                                                </div>
                                                <?php if (strlen($row['message']) > 150): ?>
                                                    <span class="toggle-description">Show more</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">No recent inquiries.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- End of .main-content -->
    </div> <!-- End of .dashboard -->

    <!-- Show More / Less Script -->
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
