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
    'whatsapp_number',
    'contact_heading',
    'contact_description',
    'contact_phone',
    'contact_email',
    'contact_address',
    'contact_map',
    'social_facebook',
    'social_twitter',
    'social_instagram',
    'social_linkedin',
    'social_airbnb',
    'site_logo',
    'hero_logo',
    'hero_text',
    'hero_contact_email',
    'hero_contact_phone',
    'hero_bottom_text'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES['hero_logo_file']) && $_FILES['hero_logo_file']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file = $_FILES['hero_logo_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = 'hero_logo_' . uniqid() . '.' . $ext;
            $uploadPath = '../public/images/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $stmtHeroLogo = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'hero_logo'");
                $stmtHeroLogo->execute();
                $oldHeroLogo = $stmtHeroLogo->fetchColumn();
                if ($oldHeroLogo && file_exists('../public/images/' . $oldHeroLogo)) {
                    unlink('../public/images/' . $oldHeroLogo);
                }
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('hero_logo', ?)
                    ON DUPLICATE KEY UPDATE setting_value = ?");
                if (!$stmt->execute([$newFileName, $newFileName])) {
                    $error = "Hero logo ayarı güncellenirken hata oluştu.";
                }
            } else {
                $error = "Hero logo dosyası yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    }
    
    if (isset($_FILES['site_logo_file']) && $_FILES['site_logo_file']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file = $_FILES['site_logo_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = 'logo_' . uniqid() . '.' . $ext;
            $uploadPath = '../public/images/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $stmtLogo = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_logo'");
                $stmtLogo->execute();
                $oldLogo = $stmtLogo->fetchColumn();
                if ($oldLogo && file_exists('../public/images/' . $oldLogo)) {
                    unlink('../public/images/' . $oldLogo);
                }
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('site_logo', ?)
                    ON DUPLICATE KEY UPDATE setting_value = ?");
                if (!$stmt->execute([$newFileName, $newFileName])) {
                    $error = "Site logo ayarı güncellenirken hata oluştu.";
                }
            } else {
                $error = "Site logo dosyası yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    }
    
    foreach ($keys as $key) {
        if ($key === 'site_logo' || $key === 'hero_logo') {
            continue;
        }
        if ($key === 'hero_text' || $key === 'hero_bottom_text') {
            $value = isset($_POST[$key]) ? $_POST[$key] : '';
        } else {
            $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        }
        if ($key === 'whatsapp_number' && empty($value)) {
            $error = "Lütfen geçerli bir WhatsApp numarası girin.";
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?");
        if (!$stmt->execute([$key, $value, $value])) {
            $error = "Ayarlar güncellenirken bir hata oluştu: " . $key;
            break;
        }
    }
    
    if (empty($error)) {
        $success = "Ayarlar başarıyla güncellendi.";
    }
}

$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings 
  WHERE setting_key IN (
    'whatsapp_number', 'contact_heading', 'contact_description', 'contact_phone', 'contact_email', 'contact_address', 'contact_map',
    'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_airbnb', 'site_logo',
    'hero_logo', 'hero_text', 'hero_contact_email', 'hero_contact_phone', 'hero_bottom_text'
  )");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<link rel="stylesheet" href="/public/css/settings.css">
<div class="settings-wrapper">
  <div class="settings-container">
    <h2>Ayarlar</h2>
    <?php if ($error): ?>
      <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data" class="settings-form">
      <fieldset>
        <legend>Logo Ayarları</legend>
        <label for="site_logo">Site Logosu (URL):</label>
        <input type="text" id="site_logo" name="site_logo" value="<?php echo isset($settings['site_logo']) ? htmlspecialchars($settings['site_logo']) : ''; ?>" <?php echo (isset($settings['site_logo']) && !empty($settings['site_logo'])) ? 'disabled' : ''; ?>>
        <label for="site_logo_file">Veya Site Logo Dosyası Yükle:</label>
        <input type="file" id="site_logo_file" name="site_logo_file" accept=".jpg,.jpeg,.png,.gif">
        <?php if (isset($settings['site_logo']) && !empty($settings['site_logo'])): ?>
          <div class="current-logo">
            <p>Mevcut Site Logosu:</p>
            <img src="/public/images/<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="Site Logosu" style="max-width:150px;">
          </div>
        <?php endif; ?>
        <hr>
        <label for="hero_logo_file">Özel Hero Logo Dosyası Yükle:</label>
        <input type="file" id="hero_logo_file" name="hero_logo_file" accept=".jpg,.jpeg,.png,.gif">
        <?php if (isset($settings['hero_logo']) && !empty($settings['hero_logo'])): ?>
          <div class="current-hero-logo">
            <p>Mevcut Hero Logosu:</p>
            <img src="/public/images/<?php echo htmlspecialchars($settings['hero_logo']); ?>" alt="Hero Logosu" style="max-width:150px;">
          </div>
        <?php endif; ?>
        <label for="hero_text">Hero Alanı Üst Metni:</label>
        <textarea id="hero_text" name="hero_text" rows="3"><?php echo isset($settings['hero_text']) ? htmlspecialchars($settings['hero_text']) : ''; ?></textarea>
      </fieldset>
      <fieldset>
        <legend>Hero Slider Alt Bilgileri</legend>
        <label for="hero_contact_email">Hero İletişim E‑Postası:</label>
        <input type="text" id="hero_contact_email" name="hero_contact_email" value="<?php echo isset($settings['hero_contact_email']) ? htmlspecialchars($settings['hero_contact_email']) : ''; ?>">
        <label for="hero_contact_phone">Hero İletişim Telefonu:</label>
        <input type="text" id="hero_contact_phone" name="hero_contact_phone" value="<?php echo isset($settings['hero_contact_phone']) ? htmlspecialchars($settings['hero_contact_phone']) : ''; ?>">
        <label for="hero_bottom_text">Hero Slider Sol Tarafı Alt Metni:</label>
        <textarea id="hero_bottom_text" name="hero_bottom_text" rows="3"><?php echo isset($settings['hero_bottom_text']) ? htmlspecialchars($settings['hero_bottom_text']) : ''; ?></textarea>
      </fieldset>
      <fieldset>
        <legend>WhatsApp Ayarları</legend>
        <label for="whatsapp_number">WhatsApp Numarası (Uluslararası Format, örn: +905XXXXXXXXX):</label>
        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo isset($settings['whatsapp_number']) ? htmlspecialchars($settings['whatsapp_number']) : ''; ?>" required>
      </fieldset>
      <fieldset>
        <legend>İletişim Sayfası Ayarları</legend>
        <label for="contact_heading">Başlık:</label>
        <input type="text" id="contact_heading" name="contact_heading" value="<?php echo isset($settings['contact_heading']) ? htmlspecialchars($settings['contact_heading']) : ''; ?>">
        <label for="contact_description">Açıklama:</label>
        <textarea id="contact_description" name="contact_description" rows="4"><?php echo isset($settings['contact_description']) ? htmlspecialchars($settings['contact_description']) : ''; ?></textarea>
        <label for="contact_phone">Telefon:</label>
        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo isset($settings['contact_phone']) ? htmlspecialchars($settings['contact_phone']) : ''; ?>">
        <label for="contact_email">E-posta:</label>
        <input type="text" id="contact_email" name="contact_email" value="<?php echo isset($settings['contact_email']) ? htmlspecialchars($settings['contact_email']) : ''; ?>">
        <label for="contact_address">Adres:</label>
        <input type="text" id="contact_address" name="contact_address" value="<?php echo isset($settings['contact_address']) ? htmlspecialchars($settings['contact_address']) : ''; ?>">
        <label for="contact_map">Harita Embed Kodu:</label>
        <textarea id="contact_map" name="contact_map" rows="4"><?php echo isset($settings['contact_map']) ? htmlspecialchars($settings['contact_map']) : ''; ?></textarea>
      </fieldset>
      <fieldset>
        <legend>Sosyal Medya Hesapları</legend>
        <label for="social_facebook">Facebook Linki:</label>
        <input type="text" id="social_facebook" name="social_facebook" value="<?php echo isset($settings['social_facebook']) ? htmlspecialchars($settings['social_facebook']) : ''; ?>">
        <label for="social_twitter">Twitter Linki:</label>
        <input type="text" id="social_twitter" name="social_twitter" value="<?php echo isset($settings['social_twitter']) ? htmlspecialchars($settings['social_twitter']) : ''; ?>">
        <label for="social_instagram">Instagram Linki:</label>
        <input type="text" id="social_instagram" name="social_instagram" value="<?php echo isset($settings['social_instagram']) ? htmlspecialchars($settings['social_instagram']) : ''; ?>">
        <label for="social_linkedin">LinkedIn Linki:</label>
        <input type="text" id="social_linkedin" name="social_linkedin" value="<?php echo isset($settings['social_linkedin']) ? htmlspecialchars($settings['social_linkedin']) : ''; ?>">
        <label for="social_airbnb">Airbnb Linki:</label>
        <input type="text" id="social_airbnb" name="social_airbnb" value="<?php echo isset($settings['social_airbnb']) ? htmlspecialchars($settings['social_airbnb']) : ''; ?>">
      </fieldset>
      <button type="submit">Ayarları Güncelle</button>
    </form>
  </div>
</div>
<?php include_once 'admin_footer.php'; ?>
