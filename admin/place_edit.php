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
$isEdit = false;
$id = 0;

$name_tr = $name_en = $name_ar = "";
$short_description_tr = $short_description_en = $short_description_ar = "";
$full_description_tr = $full_description_en = $full_description_ar = "";
$thumbnail = '';

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM places WHERE id = ?");
    $stmt->execute([$id]);
    $place = $stmt->fetch();
    if (!$place) {
        echo "<p>Belirtilen yer bulunamadı.</p>";
        include_once 'admin_footer.php';
        exit;
    }
    $name_tr = $place['name_tr'];
    $name_en = $place['name_en'];
    $name_ar = $place['name_ar'];
    $short_description_tr = $place['short_description_tr'];
    $short_description_en = $place['short_description_en'];
    $short_description_ar = $place['short_description_ar'];
    $full_description_tr = $place['full_description_tr'];
    $full_description_en = $place['full_description_en'];
    $full_description_ar = $place['full_description_ar'];
    $thumbnail = $place['thumbnail'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name_tr = trim($_POST['name_tr']);
    $name_en = trim($_POST['name_en']);
    $name_ar = trim($_POST['name_ar']);
    $short_description_tr = trim($_POST['short_description_tr']);
    $short_description_en = trim($_POST['short_description_en']);
    $short_description_ar = trim($_POST['short_description_ar']);
    $full_description_tr = $_POST['full_description_tr'];
    $full_description_en = $_POST['full_description_en'];
    $full_description_ar = $_POST['full_description_ar'];
    $newThumbnail = $thumbnail;
    
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file = $_FILES['thumbnail'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                if (!empty($thumbnail) && file_exists('../uploads/' . $thumbnail)) {
                    unlink('../uploads/' . $thumbnail);
                }
                $newThumbnail = $newFileName;
            } else {
                $error = "Thumbnail yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    }
    
    if (empty($name_tr) || empty($name_en) || empty($name_ar) ||
        empty($short_description_tr) || empty($short_description_en) || empty($short_description_ar) ||
        empty($full_description_tr) || empty($full_description_en) || empty($full_description_ar)) {
        $error = "Lütfen tüm alanları doldurun.";
    }
    
    if (empty($error)) {
        if ($isEdit) {
            $stmtUpdate = $pdo->prepare("UPDATE places SET name_tr = ?, name_en = ?, name_ar = ?, short_description_tr = ?, short_description_en = ?, short_description_ar = ?, full_description_tr = ?, full_description_en = ?, full_description_ar = ?, thumbnail = ? WHERE id = ?");
            if ($stmtUpdate->execute([$name_tr, $name_en, $name_ar, $short_description_tr, $short_description_en, $short_description_ar, $full_description_tr, $full_description_en, $full_description_ar, $newThumbnail, $id])) {
                $success = "Yer başarıyla güncellendi.";
            } else {
                $error = "Güncelleme sırasında hata oluştu.";
            }
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO places (name_tr, name_en, name_ar, short_description_tr, short_description_en, short_description_ar, full_description_tr, full_description_en, full_description_ar, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmtInsert->execute([$name_tr, $name_en, $name_ar, $short_description_tr, $short_description_en, $short_description_ar, $full_description_tr, $full_description_en, $full_description_ar, $newThumbnail])) {
                $success = "Yer başarıyla eklendi.";
                $name_tr = $name_en = $name_ar = "";
                $short_description_tr = $short_description_en = $short_description_ar = "";
                $full_description_tr = $full_description_en = $full_description_ar = "";
                $newThumbnail = '';
            } else {
                $error = "Eklenirken hata oluştu.";
            }
        }
    }
}
?>

<link rel="stylesheet" href="/public/css/places_admin.css">
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

<div class="places-admin-container">
    <h2><?php echo $isEdit ? "Yer Düzenle" : "Yeni Yer Ekle"; ?></h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="place-form">
        <label for="name_tr">Mekan Adı (TR):</label>
        <input type="text" id="name_tr" name="name_tr" value="<?php echo htmlspecialchars($name_tr); ?>" required>

        <label for="name_en">Mekan Adı (EN):</label>
        <input type="text" id="name_en" name="name_en" value="<?php echo htmlspecialchars($name_en); ?>" required>

        <label for="name_ar">Mekan Adı (AR):</label>
        <input type="text" id="name_ar" name="name_ar" value="<?php echo htmlspecialchars($name_ar); ?>" required>

        <label for="short_description_tr">Kısa Açıklama (TR):</label>
        <textarea id="short_description_tr" name="short_description_tr" rows="3" required><?php echo htmlspecialchars($short_description_tr); ?></textarea>

        <label for="short_description_en">Short Description (EN):</label>
        <textarea id="short_description_en" name="short_description_en" rows="3" required><?php echo htmlspecialchars($short_description_en); ?></textarea>

        <label for="short_description_ar">وصف قصير (AR):</label>
        <textarea id="short_description_ar" name="short_description_ar" rows="3" required><?php echo htmlspecialchars($short_description_ar); ?></textarea>

        <label for="full_description_tr">Detaylı Açıklama (TR):</label>
        <textarea id="full_description_tr" name="full_description_tr" rows="6" required><?php echo htmlspecialchars($full_description_tr); ?></textarea>
        <script>
            CKEDITOR.replace('full_description_tr');
        </script>

        <label for="full_description_en">Detailed Description (EN):</label>
        <textarea id="full_description_en" name="full_description_en" rows="6" required><?php echo htmlspecialchars($full_description_en); ?></textarea>
        <script>
            CKEDITOR.replace('full_description_en');
        </script>

        <label for="full_description_ar">التفاصيل (AR):</label>
        <textarea id="full_description_ar" name="full_description_ar" rows="6" required><?php echo htmlspecialchars($full_description_ar); ?></textarea>
        <script>
            CKEDITOR.replace('full_description_ar');
        </script>

        <label for="thumbnail">Thumbnail Resim:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png,.gif">
        <?php if (!empty($thumbnail)): ?>
            <div class="current-thumbnail">
                <p>Mevcut Thumbnail:</p>
                <img src="../uploads/<?php echo htmlspecialchars($thumbnail); ?>" alt="Mevcut Thumbnail" style="max-width:200px;">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn"><?php echo $isEdit ? "Güncelle" : "Ekle"; ?></button>
    </form>
</div>

<?php include_once 'admin_footer.php'; ?>
