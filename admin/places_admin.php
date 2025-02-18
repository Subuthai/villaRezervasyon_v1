<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';
include_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM places ORDER BY id DESC");
$places = $stmt->fetchAll();
?>
<link rel="stylesheet" href="/public/css/places_admin.css">

<div class="places-admin-container">
    <h2>Gezilecek Yerler Yönetimi</h2>
    <div class="actions">
        <a href="place_edit.php" class="btn">Yeni Yer Ekle</a>
    </div>
    <?php if(count($places) > 0): ?>
        <div class="places-list">
            <?php foreach($places as $place): ?>
                <div class="place-item">
                    <div class="place-thumbnail">
                        <?php if(!empty($place['thumbnail'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($place['thumbnail']); ?>" alt="<?php echo htmlspecialchars($place['name_tr']); ?>">
                        <?php else: ?>
                            <img src="/public/images/default_place.jpg" alt="Default Thumbnail">
                        <?php endif; ?>
                    </div>
                    <div class="place-info">
                        <h3><?php echo htmlspecialchars($place['name_tr']); ?></h3>
                        <p><?php echo htmlspecialchars($place['short_description_tr']); ?></p>
                        <div class="place-actions">
                            <a href="place_edit.php?id=<?php echo $place['id']; ?>" class="btn edit">Düzenle</a>
                            <a href="place_delete.php?id=<?php echo $place['id']; ?>" class="btn delete" onclick="return confirm('Bu yeri silmek istediğinize emin misiniz?')">Sil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Henüz gezilecek yer eklenmemiş.</p>
    <?php endif; ?>
</div>

<?php include_once 'admin_footer.php'; ?>
