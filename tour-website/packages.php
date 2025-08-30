<?php
// Include database connection
include '../includes/db.php';

// Start the session to access client session data
session_start();

// Check if the client is logged in
$isLoggedIn = isset($_SESSION['client_id']);

if ($isLoggedIn) {
    // Retrieve client information from the database
    $client_id = $_SESSION['client_id'];
    $clientStmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
    $clientStmt->bind_param("i", $client_id);
    $clientStmt->execute();
    $client = $clientStmt->get_result()->fetch_assoc();
    $clientStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta settings and page title -->
    <meta charset="UTF-8">
    <title>Tour Packages - Bukidnon Tours</title>

    <!-- External CSS stylesheets -->
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom inline styles for the package cards -->
    <style>
        /* Hover effects on card images and card container */
        .card-img-container img:hover {
            transform: scale(1.1);
        }
        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .card-title {
            transition: color 0.3s ease;
        }
        .card:hover .card-title {
            color: #0056b3;
        }

        /* Badge on top of affordable packages */
        .badge-custom {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #28a745;
            color: #fff;
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 5px;
            z-index: 2;
        }

        /* Destination tags styling */
        .destination-list {
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .destination-list span {
            display: inline-block;
            background-color: #f0f0f0;
            color: #333;
            padding: 3px 8px;
            border-radius: 12px;
            margin: 2px;
        }

        /* Ensure consistent spacing inside cards */
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-text, .destination-list {
            flex-grow: 1;
        }

        /* Toggle details button style */
        .toggle-details {
            color: #007bff;
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
            margin-top: 4px;
            display: inline-block;
        }

        /* Modal card for selecting packages */
        .package-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            user-select: none;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .package-card.selected {
            background-color: #d0e7ff;
            border-color: #007bff;
        }

        .package-card input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .check-icon {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 1.3rem;
            color: #007bff;
            display: none;
        }

        .package-card.selected .check-icon {
            display: block;
        }

        /* Background styling for packages section */
        .packages-bg {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            width: 100%;
        }

        .packages-bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: url('../image/bukid.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(3px);
            z-index: -1;
        }

        /* Description cell and show more functionality */
        .details-cell {
            max-width: 100%;
            margin-bottom: 10px;
            position: relative;
        }

        .desc-wrapper {
            display: block;
            max-height: 4em;
            overflow: hidden;
            word-break: break-word;
            white-space: pre-line;
            font-size: 1rem;
            color: #444;
            transition: max-height 0.3s;
        }

        .details-cell.expanded .desc-wrapper {
            max-height: none;
            overflow: visible;
        }

        .toggle-details {
            color: #007bff;
            cursor: pointer;
            font-size: 0.95rem;
            user-select: none;
            margin-top: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>

<!-- Include navigation bar -->
<?php include 'navbar.php'; ?>

<!-- Main content container for packages -->
<div class="packages-bg py-5">
    <div class="container my-5">
        <h2 class="text-center mb-4 fw-bold text-dark">Tour Packages</h2>

        <!-- Search and Price Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Search tour packages...">
            </div>
            <div class="col-md-6">
                <select id="priceFilter" class="form-select">
                    <option value="">Filter by price</option>
                    <option value="0-999">Under ₱1,000</option>
                    <option value="1000-4999">₱1,000 – ₱4,999</option>
                    <option value="5000-9999">₱5,000 – ₱9,999</option>
                    <option value="10000-999999">₱10,000 and up</option>
                </select>
            </div>
        </div>

        <div class="row" id="packageList">
            <?php
            // Fetch all packages from the database
            $packageQuery = $conn->query("SELECT * FROM packages");

            // Loop through each package
            while ($package = $packageQuery->fetch_assoc()):
                $package_id = $package['id'];

                // Fetch destinations related to this package
                $destinations = [];
                $destQuery = $conn->prepare("SELECT name FROM destinations WHERE package_id = ?");
                $destQuery->bind_param("i", $package_id);
                $destQuery->execute();
                $destResult = $destQuery->get_result();
                while ($destRow = $destResult->fetch_assoc()) {
                    $destinations[] = $destRow['name'];
                }
            ?>
            <!-- Package Card -->
            <div class="col-md-4 mb-4 package-item" data-title="<?= strtolower(htmlspecialchars($package['title'])) ?>" data-price="<?= $package['price'] ?>">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-img-container position-relative">
                        <?php if ($package['price'] < 2000): ?>
                            <!-- Badge for affordable packages -->
                            <span class="badge-custom">Affordable</span>
                        <?php endif; ?>
                        <!-- Package image -->
                        <img src="../admin/uploads/<?= htmlspecialchars($package['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($package['title']) ?>" style="transition: transform 0.3s ease; width: 100%; height: 200px; object-fit: cover;" onerror="this.onerror=null;this.src='assets/placeholder.jpg';">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary fw-bold"><?= htmlspecialchars($package['title']) ?></h5>

                        <!-- Static rating placeholder -->
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                            <small class="text-muted">(124 reviews)</small>
                        </div>

                        <!-- Special features if included -->
                        <?php if ($package['includes_transport'] || $package['includes_meals'] || $package['includes_stay']): ?>
                            <ul class="list-inline mb-2 text-muted" style="font-size: 0.9rem;">
                                <?php if ($package['includes_transport']): ?>
                                    <li class="list-inline-item me-3"><i class="fas fa-bus"></i> Transport</li>
                                <?php endif; ?>
                                <?php if ($package['includes_meals']): ?>
                                    <li class="list-inline-item me-3"><i class="fas fa-utensils"></i> Meals</li>
                                <?php endif; ?>
                                <?php if ($package['includes_stay']): ?>
                                    <li class="list-inline-item"><i class="fas fa-hotel"></i> Stay</li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted" style="font-size: 0.9rem;">No special features included.</p>
                        <?php endif; ?>

                        <!-- List of destinations included in the package -->
                        <?php if (!empty($destinations)): ?>
                            <div class="destination-list">
                                <strong>Includes:</strong><br>
                                <?php foreach ($destinations as $place): ?>
                                    <span><?= htmlspecialchars($place) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Truncated description with toggle -->
                        <div class="details-cell">
                            <div class="desc-wrapper" title="<?= htmlspecialchars($package['details']) ?>">
                                <?= htmlspecialchars($package['details']) ?>
                            </div>
                            <?php if (strlen($package['details']) > 150): ?>
                                <span class="toggle-details">Show more</span>
                            <?php endif; ?>
                        </div>

                        <!-- Price and book button -->
                        <div class="mt-auto">
                            <p class="card-text mb-2"><strong>Price:</strong> <span class="text-success">₱<?= number_format($package['price'], 2) ?></span></p>
                            <button class="btn btn-primary w-100 openBookingModalBtn" data-package-id="<?= $package['id'] ?>">Book Now</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
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


<!-- Booking Form Modal -->
<div id="bookingModal" class="modal fade" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="modalBookingForm" action="submit_booking.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="bookingModalLabel">Book a Tour</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="client_id" value="<?= isset($client['id']) ? $client['id'] : '' ?>">

          <!-- Package selection cards inside modal -->
          <div class="mb-3">
            <label class="form-label d-block mb-2">Select Packages:</label>
            <div class="row g-3" id="modalPackageList">
              <?php
              // Fetch packages again for modal selection
              $packagesModal = $conn->query("SELECT id, title, details, price FROM packages");
              while ($row = $packagesModal->fetch_assoc()):
              ?>
                <div class="col-md-6 col-lg-4">
                  <label class="package-card d-block" data-package-id="<?= $row['id'] ?>">
                    <input type="checkbox" class="package-checkbox" name="package_ids[]" value="<?= $row['id'] ?>">
                    <i class="fas fa-check-circle check-icon"></i>
                    <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                    <p class="mb-1 text-muted">₱<?= number_format($row['price'], 2) ?></p>
                  </label>
                </div>
              <?php endwhile; ?>
            </div>
          </div>

          <!-- Client Info -->
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" value="<?= isset($client['name']) ? htmlspecialchars($client['name']) : '' ?>" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" value="<?= isset($client['email']) ? htmlspecialchars($client['email']) : '' ?>" readonly>
          </div>

          <!-- Tour date -->
          <div class="mb-3">
            <label class="form-label">Preferred Tour Date</label>
            <input type="date" name="tour_date" class="form-control" required>
          </div>

          <!-- Tour time -->
          <div class="mb-3">
            <label class="form-label">Preferred Tour Time</label>
            <input type="time" name="tour_time" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Confirm Booking</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

  // Filter packages by search and price
  const searchInput = document.getElementById('searchInput');
  const priceFilter = document.getElementById('priceFilter');
  const packageList = document.getElementById('packageList');
  const packageItems = packageList.querySelectorAll('.package-item');

  function filterPackages() {
    const searchTerm = searchInput.value.trim().toLowerCase();
    const priceRange = priceFilter.value;
    packageItems.forEach(item => {
      const title = item.dataset.title.toLowerCase();
      const price = parseFloat(item.dataset.price);
      const matchesSearch = title.includes(searchTerm);
      let matchesPrice = true;

      if (priceRange) {
        const [min, max] = priceRange.split('-').map(Number);
        matchesPrice = price >= min && price <= max;
      }

      item.style.display = (matchesSearch && matchesPrice) ? '' : 'none';
    });
  }

  // Add event listeners to filter inputs
  searchInput.addEventListener('input', filterPackages);
  priceFilter.addEventListener('change', filterPackages);

  // Toggle package details show/hide
 document.querySelectorAll('.toggle-details').forEach(link => {
  link.addEventListener('click', function handler(e) {
    e.preventDefault();
    const p = this.closest('.package-details');
    if (!p) return;

    const isExpanded = this.textContent.toLowerCase().includes('less');

    if (isExpanded) {
      p.innerHTML = p.dataset.short + ' <a href="#" class="toggle-details text-primary ms-2">Show more</a>';
    } else {
      p.innerHTML = p.dataset.full + ' <a href="#" class="toggle-details text-primary ms-2">Show less</a>';
    }
    // Re-attach the event listener to the new link
    p.querySelector('.toggle-details').addEventListener('click', handler);
  });
});

// Show modal when Book Now button is clicked
document.querySelectorAll('.openBookingModalBtn').forEach(btn => {
  btn.addEventListener('click', function () {
    if (!isLoggedIn) {
      window.location.href = 'login/login.php'; // Redirect to login page
      return;
    }
    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    bookingModal.show();
  });
});


  // Card click selects checkbox and toggles highlight properly
  document.querySelectorAll('.package-card').forEach(card => {
    const checkbox = card.querySelector('input[type="checkbox"]');

    // Prevent multiple toggles
    card.addEventListener('click', function (e) {
      // Avoid toggling if the user directly clicked the checkbox
      if (e.target.tagName === 'INPUT' || e.target.closest('label')) return;

      if (checkbox) {
        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);
      }
    });

    // Keep highlight synced with checkbox state
    if (checkbox) {
      checkbox.addEventListener('change', function () {
        card.classList.toggle('selected', checkbox.checked);
      });
    }
  });

  document.querySelectorAll('.toggle-details').forEach(link => {
  link.addEventListener('click', function () {
    const cell = this.closest('.details-cell');
    if (!cell) return;
    cell.classList.toggle('expanded');
    this.textContent = cell.classList.contains('expanded') ? 'Show less' : 'Show more';
  });
});
</script>


</body>
</html>
