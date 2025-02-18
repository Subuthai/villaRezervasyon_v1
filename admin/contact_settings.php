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

$keys = ['contact_heading', 'contact_description', 'contact_phone', 'contact_email', 'contact_address', 'contact_map'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($keys as $key) {
        $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?");
        if (!$stmt->execute([$key, $value, $value])) {
            $error = "Ayarlar güncellenirken bir hata oluştu: $key";
            break;
        }
    }
    if (empty($error)) {
        $success = "İletişim ayarları başarıyla güncellendi.";
    }
}

$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('contact_heading', 'contact_description', 'contact_phone', 'contact_email', 'contact_address', 'contact_map')");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="dashboard-container">
    <div class="form-container">
        <h2>İletişim Sayfası Ayarları</h2>
        <?php 
            if ($error) echo '<div class="alert error">' . $error . '</div>';
            if ($success) echo '<div class="alert success">' . $success . '</div>';
        ?>
        <form action="" method="POST">
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

            <button type="submit">Ayarları Güncelle</button>
        </form>
    </div>
</div>

<?php include_once 'admin_footer.php'; ?>
