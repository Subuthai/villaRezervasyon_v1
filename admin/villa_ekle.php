<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';

$error = '';
$success = '';

$stmtServices = $pdo->query("SELECT * FROM services ORDER BY id ASC");
$allServices = $stmtServices->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $villa_type = isset($_POST['villa_type']) ? trim($_POST['villa_type']) : 'yakut';
    
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
        $error = "Lütfen tüm dillerde isim bilgisini doldurun.";
    }
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['image']['name'];
        $file_tmp  = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFileName;
            if (move_uploaded_file($file_tmp, $uploadPath)) {
                $image = $newFileName;
            } else {
                $error = "Resim yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    }
    
    if (empty($error)) {
        if ($villa_type == 'other') {
            $stmt = $pdo->prepare("INSERT INTO other_villas (name_tr, name_en, name_ar, description_tr, description_en, description_ar, location, image, services) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        } else {
            $stmt = $pdo->prepare("INSERT INTO villas (name_tr, name_en, name_ar, description_tr, description_en, description_ar, location, image, services) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        }
        if ($stmt->execute([$name_tr, $name_en, $name_ar, $description_tr, $description_en, $description_ar, $location, $image, $servicesStr])) {
            $success = "Villa başarıyla eklendi.";
            $name_tr = $name_en = $name_ar = $description_tr = $description_en = $description_ar = $location = "";
            $servicesArr = [];
        } else {
            $error = "Villa eklenirken hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Villa Ekle - Admin Paneli</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/css/villa_ekle.css">
  <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
</head>
<body>
  <?php include_once 'admin_header.php'; ?>
  <div class="form-container">
    <h2>Yeni Villa Ekle</h2>
    <?php 
      if (!empty($error)) echo '<div class="alert error">'.$error.'</div>';
      if (!empty($success)) echo '<div class="alert success">'.$success.'</div>';
    ?>
    <form action="" method="POST" enctype="multipart/form-data" class="villa-form">
      <div class="form-group">
        <label for="name_tr">Villa Adı (TR):</label>
        <input type="text" id="name_tr" name="name_tr" required>
      </div>
      <div class="form-group">
        <label for="name_en">Villa Name (EN):</label>
        <input type="text" id="name_en" name="name_en" required>
      </div>
      <div class="form-group">
        <label for="name_ar">Villa Name (AR):</label>
        <input type="text" id="name_ar" name="name_ar" required>
      </div>
      <div class="form-group">
        <label for="description_tr">Açıklama (TR):</label>
        <textarea id="description_tr" name="description_tr" rows="4"></textarea>
      </div>
      <div class="form-group">
        <label for="description_en">Description (EN):</label>
        <textarea id="description_en" name="description_en" rows="4"></textarea>
      </div>
      <div class="form-group">
        <label for="description_ar">الوصف (AR):</label>
        <textarea id="description_ar" name="description_ar" rows="4"></textarea>
      </div>
      <div class="form-group">
        <label for="villa_type">Villa Türü:</label>
        <select name="villa_type" id="villa_type" required>
          <option value="yakut">Yakut Villa</option>
          <option value="other">Diğer Villa</option>
        </select>
      </div>
      <div class="form-group">
        <label for="location">Konum:</label>
        <input type="text" id="location" name="location">
      </div>
      <div class="form-group">
        <label for="image">Villa Ana Görseli:</label>
        <input type="file" id="image" name="image">
      </div>
      <div class="form-group">
        <label for="services">Hizmetler (servisleri seçin):</label>
        <div class="services-checkboxes">
          <?php foreach($allServices as $service): ?>
            <div class="service-checkbox">
              <input type="checkbox" name="services[]" value="<?php echo $service['id']; ?>">
              <label><?php echo htmlspecialchars($service['service_name_tr']); ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button type="submit">Villa Ekle</button>
    </form>
  </div>
  <?php include_once 'admin_footer.php'; ?>
  <script>
    CKEDITOR.replace('description_tr');
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_ar');
  </script>
</body>
</html>
