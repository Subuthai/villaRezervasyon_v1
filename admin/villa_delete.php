<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include_once '../config/db.php';

if (!isset($_GET['villa_id']) || empty($_GET['villa_id'])) {
    echo "Villa ID belirtilmedi.";
    exit;
}

$villa_id = (int) $_GET['villa_id'];

$type = (isset($_GET['type']) && $_GET['type'] === 'other') ? 'other' : 'normal';

if ($type === 'other') {
    $stmt = $pdo->prepare("SELECT * FROM other_villas WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
}
$stmt->execute([$villa_id]);
$villa = $stmt->fetch();

if (!$villa) {
    echo "Belirtilen villa bulunamadı.";
    exit;
}

if (!empty($villa['image']) && file_exists('../uploads/' . $villa['image'])) {
    unlink('../uploads/' . $villa['image']);
}

if ($type === 'other') {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND villa_type = 'other'");
} else {
    $stmtImages = $pdo->prepare("SELECT * FROM villa_images WHERE villa_id = ? AND (villa_type = 'normal' OR villa_type IS NULL)");
}
$stmtImages->execute([$villa_id]);
$images = $stmtImages->fetchAll();

if ($images) {
    foreach ($images as $img) {
        if (!empty($img['image']) && file_exists('../uploads/' . $img['image'])) {
            unlink('../uploads/' . $img['image']);
        }
        if (!empty($img['thumbnail']) && file_exists('../uploads/thumbnails/' . $img['thumbnail'])) {
            unlink('../uploads/thumbnails/' . $img['thumbnail']);
        }
    }
}

if ($type === 'other') {
    $stmtDeleteImages = $pdo->prepare("DELETE FROM villa_images WHERE villa_id = ? AND villa_type = 'other'");
} else {
    $stmtDeleteImages = $pdo->prepare("DELETE FROM villa_images WHERE villa_id = ? AND (villa_type = 'normal' OR villa_type IS NULL)");
}
$stmtDeleteImages->execute([$villa_id]);

if ($type === 'other') {
    $stmtDeleteVilla = $pdo->prepare("DELETE FROM other_villas WHERE id = ?");
} else {
    $stmtDeleteVilla = $pdo->prepare("DELETE FROM villas WHERE id = ?");
}
$stmtDeleteVilla->execute([$villa_id]);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Villa Siliniyor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .modal {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="modal">
        <h2>Villa Siliniyor...</h2>
        <p>Lütfen bekleyin.</p>
    </div>
    <script>
        setTimeout(function(){
            window.location.href = "villa_listele.php";
        }, 1500);
    </script>
</body>
</html>
