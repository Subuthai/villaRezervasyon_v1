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

$langs = ['tr', 'en', 'ar'];
$privacy = [];

foreach ($langs as $lang) {
    $stmt = $pdo->prepare("SELECT content FROM privacy_policy WHERE language = ?");
    $stmt->execute([$lang]);
    $privacy[$lang] = $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($langs as $lang) {
        $content = isset($_POST['privacy_' . $lang]) ? $_POST['privacy_' . $lang] : '';
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM privacy_policy WHERE language = ?");
        $stmt->execute([$lang]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $stmtUpdate = $pdo->prepare("UPDATE privacy_policy SET content = ? WHERE language = ?");
            if (!$stmtUpdate->execute([$content, $lang])) {
                $error = "Güncelleme sırasında hata oluştu ($lang).";
                break;
            }
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO privacy_policy (language, content) VALUES (?, ?)");
            if (!$stmtInsert->execute([$lang, $content])) {
                $error = "Kayıt eklenirken hata oluştu ($lang).";
                break;
            }
        }
        $privacy[$lang] = $content;
    }
    if (empty($error)) {
        $success = "Gizlilik politikası başarıyla güncellendi.";
    }
}
?>

<link rel="stylesheet" href="/public/css/admin_privacy_policy.css">
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

<div class="admin-privacy-container">
  <h2>Gizlilik Politikası Ayarları</h2>
  <?php if ($error): ?>
    <div class="alert error"><?php echo $error; ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert success"><?php echo $success; ?></div>
  <?php endif; ?>
  <form action="" method="POST" class="privacy-form">
    <?php foreach ($langs as $lang): ?>
      <fieldset>
        <legend>Gizlilik Politikası (<?php echo strtoupper($lang); ?>)</legend>
        <textarea name="privacy_<?php echo $lang; ?>" id="privacy_<?php echo $lang; ?>" rows="10"><?php echo htmlspecialchars($privacy[$lang]); ?></textarea>
        <script>
          CKEDITOR.replace('privacy_<?php echo $lang; ?>');
        </script>
      </fieldset>
    <?php endforeach; ?>
    <button type="submit">Politikayı Güncelle</button>
  </form>
</div>

<?php include_once 'admin_footer.php'; ?>
