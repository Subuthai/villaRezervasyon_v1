<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';
include_once 'admin_header.php';
?>

<div class="admin-dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="dashboard.php">
        <img src="/public/images/logo.png" alt="Admin Logo">
      </a>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="villa_listele.php">Villa Yönetimi</a></li>
        <li><a href="hero_slider.php">Hero Slider</a></li>
        <li><a href="reservation_list.php">Rezervasyon Yönetimi</a></li>
        <li><a href="message_list.php">Mesaj Yönetimi</a></li>
        <li><a href="faq_admin.php">SSS Yönetimi</a></li>
        <li><a href="places_admin.php">Gezilecek Yerler Yönetimi</a></li>
        <li><a href="promo_settings.php">Ana Sayfa Promo Yönetimi</a></li>
        <li><a href="services.php">Servisler/Hizmetler</a></li>
        <li><a href="admin_privacy_policy.php">Gizlilik Politikası</a></li>
        <li><a href="settings.php">Ayarlar</a></li>
      </ul>
    </nav>
  </aside>
  <section class="main-content">
    <h1>Admin Paneli Dashboard</h1>
    <div class="dashboard-widgets">
      <div class="widget">
        <h2>Toplam Villa</h2>
        <?php 
          $stmt = $pdo->query("SELECT COUNT(*) as total FROM villas");
          $row = $stmt->fetch();
          echo "<p>" . $row['total'] . "</p>";
        ?>
      </div>
      <div class="widget">
        <h2>Toplam Rezervasyon</h2>
        <?php 
          $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations");
          $row = $stmt->fetch();
          echo "<p>" . $row['total'] . "</p>";
        ?>
      </div>
      <div class="widget">
        <h2>Toplam Mesaj</h2>
        <?php 
          $stmt = $pdo->query("SELECT COUNT(*) as total FROM messages");
          $row = $stmt->fetch();
          echo "<p>" . $row['total'] . "</p>";
        ?>
      </div>
    </div>
  </section>
</div>

<style>
  .admin-dashboard {
    display: flex;
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
  }
  .sidebar {
    background: #4A4A4A;
    width: 250px;
    padding: 20px;
    color: #FFD700;
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
  }
  .sidebar-logo {
    text-align: center;
    margin-bottom: 30px;
  }
  .sidebar-logo img {
    max-width: 100%;
    height: auto;
    transition: transform 0.3s ease;
  }
  .sidebar-logo img:hover {
    transform: scale(1.1);
  }
  .sidebar-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .sidebar-nav ul li {
    margin-bottom: 20px;
  }
  .sidebar-nav ul li a {
    color: #FFD700;
    text-decoration: none;
    font-size: 1.1rem;
    display: block;
    padding: 10px;
    border-radius: 4px;
    transition: background 0.3s ease, transform 0.3s ease;
  }
  .sidebar-nav ul li a:hover,
  .sidebar-nav ul li a.active {
    background: #FFD700;
    color: #4A4A4A;
    transform: translateX(5px);
  }
  .main-content {
    flex: 1;
    padding: 40px;
    background: #f4f4f4;
  }
  .main-content h1 {
    font-size: 2.8rem;
    color: #4A4A4A;
    margin-bottom: 20px;
  }
  .dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }
  .widget {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
  }
  .widget h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #333;
  }
  .widget p {
    font-size: 2rem;
    font-weight: bold;
    color: #4A4A4A;
  }
  @media (max-width: 768px) {
    .admin-dashboard {
      flex-direction: column;
    }
    .sidebar {
      width: 100%;
      flex-direction: row;
      overflow-x: auto;
    }
    .sidebar-nav ul {
      display: flex;
      gap: 10px;
    }
    .sidebar-nav ul li {
      margin-bottom: 0;
    }
    .main-content {
      padding: 20px;
    }
  }
</style>

<?php
include_once 'admin_footer.php';
?>
