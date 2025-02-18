<?php
include_once '../includes/header.php';
include_once '../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Yer ID belirtilmedi.</p>";
    include_once '../includes/footer.php';
    exit;
}

$place_id = (int)$_GET['id'];

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
if (!in_array($lang, ['tr', 'en', 'ar'])) {
    $lang = 'tr';
}
$name_field = 'name_' . $lang;
$short_field = 'short_description_' . $lang;
$full_field = 'full_description_' . $lang;

$stmt = $pdo->prepare("SELECT * FROM places WHERE id = ?");
$stmt->execute([$place_id]);
$place = $stmt->fetch();

if (!$place) {
    echo "<p>Belirtilen yer bulunamadı.</p>";
    include_once '../includes/footer.php';
    exit;
}
?>
<link rel="stylesheet" href="/public/css/place_detail.css">
<main class="place-detail-container">
    <div class="place-detail-content">
        <h2 class="place-title"><?php echo htmlspecialchars($place[$name_field]); ?></h2>
        <?php if (!empty($place['thumbnail'])): ?>
            <img src="/uploads/<?php echo htmlspecialchars($place['thumbnail']); ?>" alt="<?php echo htmlspecialchars($place[$name_field]); ?>" class="place-thumbnail">
        <?php endif; ?>
        <div class="place-short">
            <?php echo nl2br(htmlspecialchars($place[$short_field])); ?>
        </div>
        <div class="place-full">
            <?php echo $place[$full_field];?>
        </div>
        <a href="/public/places.php" class="btn back-btn"><?php echo __('all_places'); ?></a>
    </div>
</main>
<?php include_once '../includes/footer.php'; ?>
