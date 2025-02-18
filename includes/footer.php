<?php
include_once '../config/db.php';

$stmtSocial = $pdo->prepare("SELECT setting_key, setting_value FROM settings 
    WHERE setting_key IN ('social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_airbnb')");
$stmtSocial->execute();
$socialSettings = $stmtSocial->fetchAll(PDO::FETCH_KEY_PAIR);

$stmtContact = $pdo->prepare("SELECT setting_key, setting_value FROM settings
    WHERE setting_key IN ('contact_phone', 'contact_email', 'contact_address')");
$stmtContact->execute();
$contactSettings = $stmtContact->fetchAll(PDO::FETCH_KEY_PAIR);

$contactPhone   = $contactSettings['contact_phone']   ?? '+90 555 555 55 55';
$contactEmail   = $contactSettings['contact_email']   ?? 'info@example.com';
$contactAddress = $contactSettings['contact_address'] ?? 'Örnek Mah. 123. Sokak No:4';
?>
<link rel="stylesheet" href="/public/css/footer.css">

<footer class="site-footer">
  <div class="footer-container">
    
    <div class="footer-column footer-logo">
      <a href="/public/index.php">
        <img src="/public/images/logo.png" alt="Yakut Villam">
      </a>
    </div>
    
    <div class="footer-column footer-contact">
      <h3 class="footer-title"><?php echo __('get_in_touch'); ?></h3>
      <ul>
        <li><strong><?php echo __('contact_phone'); ?>:</strong> <?php echo htmlspecialchars($contactPhone); ?></li>
        <li><strong><?php echo __('contact_email'); ?>:</strong> <?php echo htmlspecialchars($contactEmail); ?></li>
        <li><strong><?php echo __('address'); ?>:</strong> <?php echo htmlspecialchars($contactAddress); ?></li>
      </ul>
    </div>
    
    <div class="footer-column footer-links">
      <h3 class="footer-title"><?php echo __('pages'); ?></h3>
      <ul>
        <li><a href="/public/index.php"><?php echo __('home'); ?></a></li>
        <li><a href="/public/villas.php"><?php echo __('villas'); ?></a></li>
        <li><a href="/public/contact.php"><?php echo __('contact'); ?></a></li>
        <li><a href="/public/privacy_policy.php"><?php echo __('privacy_policy'); ?></a></li>
      </ul>
    </div>
    
    <div class="footer-column footer-social">
      <h3 class="footer-title"><?php echo __('social_media'); ?></h3>
      <div class="social-icons">
        <?php if (!empty($socialSettings['social_facebook'])): ?>
          <a href="<?php echo $socialSettings['social_facebook']; ?>" target="_blank" class="social-icon">
            <img src="/public/images/facebook.png" alt="Facebook">
          </a>
        <?php endif; ?>
        <?php if (!empty($socialSettings['social_twitter'])): ?>
          <a href="<?php echo $socialSettings['social_twitter']; ?>" target="_blank" class="social-icon">
            <img src="/public/images/twitter.png" alt="Twitter">
          </a>
        <?php endif; ?>
        <?php if (!empty($socialSettings['social_instagram'])): ?>
          <a href="<?php echo $socialSettings['social_instagram']; ?>" target="_blank" class="social-icon">
            <img src="/public/images/instagram.png" alt="Instagram">
          </a>
        <?php endif; ?>
        <?php if (!empty($socialSettings['social_linkedin'])): ?>
          <a href="<?php echo $socialSettings['social_linkedin']; ?>" target="_blank" class="social-icon">
            <img src="/public/images/linkedin.png" alt="LinkedIn">
          </a>
        <?php endif; ?>
        <?php if (!empty($socialSettings['social_airbnb'])): ?>
          <a href="<?php echo $socialSettings['social_airbnb']; ?>" target="_blank" class="social-icon">
            <img src="/public/images/airbnb.png" alt="Airbnb">
          </a>
        <?php endif; ?>
      </div>
    </div>
    
  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> Yakut Villam. Tüm hakları saklıdır.</p>
  </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function() {
  var faqItems = document.querySelectorAll(".faq-item");
  
  faqItems.forEach(function(item){
    item.addEventListener("click", function(){
      var answer = item.querySelector(".faq-answer");
      var toggleIcon = item.querySelector(".faq-toggle");
      
      if (answer.style.display === "block") {
        answer.style.display = "none";
        if (toggleIcon) toggleIcon.innerHTML = "&#xf078;";
      } else {
        answer.style.display = "block";
        if (toggleIcon) toggleIcon.innerHTML = "&#xf077;";
      }
    });
  });
});
</script>

</body>
</html>
