<?php
// Start the session if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">

    <!-- Website Logo / Brand Name -->
    <a class="navbar-brand" href="index.php">Bukidnon Tours</a>

    <!-- Right Side Buttons and Account Dropdown -->
    <div class="d-flex align-items-center ms-auto gap-2">

      <!-- Navigation Buttons -->
      <a class="btn btn-outline-light" href="packages.php">Packages</a>
      <a class="btn btn-outline-light" href="destinations.php">Destinations</a>
      <a class="btn btn-outline-light" href="inquiry.php">Send Inquiry</a>

      <!-- Account Dropdown -->
      <div class="dropdown">
        <button class="btn p-0 border-0 bg-transparent dropdown-toggle ms-3" 
                type="button" 
                id="accountDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false">
          <!-- User Avatar -->
          <img src="../image/me.jpg" 
               alt="Account" 
               class="rounded-circle" 
               style="width: 25px; height: 25px; object-fit: cover;">
        </button>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
          <?php if (isset($_SESSION['client_id'])): ?>
            <!-- If user is logged in, show profile and logout options -->
            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
            <li><a class="dropdown-item" href="booking_history.php">Booking History</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="login/logout.php">Logout</a></li>
          <?php else: ?>
            <!-- If user is not logged in, show Sign In option -->
            <li><a class="dropdown-item" href="login/login.php">Sign In</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</nav>
