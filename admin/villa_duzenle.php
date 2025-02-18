<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';
include_once 'admin_header.php';

if (!isset($_GET['villa_id']) || empty($_GET['villa_id'])) {
    echo "<p>Villa ID belirtilmedi.</p>";
    include_once 'admin_footer.php';
    exit;
}
$villa_id = (int)$_GET['villa_id'];
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
    include_once 'admin_footer.php';
    exit;
}

$error = '';
$success = '';

$stmtServices = $pdo->query("SELECT * FROM services ORDER BY id ASC");
$allServices = $stmtServices->fetchAll();

$assignedServices = [];
if (!empty($villa['services'])) {
    $assignedServices = array_map('trim', explode(',', $villa['services']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_main_image']) && !isset($_POST['async_upload'])) {
    $name_tr         = trim($_POST['name_tr']);
    $name_en         = trim($_POST['name_en']);
    $name_ar         = trim($_POST['name_ar']);
    $description_tr  = trim($_POST['description_tr']);
    $description_en  = trim($_POST['description_en']);
    $description_ar  = trim($_POST['description_ar']);
    $location        = trim($_POST['location']);
    
    $servicesArr = isset($_POST['services']) ? $_POST['services'] : [];
    $servicesStr = implode(',', $servicesArr);

    if (empty($name_tr) || empty($name_en) || empty($name_ar)) {
        $error = "Lütfen tüm dillerde villa adını doldurun.";
    }
    
    if (empty($error)) {
        if ($type === 'other') {
            $updateStmt = $pdo->prepare("UPDATE other_villas SET name_tr = ?, name_en = ?, name_ar = ?, description_tr = ?, description_en = ?, description_ar = ?, location = ?, services = ? WHERE id = ?");
        } else {
            $updateStmt = $pdo->prepare("UPDATE villas SET name_tr = ?, name_en = ?, name_ar = ?, description_tr = ?, description_en = ?, description_ar = ?, location = ?, services = ? WHERE id = ?");
        }
        if ($updateStmt->execute([$name_tr, $name_en, $name_ar, $description_tr, $description_en, $description_ar, $location, $servicesStr, $villa_id])) {
            $success = "Villa bilgileri başarıyla güncellendi.";
            if ($type === 'other') {
                $stmt = $pdo->prepare("SELECT * FROM other_villas WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
            }
            $stmt->execute([$villa_id]);
            $villa = $stmt->fetch();
            $assignedServices = [];
            if (!empty($villa['services'])) {
                $assignedServices = array_map('trim', explode(',', $villa['services']));
            }
        } else {
            $error = "Güncelleme sırasında bir hata oluştu.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_main_image'])) {
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file = $_FILES['main_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                if (!empty($villa['image']) && file_exists('../uploads/' . $villa['image'])) {
                    unlink('../uploads/' . $villa['image']);
                }
                if ($type === 'other') {
                    $updateImgStmt = $pdo->prepare("UPDATE other_villas SET image = ? WHERE id = ?");
                } else {
                    $updateImgStmt = $pdo->prepare("UPDATE villas SET image = ? WHERE id = ?");
                }
                if ($updateImgStmt->execute([$newFileName, $villa_id])) {
                    $success = "Ana fotoğraf başarıyla güncellendi.";
                    if ($type === 'other') {
                        $stmt = $pdo->prepare("SELECT * FROM other_villas WHERE id = ?");
                    } else {
                        $stmt = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
                    }
                    $stmt->execute([$villa_id]);
                    $villa = $stmt->fetch();
                } else {
                    $error = "Ana fotoğraf veritabanı güncellemesinde hata oluştu.";
                }
            } else {
                $error = "Ana fotoğraf yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    } else {
        $error = "Lütfen bir ana fotoğraf seçin.";
    }
}

if ($type === 'other') {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND villa_type = 'other'");
} else {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND (villa_type = 'normal' OR villa_type IS NULL)");
}
$stmtImages->execute([$villa_id]);
$imagesList = $stmtImages->fetchAll();
?>

<link rel="stylesheet" href="/public/css/villa_duzenle.css">
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

<div class="dashboard-container">
    <div class="form-container">
        <h2>Villa Düzenle (ID: <?php echo $villa['id']; ?>)</h2>
        <?php 
            if (!empty($error)) { echo '<div class="error">' . $error . '</div>'; }
            if (!empty($success)) { echo '<div class="success">' . $success . '</div>'; }
        ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="villa-edit-form">
            <label for="name_tr">Villa Adı (TR):</label>
            <input type="text" id="name_tr" name="name_tr" value="<?php echo htmlspecialchars($villa['name_tr']); ?>" required>
            
            <label for="name_en">Villa Name (EN):</label>
            <input type="text" id="name_en" name="name_en" value="<?php echo htmlspecialchars($villa['name_en']); ?>" required>
            
            <label for="name_ar">Villa Name (AR):</label>
            <input type="text" id="name_ar" name="name_ar" value="<?php echo htmlspecialchars($villa['name_ar']); ?>" required>
            
            <label for="description_tr">Açıklama (TR):</label>
            <textarea id="description_tr" name="description_tr" rows="4"><?php echo htmlspecialchars($villa['description_tr']); ?></textarea>
            
            <label for="description_en">Description (EN):</label>
            <textarea id="description_en" name="description_en" rows="4"><?php echo htmlspecialchars($villa['description_en']); ?></textarea>
            
            <label for="description_ar">الوصف (AR):</label>
            <textarea id="description_ar" name="description_ar" rows="4"><?php echo htmlspecialchars($villa['description_ar']); ?></textarea>
            
            <label for="location">Konum:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($villa['location']); ?>">
            
            <label for="services">Hizmetler (servisleri seçin):</label>
            <div class="services-checkboxes">
                <?php foreach($allServices as $service): 
                    $checked = in_array($service['id'], $assignedServices) ? 'checked' : '';
                ?>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="<?php echo $service['id']; ?>" <?php echo $checked; ?>>
                        <label><?php echo htmlspecialchars($service['service_name_tr']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit">Bilgileri Güncelle</button>
        </form>
        
        <hr>
        <h3>Ana Fotoğrafı Güncelle</h3>
        <form action="" method="POST" enctype="multipart/form-data" class="main-image-form">
            <label for="main_image">Yeni Ana Fotoğraf Seçin:</label>
            <input type="file" id="main_image" name="main_image" accept=".jpg,.jpeg,.png,.gif">
            <button type="submit" name="update_main_image">Fotoğrafı Güncelle</button>
        </form>
        <?php if (!empty($villa['image'])): ?>
            <div class="current-image">
                <p>Mevcut Ana Fotoğraf:</p>
                <img src="../uploads/<?php echo htmlspecialchars($villa['image']); ?>" alt="Ana Fotoğraf">
            </div>
        <?php endif; ?>
        
        <hr>
        <h3>Yeni Fotoğraflar Yükle</h3>
        <form id="uploadForm" method="POST" enctype="multipart/form-data" class="upload-form">
            <input type="hidden" name="async_upload" value="1">
            <input type="hidden" name="villa_id" value="<?php echo $villa_id; ?>">
            <input type="hidden" name="villa_type" value="<?php echo $type; ?>">
            <label for="images">Fotoğrafları Seçin (birden fazla):</label>
            <input type="file" id="images" name="images[]" multiple accept=".jpg,.jpeg,.png,.gif">
            <button type="submit" id="uploadButton">Fotoğrafları Yükle</button>
        </form>
        <div id="uploadProgress"></div>
        
        <hr>
        <h3>Mevcut Fotoğraflar</h3>
        <?php if(count($imagesList) > 0): ?>
            <div class="image-gallery">
                <?php foreach($imagesList as $img): ?>
                    <div class="gallery-item">
                        <img src="../uploads/thumbnails/<?php echo htmlspecialchars($img['thumbnail']); ?>" 
                             alt="Villa Fotoğraf" 
                             loading="lazy"
                             onclick="openLightbox('../uploads/<?php echo htmlspecialchars($img['image']); ?>')">
                        <a href="villa_image_delete.php?image_id=<?php echo $img['id']; ?>&villa_id=<?php echo $villa_id; ?>&type=<?php echo $type; ?>" class="delete-btn" onclick="return confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')">Sil</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Henüz fotoğraf yüklenmemiş.</p>
        <?php endif; ?>
    </div>
</div>

<div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox(event)">&times;</span>
    <img class="lightbox-content" id="lightboxImage">
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    CKEDITOR.replace('description_tr');
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_ar');

    const uploadForm = document.getElementById("uploadForm");
    if(uploadForm){
        const fileInput = document.getElementById("images");
        const progressText = document.getElementById("uploadProgress");
        const uploadButton = document.getElementById("uploadButton");
        const villaId = <?php echo $villa_id; ?>;
        
        uploadForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const files = fileInput.files;
            const totalFiles = files.length;
            let uploadedCount = 0;
            
            if(totalFiles === 0) {
                alert("Lütfen dosya seçin.");
                return;
            }
            
            uploadButton.disabled = true;
            fileInput.disabled = true;
            
            progressText.textContent = "0 / " + totalFiles + " yüklendi...";
            
            function uploadNext(index) {
                if (index >= totalFiles) {
                    progressText.textContent = totalFiles + " / " + totalFiles + " yüklendi.";
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                    return;
                }
                
                let formData = new FormData();
                formData.append("image", files[index]);
                formData.append("villa_id", villaId);
                formData.append("async_upload", "1");
                formData.append("villa_type", "<?php echo $type; ?>");
                
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "upload_image.php", true);
                xhr.onload = function(){
                    if (xhr.status === 200) {
                        uploadedCount++;
                        progressText.textContent = uploadedCount + " / " + totalFiles + " yüklendi...";
                        uploadNext(index + 1);
                    } else {
                        progressText.textContent = "Hata: " + xhr.responseText;
                    }
                };
                xhr.onerror = function(){
                    progressText.textContent = "Yükleme sırasında hata oluştu.";
                };
                xhr.send(formData);
            }
            
            uploadNext(0);
        });
    }
    
    window.openLightbox = function(src) {
        const modal = document.getElementById("lightboxModal");
        const modalImg = document.getElementById("lightboxImage");
        modal.style.display = "block";
        modalImg.src = src;
    };
    
    window.closeLightbox = function(e) {
        if(e && e.target.className === "lightbox-close") {
            e.stopPropagation();
        }
        document.getElementById("lightboxModal").style.display = "none";
    };
    
    document.getElementById("lightboxModal").addEventListener("click", function(e) {
        if(e.target.id === "lightboxModal") {
            closeLightbox();
        }
    });
});
</script>

<?php include_once 'admin_footer.php'; ?>
