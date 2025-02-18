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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name_tr = trim($_POST['service_name_tr']);
    $service_name_en = trim($_POST['service_name_en']);
    $service_name_ar = trim($_POST['service_name_ar']);
    $description_tr = trim($_POST['description_tr']);
    $description_en = trim($_POST['description_en']);
    $description_ar = trim($_POST['description_ar']);
    $icon = trim($_POST['icon']);

    if (empty($service_name_tr) || empty($service_name_en) || empty($service_name_ar)) {
        $error = "Lütfen tüm dillerde servis adını girin.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO services (service_name_tr, service_name_en, service_name_ar, description_tr, description_en, description_ar, icon) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$service_name_tr, $service_name_en, $service_name_ar, $description_tr, $description_en, $description_ar, $icon])) {
            $success = "Servis başarıyla eklendi.";
        } else {
            $error = "Servis eklenirken bir hata oluştu.";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM services ORDER BY id DESC");
$services = $stmt->fetchAll();
?>

<link rel="stylesheet" href="/public/css/services_admin.css">

<div class="services-admin-container">
    <h2>Servis Yönetimi</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="service-form">
        <h3>Yeni Servis Ekle</h3>
        <label for="service_name_tr">Servis Adı (TR):</label>
        <input type="text" id="service_name_tr" name="service_name_tr" required>

        <label for="service_name_en">Service Name (EN):</label>
        <input type="text" id="service_name_en" name="service_name_en" required>

        <label for="service_name_ar">اسم الخدمة (AR):</label>
        <input type="text" id="service_name_ar" name="service_name_ar" required>

        <label for="description_tr">Açıklama (TR):</label>
        <textarea id="description_tr" name="description_tr" rows="3"></textarea>

        <label for="description_en">Description (EN):</label>
        <textarea id="description_en" name="description_en" rows="3"></textarea>

        <label for="description_ar">الوصف (AR):</label>
        <textarea id="description_ar" name="description_ar" rows="3"></textarea>

        <label for="icon">İkon (Opsiyonel, örneğin: fas fa-wifi):</label>
        <input type="text" id="icon" name="icon">

        <button type="submit" name="add_service" class="btn">Servis Ekle</button>
    </form>

    <div class="services-list">
        <h3>Mevcut Servisler</h3>
        <?php if(count($services) > 0): ?>
            <?php foreach($services as $service): ?>
                <div class="service-item">
                    <div class="service-header">
                        <h4><?php echo htmlspecialchars($service['service_name_tr']); ?></h4>
                        <?php if(!empty($service['icon'])): ?>
                            <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="service-description">
                        <?php echo nl2br(htmlspecialchars($service['description_tr'])); ?>
                    </div>
                    <div class="service-actions">
                        <a href="service_edit.php?id=<?php echo $service['id']; ?>" class="btn edit">Düzenle</a>
                        <a href="service_delete.php?id=<?php echo $service['id']; ?>" class="btn delete" onclick="return confirm('Bu servisi silmek istediğinize emin misiniz?')">Sil</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz servis eklenmemiş.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'admin_footer.php'; ?>
