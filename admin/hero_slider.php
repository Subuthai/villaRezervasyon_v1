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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_hero'])) {
    $caption = trim($_POST['caption']);
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file = $_FILES['hero_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $stmt = $pdo->prepare("INSERT INTO hero_images (image, caption) VALUES (?, ?)");
                if ($stmt->execute([$newFileName, $caption])) {
                    $success = "Yeni hero görseli başarıyla eklendi.";
                } else {
                    $error = "Veritabanı hatası: Görsel eklenemedi.";
                }
            } else {
                $error = "Görsel yüklenirken hata oluştu.";
            }
        } else {
            $error = "Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.";
        }
    } else {
        $error = "Lütfen bir görsel seçin.";
    }
}

$stmtHero = $pdo->query("SELECT * FROM hero_images ORDER BY id ASC");
$heroImages = $stmtHero->fetchAll();
?>

<div class="dashboard-container">
  <div class="form-container">
    <h2>Hero Slider Yönetimi</h2>
    <?php 
      if (!empty($error)) { echo '<div class="error">'.$error.'</div>'; }
      if (!empty($success)) { echo '<div class="success">'.$success.'</div>'; }
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
      <label for="hero_image">Yeni Hero Görseli Seçin:</label>
      <input type="file" id="hero_image" name="hero_image" accept=".jpg,.jpeg,.png,.gif" required>
      <label for="caption">Açıklama (İsteğe Bağlı):</label>
      <input type="text" id="caption" name="caption" placeholder="Görsel açıklaması">
      <button type="submit" name="add_hero">Görsel Ekle</button>
    </form>
    
    <hr>
    <h3>Mevcut Hero Görselleri</h3>
    <?php if($heroImages && count($heroImages) > 0): ?>
      <div class="hero-gallery">
        <?php foreach($heroImages as $hero): ?>
          <div class="hero-item">
            <img src="../uploads/<?php echo htmlspecialchars($hero['image']); ?>" alt="<?php echo htmlspecialchars($hero['caption']); ?>">
            <?php if(!empty($hero['caption'])): ?>
              <p class="hero-caption"><?php echo htmlspecialchars($hero['caption']); ?></p>
            <?php endif; ?>
            <a href="hero_delete.php?hero_id=<?php echo $hero['id']; ?>" class="delete-btn" onclick="return confirm('Bu görseli silmek istediğinize emin misiniz?')">Sil</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Henüz hero görseli eklenmemiş.</p>
    <?php endif; ?>
  </div>
</div>

<style>
  .dashboard-container {
    max-width: 900px;
    margin: 20px auto;
    padding: 0 20px;
  }
  .form-container {
    background: #fff;
    padding: 20px 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .form-container h2 {
    text-align: center;
    color: #4A4A4A;
    margin-bottom: 20px;
  }
  .form-container label {
    display: block;
    margin-top: 1rem;
    font-weight: 600;
    color: #333;
  }
  .form-container input[type="file"],
  .form-container input[type="text"] {
    width: 100%;
    padding: 0.75rem;
    margin-top: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
  }
  .form-container button {
    width: 100%;
    padding: 0.75rem;
    margin-top: 1.5rem;
    background: #FFD700;
    border: none;
    border-radius: 4px;
    font-size: 1.1rem;
    font-weight: bold;
    color: #4A4A4A;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  .form-container button:hover {
    background: #e6c200;
  }
  .error, .success {
    text-align: center;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border-radius: 4px;
  }
  .error { background: #ffcccc; color: #a00; }
  .success { background: #ccffcc; color: #070; }
  .hero-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }
  .hero-item {
    background: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }
  .hero-item:hover {
    transform: translateY(-5px);
  }
  .hero-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }
  .hero-caption {
    padding: 10px;
    font-size: 1rem;
    color: #555;
    margin: 0;
  }
  .delete-btn {
    display: block;
    background: #ff4d4d;
    color: #fff;
    text-decoration: none;
    padding: 8px;
    border-radius: 0 0 8px 8px;
    transition: background 0.3s ease;
  }
  .delete-btn:hover {
    background: #ff1a1a;
  }
  @media (max-width: 600px) {
    .hero-item img {
      height: 160px;
    }
  }
</style>

<?php
include_once 'admin_footer.php';
?>
