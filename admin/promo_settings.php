<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';
include_once 'admin_header.php';

$error = '';
$success = '';

$keys = [
    'promo_heading_tr',
    'promo_heading_en',
    'promo_heading_ar',
    'promo_content_tr',
    'promo_content_en',
    'promo_content_ar'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($keys as $key) {
        $value = isset($_POST[$key]) ? $_POST[$key] : '';
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?");
        if (!$stmt->execute([$key, $value, $value])) {
            $error = "Ayarlar güncellenirken bir hata oluştu: " . $key;
            break;
        }
    }
    if (empty($error)) {
        $success = "Promo ayarları başarıyla güncellendi.";
    }
}

$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings 
  WHERE setting_key IN (
    'promo_heading_tr', 'promo_heading_en', 'promo_heading_ar',
    'promo_content_tr', 'promo_content_en', 'promo_content_ar'
  )");
$stmt->execute();
$promoSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<link rel="stylesheet" href="/public/css/settings.css">
<div class="settings-wrapper">
  <div class="settings-container">
    <h2>Promo Ayarları</h2>
    <?php if ($error): ?>
      <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="" method="POST" class="settings-form">
      <fieldset>
        <legend>Promo Başlıkları</legend>
        <label for="promo_heading_tr">Promo Başlığı (TR):</label>
        <input type="text" id="promo_heading_tr" name="promo_heading_tr" value="<?php echo isset($promoSettings['promo_heading_tr']) ? htmlspecialchars($promoSettings['promo_heading_tr']) : ''; ?>">
        
        <label for="promo_heading_en">Promo Heading (EN):</label>
        <input type="text" id="promo_heading_en" name="promo_heading_en" value="<?php echo isset($promoSettings['promo_heading_en']) ? htmlspecialchars($promoSettings['promo_heading_en']) : ''; ?>">
        
        <label for="promo_heading_ar">Promo Heading (AR):</label>
        <input type="text" id="promo_heading_ar" name="promo_heading_ar" value="<?php echo isset($promoSettings['promo_heading_ar']) ? htmlspecialchars($promoSettings['promo_heading_ar']) : ''; ?>">
      </fieldset>
      <fieldset>
        <legend>Promo İçerikleri</legend>
        <label for="promo_content_tr">Promo İçeriği (TR):</label>
        <textarea id="promo_content_tr" name="promo_content_tr" rows="6"><?php echo isset($promoSettings['promo_content_tr']) ? $promoSettings['promo_content_tr'] : ''; ?></textarea>
        
        <label for="promo_content_en">Promo Content (EN):</label>
        <textarea id="promo_content_en" name="promo_content_en" rows="6"><?php echo isset($promoSettings['promo_content_en']) ? $promoSettings['promo_content_en'] : ''; ?></textarea>
        
        <label for="promo_content_ar">Promo Content (AR):</label>
        <textarea id="promo_content_ar" name="promo_content_ar" rows="6"><?php echo isset($promoSettings['promo_content_ar']) ? $promoSettings['promo_content_ar'] : ''; ?></textarea>
      </fieldset>
      <button type="submit">Ayarları Güncelle</button>
    </form>
  </div>
</div>
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
  CKEDITOR.replace('promo_content_tr');
  CKEDITOR.replace('promo_content_en');
  CKEDITOR.replace('promo_content_ar');
</script>
<?php include_once 'admin_footer.php'; ?>
