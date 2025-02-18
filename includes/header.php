<?php
include_once 'language.php';
include_once '../config/db.php';

function updateUrlParameter($param, $value) {
  $url = $_SERVER['REQUEST_URI'];
  $urlParts = parse_url($url);
  $query = array();
  if (isset($urlParts['query'])) {
    parse_str($urlParts['query'], $query);
  }
  $query[$param] = $value;
  $newQuery = http_build_query($query);
  $path = isset($urlParts['path']) ? $urlParts['path'] : '/';
  return $path . '?' . $newQuery;
}

$stmtLogo = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
$stmtLogo->execute(['site_logo']);
$logoRow = $stmtLogo->fetch();
$logoPath = ($logoRow && !empty($logoRow['setting_value'])) ? '/public/images/' . $logoRow['setting_value'] : '/public/images/logo.png';
?>
<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr'; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo __('villa_rental'); ?></title>
  <link rel="shortcut icon" type="image/x-icon" href="/public/images/favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="/public/css/header.css">
  <link rel="stylesheet" href="/public/css/whatsapp.css">
</head>
<body>
  <header>
    <div class="header-container">
      <div class="header-left">
        <a href="/public/index.php">
          <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo __('villa_rental'); ?>">
        </a>
      </div>
      <div class="header-center">
        <nav class="desktop-nav">
          <a href="/public/index.php"><?php echo __('home'); ?></a>
          <a href="/public/villas.php"><?php echo __('villas'); ?></a>
          <a href="/public/contact.php"><?php echo __('contact'); ?></a>
          <a href="/public/faq.php"><?php echo __('faq'); ?></a>
        </nav>
      </div>
      <div class="header-right">
        <div class="language-dropdown">
          <button class="dropdown-btn">
            <span class="world-icon">🌐</span>
          </button>
          <div class="dropdown-content">
            <a href="<?php echo updateUrlParameter('lang', 'tr'); ?>">Türkçe</a>
            <a href="<?php echo updateUrlParameter('lang', 'en'); ?>">English</a>
            <a href="<?php echo updateUrlParameter('lang', 'ar'); ?>">العربية</a>
          </div>
        </div>
        <button class="hamburger" id="hamburgerBtn">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </button>
      </div>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <nav class="mobile-nav">
          <a href="/public/index.php"><?php echo __('home'); ?></a>
          <a href="/public/villas.php"><?php echo __('villas'); ?></a>
          <a href="/public/contact.php"><?php echo __('contact'); ?></a>
          <a href="/public/contact.php"><?php echo __('faq'); ?></a>
          <div class="mobile-language">
            <a href="<?php echo updateUrlParameter('lang', 'tr'); ?>">Türkçe</a>
            <a href="<?php echo updateUrlParameter('lang', 'en'); ?>">English</a>
            <a href="<?php echo updateUrlParameter('lang', 'ar'); ?>">العربية</a>
          </div>
      </nav>
    </div>
  </header>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var hamburger = document.getElementById("hamburgerBtn");
      var mobileMenu = document.getElementById("mobileMenu");
      hamburger.addEventListener("click", function() {
        mobileMenu.classList.toggle("active");
      });
    });
  </script>
</body>
</html>
