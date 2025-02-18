<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$stmtHero = $pdo->query("SELECT * FROM hero_images ORDER BY id ASC");
$heroImages = $stmtHero->fetchAll();

$stmtHeroSettings = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('hero_logo', 'hero_text', 'hero_bottom_text', 'hero_contact_phone', 'hero_contact_email')");
$stmtHeroSettings->execute();
$heroSettings = $stmtHeroSettings->fetchAll(PDO::FETCH_KEY_PAIR);
$heroLogo          = isset($heroSettings['hero_logo']) ? $heroSettings['hero_logo'] : '';
$heroText          = isset($heroSettings['hero_text']) ? $heroSettings['hero_text'] : '';
$heroBottomText    = isset($heroSettings['hero_bottom_text']) ? $heroSettings['hero_bottom_text'] : '';
$heroContactPhone  = isset($heroSettings['hero_contact_phone']) ? $heroSettings['hero_contact_phone'] : '';
$heroContactEmail  = isset($heroSettings['hero_contact_email']) ? $heroSettings['hero_contact_email'] : '';
?>
<link rel="stylesheet" href="/public/css/index.css">

<script>
  document.addEventListener("DOMContentLoaded", function() {
    if (window.innerWidth >= 768) {
      var secretCode = "admin";
      var inputSequence = "";
      document.addEventListener("keydown", function(e) {
        inputSequence += e.key.toLowerCase();
        if (inputSequence.length > secretCode.length) {
          inputSequence = inputSequence.slice(-secretCode.length);
        }
        if (inputSequence === secretCode) {
          window.location.href = "/admin/login.php";
        }
      });
    }
  });

  document.addEventListener("DOMContentLoaded", function() {
    var threshold = 2000;
    var longPressTimer;
    var langButton = document.querySelector(".hamburger");
    if (langButton) {
      langButton.addEventListener("touchstart", function(e) {
        longPressTimer = setTimeout(function() {
          window.location.href = "/admin/login.php";
        }, threshold);
      });
      langButton.addEventListener("touchend", function(e) {
        clearTimeout(longPressTimer);
      });
      langButton.addEventListener("touchmove", function(e) {
        clearTimeout(longPressTimer);
      });
    }
  });
</script>

<main class="index-main">
  <section class="hero-slider">
    <div class="slider-wrapper">
      <?php if ($heroImages && count($heroImages) > 0): ?>
        <?php foreach ($heroImages as $index => $hero): ?>
          <div class="slide <?php echo ($index === 0) ? 'active' : ''; ?>">
            <img src="/uploads/<?php echo htmlspecialchars($hero['image']); ?>" alt="<?php echo htmlspecialchars($hero['caption'] ?? 'Hero Image'); ?>">
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="slide active">
          <img src="/public/images/default-hero.jpg" alt="Default Hero">
        </div>
      <?php endif; ?>
    </div>
    <?php if (!empty($heroLogo) || !empty($heroText)): ?>
      <div class="hero-custom">
        <?php if (!empty($heroLogo)): ?>
          <img src="/public/images/<?php echo htmlspecialchars($heroLogo); ?>" alt="Hero Logo" class="hero-custom-logo">
        <?php endif; ?>
        <?php if (!empty($heroText)): ?>
          <div class="hero-custom-text"><?php echo $heroText; ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="hero-bottom">
      <div class="hero-bottom-line"></div>
      <div class="hero-bottom-info">
        <div class="hero-bottom-left">
          <?php if (!empty($heroBottomText)): ?>
            <?php echo $heroBottomText; ?>
          <?php endif; ?>
        </div>
        <div class="hero-bottom-right">
          <?php if (!empty($heroContactEmail) || !empty($heroContactPhone)): ?>
            <?php if (!empty($heroContactEmail)): ?>
              <span class="hero-contact-email"><?php echo $heroContactEmail; ?></span>
            <?php endif; ?>
            <?php if (!empty($heroContactPhone)): ?>
              <span class="hero-contact-phone"><?php echo $heroContactPhone; ?></span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="hero-overlay">
      <h1><?php echo __('welcome'); ?>!</h1>
      <p><?php echo __('modern_experience'); ?></p>
      <a href="/public/villas.php" class="btn"><?php echo __('view_villas'); ?></a>
    </div>
  </section>

  <div class="content-wrapper">
    <section class="featured-villas">
      <h2 class="section-title"><?php echo __('ourvillas'); ?></h2>
      <div class="villas-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM villas ORDER BY id DESC LIMIT 6");
        $villas = $stmt->fetchAll();
        if ($villas):
          foreach ($villas as $villa):
        ?>
            <div class="villa-card">
              <?php if (!empty($villa['image'])): ?>
                <img src="/uploads/<?php echo htmlspecialchars($villa['image']); ?>" alt="<?php echo htmlspecialchars($villa['name_tr']); ?>">
              <?php else: ?>
                <img src="/public/default_villa.jpg" alt="Default Villa">
              <?php endif; ?>
              <div class="villa-info">
                <h3><?php echo htmlspecialchars($villa['name_tr']); ?></h3>
                <p class="villa-location"><?php echo htmlspecialchars($villa['location']); ?></p>
                <p class="villa-price">
                  <?php
                  if ($villa['price'] == 0) {
                    echo "Bilgi almak için iletişime geçin.";
                  } else {
                    echo number_format($villa['price'], 2) . " TL";
                  }
                  ?>
                </p>
                <a href="/public/villa_detail.php?id=<?php echo $villa['id']; ?>" class="btn"><?php echo __('details'); ?></a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Gösterilecek villa bulunamadı.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php
    $stmtOther = $pdo->query("SELECT * FROM other_villas ORDER BY id DESC");
    $otherVillas = $stmtOther->fetchAll();
    ?>
    <?php if (count($otherVillas) > 0): ?>
      <section class="other-villas">
        <h2 class="section-title"><?php echo __('othervillas'); ?></h2>
        <div class="villa-grid">
          <?php foreach ($otherVillas as $villa): ?>
            <div class="villa-card">
              <?php if (!empty($villa['image'])): ?>
                <img src="/uploads/<?php echo htmlspecialchars($villa['image']); ?>" alt="<?php echo htmlspecialchars($villa['name_tr']); ?>">
              <?php else: ?>
                <img src="/public/default-villa.jpg" alt="Default Villa">
              <?php endif; ?>
              <div class="villa-info">
                <h3><?php echo htmlspecialchars($villa['name_tr']); ?></h3>
                <p class="villa-location"><?php echo htmlspecialchars($villa['location']); ?></p>
                <p class="villa-price">
                  <?php
                  if ($villa['price'] == 0) {
                    echo "Bilgi almak için iletişime geçin.";
                  } else {
                    echo number_format($villa['price'], 2) . " TL";
                  }
                  ?>
                </p>
                <a href="/public/villa_detail.php?id=<?php echo $villa['id']; ?>&type=other" class="btn"><?php echo __('details'); ?></a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <?php
      $stmtPromo = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('promo_heading_tr', 'promo_heading_en', 'promo_heading_ar', 'promo_content_tr', 'promo_content_en', 'promo_content_ar')");
      $stmtPromo->execute();
      $promoSettings = $stmtPromo->fetchAll(PDO::FETCH_KEY_PAIR);

      $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
      $promoHeadingKey = 'promo_heading_' . $lang;
      $promoContentKey = 'promo_content_' . $lang;

      $promoHeading = isset($promoSettings[$promoHeadingKey]) ? $promoSettings[$promoHeadingKey] : '';
      $promoContent = isset($promoSettings[$promoContentKey]) ? $promoSettings[$promoContentKey] : '';
      ?>
      <?php if (!empty($promoHeading) || !empty($promoContent)): ?>
        <section class="promo-section">
          <?php if (!empty($promoHeading)): ?>
            <h2 class="section-title promo-heading"><?php echo $promoHeading; ?></h2>
          <?php endif; ?>
          <?php if (!empty($promoContent)): ?>
            <div class="promo-content">
              <?php echo $promoContent; ?>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>


    <?php endif; ?>

    <?php
    $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
    if (!in_array($lang, ['tr', 'en', 'ar'])) {
      $lang = 'tr';
    }
    $serviceNameField = 'service_name_' . $lang;
    $serviceDescField = 'description_' . $lang;
    $stmtServices = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $stmtServices->fetchAll();
    ?>
    <section class="services-section">
      <h2 class="section-title"><?php echo __('services') ?: "Hizmetlerimiz"; ?></h2>
      <div class="services-grid">
        <?php if (count($services) > 0): ?>
          <?php foreach ($services as $service): ?>
            <div class="service-item">
              <?php if (!empty($service['icon'])): ?>
                <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
              <?php endif; ?>
              <h3 class="service-name"><?php echo htmlspecialchars($service[$serviceNameField]); ?></h3>
              <p class="service-desc"><?php echo nl2br(htmlspecialchars($service[$serviceDescField])); ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center;">Henüz hizmet eklenmemiş.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php
    $stmtFaq = $pdo->query("SELECT * FROM faq ORDER BY id DESC");
    $faqs = $stmtFaq->fetchAll();
    $question_field = 'question_' . $lang;
    $answer_field = 'answer_' . $lang;
    ?>
    <section class="faq-container" style="max-width:100%; margin:50px auto; padding:20px; background:#fff; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
      <h1 class="faq-heading" style="text-align:center; font-size:2.5rem; color:#333; margin-bottom:20px; position:relative;">
        <?php echo __('faq_heading') ?: "Sıkça Sorulan Sorular"; ?>
      </h1>
      <div class="faq-list">
        <?php if (count($faqs) > 0): ?>
          <?php foreach ($faqs as $faq): ?>
            <div class="faq-item" style="margin-bottom:15px; padding:10px; border-bottom:1px solid #e0e0e0; cursor: pointer;">
              <div class="faq-question" style="font-weight:600; color:#333; font-size:1.2rem; position: relative; padding-right: 30px;">
                <?php echo htmlspecialchars($faq[$question_field]); ?>
                <span class="faq-toggle" style="position:absolute; right:0; top:50%; transform: translateY(-50%); font-family: 'Font Awesome 5 Free'; font-weight:900; font-size:1rem;">&#xf078;</span>
              </div>
              <div class="faq-answer" style="color:#555; line-height:1.5; display:none; margin-top:10px;">
                <?php echo nl2br(htmlspecialchars($faq[$answer_field])); ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center;"><?php echo __('nofaq'); ?></p>
        <?php endif; ?>
      </div>
    </section>

    <?php
    $stmtPlaces = $pdo->query("SELECT * FROM places ORDER BY id DESC");
    $places = $stmtPlaces->fetchAll();
    $name_field = 'name_' . $lang;
    $short_field = 'short_description_' . $lang;
    ?>
    <link rel="stylesheet" href="/public/css/places.css">
    <section class="places-container">
      <h1 class="places-heading">
        <?php echo __('places') ?: "Gezilecek Yerler"; ?>
      </h1>
      <div class="places-grid">
        <?php if (count($places) > 0): ?>
          <?php foreach ($places as $place): ?>
            <div class="place-card">
              <div class="place-thumbnail">
                <?php if (!empty($place['thumbnail'])): ?>
                  <img src="/uploads/<?php echo htmlspecialchars($place['thumbnail']); ?>" alt="<?php echo htmlspecialchars($place[$name_field]); ?>">
                <?php else: ?>
                  <img src="/public/images/default_place.jpg" alt="Default Yer">
                <?php endif; ?>
              </div>
              <div class="place-info">
                <h3 class="place-name"><?php echo htmlspecialchars($place[$name_field]); ?></h3>
                <p class="place-short"><?php echo nl2br(htmlspecialchars($place[$short_field])); ?></p>
              </div>
              <a href="/public/place_detail.php?id=<?php echo $place['id']; ?>" class="btn"><?php echo __('details'); ?></a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-places">Henüz gezilecek yer eklenmemiş.</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="why-choose-us">
      <h2 class="section-title"><?php echo __('whyus'); ?></h2>
      <div class="features-grid">
        <div class="feature">
          <i class="fa fa-star"></i>
          <h3><?php echo __('quality'); ?></h3>
          <p><?php echo __('quality_desc'); ?></p>
        </div>
        <div class="feature">
          <i class="fa fa-thumbs-up"></i>
          <h3><?php echo __('trust'); ?></h3>
          <p><?php echo __('trust_desc'); ?></p>
        </div>
        <div class="feature">
          <i class="fa fa-globe"></i>
          <h3><?php echo __('globalexp'); ?></h3>
          <p><?php echo __('globalexp_desc'); ?></p>
        </div>
      </div>
    </section>

    <section class="testimonials-section">
      <h2 class="section-title"><?php echo __('testimonials_title'); ?></h2>
      <div class="testimonial-slider">
        <div class="testimonial-slide active">
          <p class="testimonial-text"><?php echo __('testimonial_1_text'); ?></p>
          <span class="testimonial-author"><?php echo __('testimonial_1_author'); ?></span>
        </div>
        <div class="testimonial-slide">
          <p class="testimonial-text"><?php echo __('testimonial_2_text'); ?></p>
          <span class="testimonial-author"><?php echo __('testimonial_2_author'); ?></span>
        </div>
        <div class="testimonial-slide">
          <p class="testimonial-text"><?php echo __('testimonial_3_text'); ?></p>
          <span class="testimonial-author"><?php echo __('testimonial_3_author'); ?></span>
        </div>
        <div class="testimonial-slide">
          <p class="testimonial-text"><?php echo __('testimonial_4_text'); ?></p>
          <span class="testimonial-author"><?php echo __('testimonial_4_author'); ?></span>
        </div>
        <div class="testimonial-slide">
          <p class="testimonial-text"><?php echo __('testimonial_5_text'); ?></p>
          <span class="testimonial-author"><?php echo __('testimonial_5_author'); ?></span>
        </div>
      </div>
    </section>
  </div>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll(".hero-slider .slide");
    let currentSlide = 0;
    if (slides.length > 1) {
      setInterval(function() {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add("active");
      }, 5000);
    }
  });

  document.addEventListener("DOMContentLoaded", function() {
    const tSlides = document.querySelectorAll(".testimonial-slider .testimonial-slide");
    let currentTSlide = 0;
    if (tSlides.length > 1) {
      setInterval(function() {
        tSlides[currentTSlide].classList.remove("active");
        currentTSlide = (currentTSlide + 1) % tSlides.length;
        tSlides[currentTSlide].classList.add("active");
      }, 4000);
    }
  });
</script>

<?php
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
$stmt->execute();
$row = $stmt->fetch();
$whatsapp_number = $row ? $row['setting_value'] : '';
$clean_number = preg_replace('/\D/', '', $whatsapp_number);
?>
<div class="whatsapp-widget">
  <a href="https://wa.me/<?php echo $clean_number; ?>" target="_blank">
    <img src="/public/images/WhatsApp.png" alt="WhatsApp Chat">
  </a>
</div>

<?php
include_once '../includes/footer.php';
?>