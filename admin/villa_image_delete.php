<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include_once '../config/db.php';

if (isset($_GET['image_id']) && isset($_GET['villa_id'])) {
    $image_id = (int) $_GET['image_id'];
    $villa_id = (int) $_GET['villa_id'];

    $stmt = $pdo->prepare("SELECT image FROM villa_images WHERE id = ? AND villa_id = ?");
    $stmt->execute([$image_id, $villa_id]);
    $image = $stmt->fetchColumn();

    if ($image) {
        $stmtDelete = $pdo->prepare("DELETE FROM villa_images WHERE id = ? AND villa_id = ?");
        if ($stmtDelete->execute([$image_id, $villa_id])) {
            $filePath = '../uploads/' . $image;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

header("Location: villa_duzenle.php?villa_id=" . $villa_id);
exit;
?>
