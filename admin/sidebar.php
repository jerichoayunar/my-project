<!-- sidebar.php -->
<style>
 .sidebar {
  width: 250px;
  background-color: #343a40;
  color: white;
  padding: 20px;
  position: fixed;         /* Make sidebar fixed */
  top: 0;
  left: 0;
  bottom: 0;               /* Fill entire height */
  overflow-y: auto;        /* Scrollable if content overflows */
}


  .sidebar .heads {
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center;
  }

  .sidebar img {
    width: 100px;
    border-radius: 50%;
    display: block;
    margin: 0 auto 20px;
  }

  .menu {
    list-style: none;
    padding: 0;
  }

  .menu li {
    margin: 15px 0;
  }

  .menu a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 5px;
  }

  .menu a.active,
  .menu a:hover {
    background-color: #007bff;
  }
</style>

<div class="sidebar">
  <div class="heads">A D M I N</div>
  <img src="../../image/bukidnonupdates.jpg" alt="logo">
  <ul class="menu">
    <li><a href="../Dashboard/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
    <li><a href="../Destinations/destinations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'destinations.php' ? 'active' : '' ?>">Manage Destinations</a></li>
    <li><a href="../Packages/packages.php" class="<?= basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : '' ?>">Manage Tour Packages</a></li>
    <li><a href="../Bookings/bookings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : '' ?>">View Bookings</a></li>
    <li><a href="../Clients/clients.php" class="<?= basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'active' : '' ?>">Clients</a></li>
    <li><a href="../Inquiries/inquiries.php" class="<?= basename($_SERVER['PHP_SELF']) == 'inquiries.php' ? 'active' : '' ?>">Inquiries</a></li>
    <li><a href="../logout.php">Logout</a></li>
  </ul>
</div>
