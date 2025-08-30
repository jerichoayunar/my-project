<?php
session_start();  // Start session on the page
include '../includes/db.php'; // Include the database connection file
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Set character encoding -->
    <title>Explore Bukidnon - Index</title> <!-- Page title -->

    <!-- External CSS and icon libraries -->
    <link rel="stylesheet" href="assets/style.css"> <!-- Custom styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header/Navbar -->
<?php include 'navbar.php'; ?>


<!-- Hero Section -->
<section class="hero text-center py-5">
  <div class="hero-content">
    <h1>Explore, Experience, Enjoy Bukidnon!</h1>  <!-- Main hero heading -->
    <p>Discover amazing destinations and create unforgettable memories.</p>  <!-- Subheading -->
    <p>Bukidnon Updates</p>  <!-- Additional tagline -->
    <a href="packages.php" class="click">View Tour Packages</a>  <!-- CTA button -->
  </div>
</section>



<!-- Featured Destinations Section -->
<div class="featured-section my-5 py-5">
    <div class="container">
        <?php include 'destinations_content.php'; ?>  <!-- Include dynamic destinations content -->
    </div>
</div>


<!-- Featured Packages -->
<div class="container my-5">
    <?php include 'packages_content.php'; ?>  <!-- Include dynamic packages content -->
</div>



 <!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="footer-section">
        <h3>Tour & Travel</h3>
        <p>Your trusted partner in travel.</p>
      </div>

      <!-- Quick Navigation Links -->
      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="packages.php">Packages</a></li>
          <li><a href="destinations.php">Destinations</a></li>
          <li><a href="inquiry.php">Send Inquiry</a></li>
        </ul>
      </div>

       <!-- Social Media Links -->
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

  <!-- This is required to make the dropdown menu work correctly. -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
</body>
</html>
