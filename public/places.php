<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
if (!in_array($lang, ['tr', 'en', 'ar'])) {
    $lang = 'tr';
}
$name_field = 'name_' . $lang;
$short_field = 'short_description_' . $lang;

$stmt = $pdo->query("SELECT * FROM places ORDER BY id DESC");
$places = $stmt->fetchAll();
?>
<link rel="stylesheet" href="/public/css/places.css">
<main class="places-container">
    <h2 class="section-title"><?php echo __('places') ?></h2>
    <div class="places-grid">
        <?php if (count($places) > 0): ?>
            <?php foreach ($places as $place): ?>
                <div class="place-card">
                    <?php if (!empty($place['thumbnail'])): ?>
                        <img src="/uploads/<?php echo htmlspecialchars($place['thumbnail']); ?>" alt="<?php echo htmlspecialchars($place[$name_field]); ?>">
                    <?php else: ?>
                        <img src="/public/images/default_place.jpg" alt="Default Yer">
                    <?php endif; ?>
                    <div class="place-info">
                        <h3><?php echo htmlspecialchars($place[$name_field]); ?></h3>
                        <p class="place-short"><?php echo htmlspecialchars($place[$short_field]); ?></p>
                    </div>
                    <a href="place_detail.php?id=<?php echo $place['id']; ?>" class="btn"><?php echo __('details'); ?></a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz gezilecek yer eklenmemiş.</p>
        <?php endif; ?>
    </div>
</main>
<?php include_once '../includes/footer.php'; ?>
