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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Servis ID belirtilmedi.</p>";
    include_once 'admin_footer.php';
    exit;
}

$service_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    echo "<p>Belirtilen servis bulunamadı.</p>";
    include_once 'admin_footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name_tr = trim($_POST['service_name_tr']);
    $service_name_en = trim($_POST['service_name_en']);
    $service_name_ar = trim($_POST['service_name_ar']);
    $description_tr  = trim($_POST['description_tr']);
    $description_en  = trim($_POST['description_en']);
    $description_ar  = trim($_POST['description_ar']);
    $icon            = trim($_POST['icon']);

    if (empty($service_name_tr) || empty($service_name_en) || empty($service_name_ar)) {
        $error = "Lütfen tüm dillerde servis adını girin.";
    }

    if (empty($error)) {
        $stmtUpdate = $pdo->prepare("UPDATE services SET service_name_tr = ?, service_name_en = ?, service_name_ar = ?, description_tr = ?, description_en = ?, description_ar = ?, icon = ? WHERE id = ?");
        if ($stmtUpdate->execute([$service_name_tr, $service_name_en, $service_name_ar, $description_tr, $description_en, $description_ar, $icon, $service_id])) {
            $success = "Servis başarıyla güncellendi.";
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch();
        } else {
            $error = "Güncelleme sırasında hata oluştu.";
        }
    }
}
?>

<link rel="stylesheet" href="/public/css/services_admin.css">

<div class="services-admin-container">
    <h2>Servis Düzenle</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="service-form">
        <label for="service_name_tr">Servis Adı (TR):</label>
        <input type="text" id="service_name_tr" name="service_name_tr" value="<?php echo htmlspecialchars($service['service_name_tr']); ?>" required>

        <label for="service_name_en">Service Name (EN):</label>
        <input type="text" id="service_name_en" name="service_name_en" value="<?php echo htmlspecialchars($service['service_name_en']); ?>" required>

        <label for="service_name_ar">اسم الخدمة (AR):</label>
        <input type="text" id="service_name_ar" name="service_name_ar" value="<?php echo htmlspecialchars($service['service_name_ar']); ?>" required>

        <label for="description_tr">Açıklama (TR):</label>
        <textarea id="description_tr" name="description_tr" rows="3" required><?php echo htmlspecialchars($service['description_tr']); ?></textarea>

        <label for="description_en">Description (EN):</label>
        <textarea id="description_en" name="description_en" rows="3" required><?php echo htmlspecialchars($service['description_en']); ?></textarea>

        <label for="description_ar">الوصف (AR):</label>
        <textarea id="description_ar" name="description_ar" rows="3" required><?php echo htmlspecialchars($service['description_ar']); ?></textarea>

        <label for="icon">İkon (Opsiyonel, örneğin: fas fa-wifi):</label>
        <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>">

        <button type="submit" class="btn">Güncelle</button>
    </form>
</div>

<?php include_once 'admin_footer.php'; ?>
