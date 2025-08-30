<?php
// Include the database connection
include '../includes/db.php';

// Fetch all destinations from the database ordered by ID in ascending order
$destinations = $conn->query("SELECT * FROM destinations ORDER BY id ASC");
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Popular Destinations</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <style>
  /* Global body styling */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    padding: 0;
    margin: 0;
    color: #333;
  }

  /* Title styling */
  .popular-destinations h2 {
    font-weight: 700;
    font-size: 36px;
    margin-bottom: 15px;
    letter-spacing: 1px;
    color: : #f0f2f5; /* ⚠️ There is an extra colon here - this may break the style */
    user-select: none;
  }

  /* Top navigation bar with title and nav buttons */
  .top-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  /* Container for the nav buttons */
  .nav-buttons {
    display: flex;
    gap: 12px;
  }

  /* Nav buttons styling */
  .nav-buttons button {
    color: #444;
    background-color: #fff;
    border: 2px solid #444;
    padding: 8px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }

  /* Icon spacing inside buttons */
  .nav-buttons button i {
    margin-right: 8px;
    font-size: 16px;
  }

  /* Button hover effects */
  .nav-buttons button:hover {
    background-color: #444;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  /* Slider container styling */
  .slider-container {
    position: relative;
    overflow: hidden;
    margin-top: 20px;
  }

  /* Slider element holding cards */
  .slider {
    display: flex;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
    scroll-snap-type: x mandatory;
  }

  /* Hide scrollbar in WebKit browsers */
  .slider::-webkit-scrollbar {
    display: none;
  }

  /* Individual destination card styling */
  .dest-card {
    flex: 0 0 calc(25% - 20px);
    margin-right: 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    overflow: hidden;
    scroll-snap-align: start;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    user-select: none;
  }

  /* Hover effect on card */
  .dest-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
  }

  /* Card image styling */
  .dest-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.5s ease;
    position: relative;
  }

  /* Image zoom effect on hover */
  .dest-card:hover img {
    transform: scale(1.1);
  }

  /* Gradient overlay effect on hover */
  .dest-card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 200px;
    background: linear-gradient(180deg, rgba(0,0,0,0.05), rgba(0,0,0,0.15));
    pointer-events: none;
    border-radius: 14px 14px 0 0;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .dest-card:hover::after {
    opacity: 1;
  }

  /* Card body section */
  .dest-card-body {
    padding: 18px 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }

  /* Destination name styling */
  .dest-card h4 {
    font-size: 20px;
    color: #222;
    margin-bottom: 8px;
    font-weight: 700;
  }

  /* Description styling with 3-line clamp */
  .dest-card p {
    font-size: 14px;
    color: #666;
    flex-grow: 1;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }

  /* Responsive for tablets */
  @media (max-width: 992px) {
    .dest-card {
      flex: 0 0 calc(50% - 20px);
    }
  }

  /* Responsive for mobile */
  @media (max-width: 576px) {
    .dest-card {
      flex: 0 0 100%;
    }

    .nav-buttons {
      justify-content: center;
      margin-top: 15px;
    }
  }
  </style>
</head>
<body>

<!-- Section for displaying popular destinations -->
<section class="popular-destinations">
  <div class="top-nav">
    <h2>Popular Destinations</h2>

    <!-- Navigation buttons for scrolling the slider -->
    <div class="nav-buttons">
      <button id="prevBtn"><i class="fas fa-chevron-left"></i> Prev</button>
      <button id="nextBtn">Next <i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <!-- Slider that holds all destination cards -->
  <div class="slider-container">
    <div class="slider" id="slider">
      <!-- PHP loop to render each destination card -->
      <?php while ($row = $destinations->fetch_assoc()): ?>
        <div class="dest-card">
          <img src="../admin/uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="dest-card-body">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <p><?= htmlspecialchars($row['description']) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- JavaScript for slider navigation functionality -->
<script>
  const slider = document.getElementById('slider');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  // Function to get the width of a single card including margin
  function getCardWidth() {
    const card = slider.querySelector('.dest-card');
    const style = getComputedStyle(card); 
    return card.offsetWidth + parseInt(style.marginRight);
  }

  // Scroll the slider to the right
  nextBtn.addEventListener('click', () => {
    slider.scrollBy({ left: getCardWidth() * 4, behavior: 'smooth' });
  });

  // Scroll the slider to the left
  prevBtn.addEventListener('click', () => {
    slider.scrollBy({ left: -getCardWidth() * 4, behavior: 'smooth' });
  });
</script>

</body>
</html>
