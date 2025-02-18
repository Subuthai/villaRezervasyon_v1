<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
if (!in_array($lang, ['tr', 'en', 'ar'])) {
    $lang = 'tr';
}

$stmt = $pdo->prepare("SELECT content FROM privacy_policy WHERE language = ?");
$stmt->execute([$lang]);
$privacyContent = $stmt->fetchColumn();
if (!$privacyContent) {
    $privacyContent = "Gizlilik politikası henüz eklenmemiş.";
}
?>
<link rel="stylesheet" href="/public/css/privacy_policy.css">

<main class="privacy-policy-container">
  <h1><?php echo __('privacy_policy'); ?></h1>
  <div class="privacy-content">
    <?php echo $privacyContent; ?>
  </div>
</main>

<?php include_once '../includes/footer.php'; ?>
