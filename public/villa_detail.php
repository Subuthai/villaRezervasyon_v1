<?php
include_once '../includes/header.php';
include_once '../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Villa ID belirtilmedi.</p>";
    include_once '../includes/footer.php';
    exit;
}

$villa_id = (int) $_GET['id'];
$type = (isset($_GET['type']) && $_GET['type'] === 'other') ? 'other' : 'normal';

if ($type === 'other') {
    $stmt = $pdo->prepare("SELECT * FROM other_villas WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
}
$stmt->execute([$villa_id]);
$villa = $stmt->fetch();

if (!$villa) {
    echo "<p>Belirtilen villa bulunamadı.</p>";
    include_once '../includes/footer.php';
    exit;
}

if ($type === 'other') {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND villa_type = 'other'");
} else {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND (villa_type = 'normal' OR villa_type IS NULL)");
}
$stmtImages->execute([$villa_id]);
$images = $stmtImages->fetchAll();

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
if (!in_array($lang, ['tr', 'en', 'ar'])) {
    $lang = 'tr';
}
$description_column = 'description_' . $lang;
$description = isset($villa[$description_column]) && !empty($villa[$description_column])
               ? $villa[$description_column]
               : $villa['description_tr'];
?>

<link rel="stylesheet" href="/public/css/villa_detail.css">

<main class="villa-detail-container">
    <div class="villa-main-content">
        <div class="villa-info">
            <h2 class="villa-title"><?php echo htmlspecialchars($villa['name_tr']); ?></h2>
            <p class="villa-location"><strong><?php echo __('location'); ?>:</strong> <?php echo htmlspecialchars($villa['location']); ?></p>
            <div class="villa-description">
                <h3><?php echo __('description'); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
            </div>
            <a href="/public/villas.php" class="btn back-btn"><?php echo __('all_villas'); ?></a>
        </div>

        <div class="reservation-form-container">
            <h3><?php echo __('reservation'); ?></h3>
            <form action="/public/reserve.php" method="POST" class="reservation-form">
                <input type="hidden" name="villa_id" value="<?php echo $villa_id; ?>">
                <div class="form-group">
                    <label for="adult"><?php echo __('adult'); ?></label>
                    <input type="number" id="adult" name="adult" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="child"><?php echo __('child'); ?></label>
                    <input type="number" id="child" name="child" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="check_in"><?php echo __('check_in'); ?></label>
                    <input type="date" id="check_in" name="check_in" required>
                </div>
                <div class="form-group">
                    <label for="check_out"><?php echo __('check_out'); ?></label>
                    <input type="date" id="check_out" name="check_out" required>
                </div>
                <div class="form-group">
                    <label for="name"><?php echo __('name'); ?></label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="surname"><?php echo __('surname'); ?></label>
                    <input type="text" id="surname" name="surname" required>
                </div>
                <div class="form-group">
                    <label for="phone"><?php echo __('phone'); ?></label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="email"><?php echo __('email'); ?></label>
                    <input type="email" id="email" name="email">
                </div>
                <button type="submit" class="btn submit-btn"><?php echo __('reservation'); ?></button>
            </form>
        </div>
    </div>

    <?php if (count($images) > 0): ?>
    <div class="villa-gallery-container">
        <h3><?php echo __('photos'); ?></h3>
        <div class="villa-gallery">
            <?php foreach ($images as $img): ?>
                <div class="gallery-item">
                    <img src="/uploads/thumbnails/<?php echo htmlspecialchars($img['thumbnail']); ?>" 
                         alt="Villa Fotoğraf" 
                         loading="lazy"
                         onclick="openLightbox('/uploads/<?php echo htmlspecialchars($img['image']); ?>')">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $servicesStr = $villa['services'];
    $serviceIds = [];
    if (!empty($servicesStr)) {
        $serviceIds = array_map('trim', explode(',', $servicesStr));
    }
    if (count($serviceIds) > 0):
        $service_name_field = 'service_name_' . $lang;
        $placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
        $stmtServices = $pdo->prepare("SELECT * FROM services WHERE id IN ($placeholders) ORDER BY id ASC");
        $stmtServices->execute($serviceIds);
        $services = $stmtServices->fetchAll();
    ?>
    <div class="villa-services">
        <h3><?php echo __('services'); ?></h3>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-item">
                    <?php if (!empty($service['icon'])): ?>
                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                    <?php endif; ?>
                    <span class="service-name"><?php echo htmlspecialchars($service[$service_name_field]); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</main>

<div id="lightboxModal" class="lightbox-modal">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightboxImage">
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    window.openLightbox = function(src) {
        var modal = document.getElementById("lightboxModal");
        var modalImg = document.getElementById("lightboxImage");
        modal.classList.add("active");
        modalImg.src = src;
    };

    function closeLightbox() {
        var modal = document.getElementById("lightboxModal");
        modal.classList.remove("active");
    }

    var closeBtn = document.querySelector(".lightbox-close");
    if (closeBtn) {
        closeBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            closeLightbox();
        });
    }

    var modal = document.getElementById("lightboxModal");
    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            closeLightbox();
        }
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>
