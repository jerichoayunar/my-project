<?php
// Include database connection
include '../includes/db.php';

// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect user to login page if not logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

// Get logged-in client's ID from session
$client_id = $_SESSION['client_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic meta tags for character encoding and responsiveness -->
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Booking History</title>

    <!-- External CSS and Bootstrap for styling -->
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Inline styles for specific layout and design -->
    <style>
body {
    background: linear-gradient(to right, #e0f7fa, #f0f4f8);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.main-content {
    flex: 1; /* Takes up remaining height */
}
.package-list {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
}
.package-list li {
    margin-bottom: 0.4rem;
}
.package-details-wrapper {
    display: block;
    max-width: 100%;
    margin-bottom: 4px;
    position: relative;
}
.package-details {
    /* Display first 3 lines only and hide the rest */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
    white-space: pre-line;
    font-size: 1rem;
    color: #444;
    transition: all 0.3s;
}
.package-details.expanded {
    /* Show full details when expanded */
    -webkit-line-clamp: unset;
    max-height: none;
    overflow: visible;
}
.toggle-details {
    /* "Show more" link style */
    display: inline-block;
    margin-left: 5px;
    margin-top: 4px;
    font-size: 0.95rem;
    color: #007bff;
    cursor: pointer;
    user-select: none;
}
    </style>
</head>
<body>

<!-- Navigation bar -->
<?php include 'navbar.php'; ?>

<!-- Main content area -->
<div class="main-content container mt-4" style="margin-bottom: 240px;">
    <h2 class="mb-3">My Booking History</h2>

    <!-- Table showing booking records -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Booking Date</th>
                    <th>Packages</th>
                    <th>Status</th>
                    <th>Cancel Booking</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SQL query to fetch booking data for the logged-in client
                $sql = "SELECT 
                            bookings.booking_date,
                            bookings.status,
                            GROUP_CONCAT(packages.title SEPARATOR '||') AS package_titles,
                            GROUP_CONCAT(packages.details SEPARATOR '||') AS package_details,
                            GROUP_CONCAT(packages.price SEPARATOR '||') AS package_prices,
                            GROUP_CONCAT(bookings.id SEPARATOR ',') AS booking_ids
                        FROM bookings
                        LEFT JOIN packages ON bookings.package_id = packages.id
                        WHERE bookings.client_id = ?
                        GROUP BY bookings.booking_date, bookings.status
                        ORDER BY bookings.booking_date DESC";

                // Prepare and execute query
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $client_id);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if any results were returned
                if ($result && $result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        // Split grouped fields into arrays
                        $titles = explode('||', $row['package_titles']);
                        $details = explode('||', $row['package_details']);
                        $prices = explode('||', $row['package_prices']);
                        $booking_ids_arr = explode(',', $row['booking_ids']);

                        echo "<tr>";
                        echo "<td>{$counter}</td>";
                        echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                        echo "<td><ul class='package-list'>";

                        // Display each package in a list item
                        for ($i = 0; $i < count($titles); $i++) {
                            $title = htmlspecialchars($titles[$i]);
                            $raw_detail = $details[$i];
                            $clean_detail = preg_replace("/(\r?\n){2,}/", "\n", trim($raw_detail)); // Remove extra line breaks
                            $escaped_detail = nl2br(htmlspecialchars($clean_detail)); // Escape HTML and convert newlines
                            $price = number_format(floatval($prices[$i]), 2); // Format price

                            echo "<li>
                                    <strong>$title</strong><br>
                                    <span class='package-details-wrapper'>
                                        <span class='package-details'>$escaped_detail</span>";
                            // Add toggle if content is long
                            if (strlen($clean_detail) > 100) {
                                echo " <span class='toggle-details'>Show more</span>";
                            }
                            echo "</span><br>
                                    <span class='text-success'>₱$price</span>
                                </li>";
                        }

                        echo "</ul></td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                        // Form to cancel a booking
                        echo "<td>
                                <form method='POST' action='cancel_booking.php' onsubmit='return confirm(\"Are you sure you want to cancel this booking?\");'>
                                    <input type='hidden' name='booking_ids' value='" . htmlspecialchars($row['booking_ids']) . "'>
                                    <button type='submit' class='btn btn-sm btn-danger'>Cancel</button>
                                </form>
                            </td>";
                        echo "</tr>";
                        $counter++;
                    }
                } else {
                    // If no bookings are found
                    echo "<tr><td colspan='5' class='text-center'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer section -->
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for toggling package details view -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-details').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const details = this.closest('.package-details-wrapper').querySelector('.package-details');
            if (details.classList.contains('expanded')) {
                details.classList.remove('expanded');
                this.textContent = 'Show more';
            } else {
                details.classList.add('expanded');
                this.textContent = 'Show less';
            }
        });
    });
});
</script>
</body>
</html>
