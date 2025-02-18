<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $file = $_FILES['image'];
    
    if ($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $thumbFolder = '../uploads/thumbnails/';
                if (!file_exists($thumbFolder)) {
                    mkdir($thumbFolder, 0755, true);
                }
                $thumbPath = $thumbFolder . $newFileName;
                
                try {
                    createThumbnail($uploadPath, $thumbPath, 400);
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Thumbnail oluşturulurken hata: ' . $e->getMessage()]);
                    exit;
                }
                
                $villa_id = isset($_POST['villa_id']) ? (int)$_POST['villa_id'] : 0;
                $villa_type = isset($_POST['villa_type']) ? $_POST['villa_type'] : 'normal';
                
                $stmt = $pdo->prepare("INSERT INTO villa_images (villa_id, image, thumbnail, villa_type) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$villa_id, $newFileName, $newFileName, $villa_type])) {
                    echo json_encode(['success' => true, 'filename' => $newFileName]);
                    exit;
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Veritabanı hatası']);
                    exit;
                }
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Dosya taşınırken hata oluştu']);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Geçersiz dosya uzantısı. Sadece JPG, JPEG, PNG, GIF kabul edilir.']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Dosya yükleme hatası']);
        exit;
    }
}

function createThumbnail($src, $dest, $thumbWidth = 400) {
    $info = getimagesize($src);
    $mime = $info['mime'];
    switch($mime) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            break;
        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagepng';
            break;
        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagegif';
            break;
        default:
            throw new Exception('Desteklenmeyen resim türü.');
    }
    $img = $image_create_func($src);
    $width = imagesx($img);
    $height = imagesy($img);
    $new_width = $thumbWidth;
    $new_height = floor($height * ($thumbWidth / $width));
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagecolortransparent($tmp_img, imagecolorallocatealpha($tmp_img, 0, 0, 0, 127));
        imagealphablending($tmp_img, false);
        imagesavealpha($tmp_img, true);
    }
    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    if ($mime == 'image/jpeg') {
        $image_save_func($tmp_img, $dest, 85);
    } else {
        $image_save_func($tmp_img, $dest);
    }
    imagedestroy($img);
    imagedestroy($tmp_img);
    return true;
}
?>
