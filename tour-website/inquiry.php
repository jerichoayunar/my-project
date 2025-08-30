<?php
include '../includes/db.php'; // Include the database connection file
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">  <!-- Set character encoding -->
  <title>Destinations - Bukidnon Tours</title>  <!-- Page title -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">   <!-- Responsive design -->

  <!-- External Stylesheets -->
  <link rel="stylesheet" href="assets/style.css">  <!-- Custom CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Inline CSS for blurred background effect -->
  <style>
.bg-container {
  position: relative; /* Parent container for blur effect */
  overflow: hidden;
  min-height: 90.8vh; /* Full view height minus header */
  width: 100%;
}

.bg-container::before {
  content: "";  /* Pseudo-element for background image */
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: url('../image/bukid.jpg') no-repeat center center fixed;  /* Background image */
  background-size: cover;
  filter: blur(3px); /* Apply blur effect */
  z-index: -1;  /* Send behind content */
}

.bg-blur-content {
  position: relative;  /* Content stays on top */
  z-index: 1;
  min-height: 92.8vh;
  background: rgba(255, 255, 255, 0.3);  /* Semi-transparent white background */
  backdrop-filter: blur(2px);   /* Apply blur effect inside container */
  -webkit-backdrop-filter: blur(2px);  /* Safari support */
  border-radius: 16px;  /* Rounded corners */
  padding: 2rem;   /* Inner spacing */
}
  </style>
</head>
<body>

<!-- Navigation -->
<?php include 'navbar.php'; ?>  <!-- Include the navigation menu -->

<!-- Blurred Background Container -->
<div class="bg-container">
  <div class="bg-blur-content py-5">
    <div class="container">
      <h2 class="mb-4">Send Us an Inquiry</h2>

      <!-- Inquiry Form -->
      <form action="submit_inquiry.php" method="POST">
          <div class="mb-3">
              <input type="text" name="name" class="form-control" placeholder="Your Name" required>
          </div>
          <div class="mb-3">
              <input type="email" name="email" class="form-control" placeholder="Your Email" required>
          </div>
          <div class="mb-3">
              <textarea name="message" class="form-control" rows="4" placeholder="Your Message" required></textarea>
          </div>
          <button type="submit" class="btn btn-success">Send Inquiry</button>
      </form>
    </div>
  </div>
</div>


 <!-- Footer -->
  <footer>
    <div class="footer-content">
      <!-- Company Info -->
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