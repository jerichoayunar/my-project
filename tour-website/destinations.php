<?php
// Connect to the database
include '../includes/db.php';

// Fetch all destination data ordered by ID in ascending order
$destinations = $conn->query("SELECT * FROM destinations ORDER BY id ASC");
$destData = [];
while ($row = $destinations->fetch_assoc()) {
  $destData[] = $row; // Store each row into the $destData array
}
?>

<!-- HTML document structure -->
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic meta setup -->
  <meta charset="UTF-8">
  <title>Destinations - Bukidnon Tours</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- External and internal stylesheets -->
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    .navbar {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .about {
      background: linear-gradient(to right, rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.4)), url('../image/bukid.jpg') no-repeat center center/cover;
      padding: 4rem 2rem;
      color: #fff;
      text-align: center;
    }

    .about h1 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 2rem;
    }

    .about-container {
      max-width: 1300px;
      margin: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      align-items: center;
      justify-content: center;
      padding-top: 2rem;
    }

    .about-slider {
      flex: 1;
      max-width: 600px;
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    }

    .image-slide {
      display: none;
      width: 100%;
      height: 400px;
      object-fit: cover;
      transition: opacity 0.5s ease;
    }

    .image-slide.active {
      display: block;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.98); }
      to { opacity: 1; transform: scale(1); }
    }

    .slider-buttons {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
      transform: translateY(-50%);
      padding: 0 1rem;
      pointer-events: none;
    }

    .slider-nav {
      pointer-events: auto;
      background-color: rgba(0, 0, 0, 0.6);
      border: none;
      color: white;
      font-size: 1.5rem;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .slider-nav:hover {
      background-color: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }

    .info-panel {
      flex: 1;
      max-width: 500px;
      background: #ffffff;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .info-panel:hover {
      transform: translateY(-3px);
    }

    .info-panel h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 1rem;
      color: #333;
    }

    .info-panel p {
      font-size: 1.1rem;
      margin-bottom: 1rem;
      color: #555;
    }

    .map-box iframe {
      width: 100%;
      height: 250px;
      border: none;
      border-radius: 10px;
      margin-bottom: 1rem;
    }

    .price-duration {
      display: flex;
      justify-content: flex-start;
      gap: 1rem;
      align-items: center;
      font-size: 1rem;
    }

    .price, .destination-name {
      background: #212529;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .about-container {
        flex-direction: column;
      }

      .image-slide {
        height: 300px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar section included from external file -->
<?php include 'navbar.php'; ?>

<!-- Explore Bukidnon Section -->
<section class="about">
  <h1>Explore Bukidnon</h1>
  <div class="about-container">
    <!-- Destination Slider -->
    <div class="about-slider">
      <?php foreach ($destData as $index => $dest): ?>
        <img src="../admin/uploads/<?= $dest['image_url'] ?>" alt="<?= $dest['name'] ?>" class="image-slide <?= $index === 0 ? 'active' : '' ?>">
      <?php endforeach; ?>
      <div class="slider-buttons">
        <button class="slider-nav" onclick="prevSlide()"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-nav" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>

    <!-- Destination Info Panel -->
    <div class="info-panel">
      <h2 id="name"><?= $destData[0]['name'] ?></h2>
      <p id="description"><?= $destData[0]['description'] ?></p>
      <div class="map-box" id="map-embed"><?= $destData[0]['location_map'] ?></div>
      <div class="price-duration">
        <div class="price" id="price">₱<?= number_format($destData[0]['price'], 2) ?></div>
      </div>
    </div>
  </div>
</section>

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

<!-- JavaScript for Image Slider Functionality -->
<script>
  // Get destination data from PHP
  const destinations = <?= json_encode($destData) ?>;
  let current = 0; // Current slide index
  let interval; // Interval holder

  // DOM elements
  const slides = document.querySelectorAll('.image-slide');
  const desc = document.getElementById('description');
  const price = document.getElementById('price');
  const name = document.getElementById('name');
  const mapEmbed = document.getElementById('map-embed');

  // Function to show a specific slide and update info panel
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
    desc.textContent = destinations[index].description;
    price.textContent = '₱' + parseFloat(destinations[index].price).toLocaleString(undefined, {minimumFractionDigits: 2});
    name.textContent = destinations[index].name;
    mapEmbed.innerHTML = destinations[index].location_map;
  }

  // Go to the next slide
  function nextSlide() {
    current = (current + 1) % slides.length;
    showSlide(current);
  }

  // Go to the previous slide
  function prevSlide() {
    current = (current - 1 + slides.length) % slides.length;
    showSlide(current);
  }

  // Start auto-sliding
  function startAutoSlide() {
    interval = setInterval(nextSlide, 15000); // Change slide every 15 seconds
  }

  // Stop auto-sliding
  function stopAutoSlide() {
    clearInterval(interval);
  }

  // Pause slider on hover
  document.querySelector('.about-slider').addEventListener('mouseenter', stopAutoSlide);
  document.querySelector('.about-slider').addEventListener('mouseleave', startAutoSlide);

  // Start the auto slide on load
  startAutoSlide();
</script>

<!-- Bootstrap Bundle with Popper for dropdowns and navbar toggling -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>