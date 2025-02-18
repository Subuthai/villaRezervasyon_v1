<?php
session_start();
include_once '../config/db.php';
include_once '../includes/language.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $villa_id   = isset($_POST['villa_id']) ? (int)$_POST['villa_id'] : 0;
    $adult      = isset($_POST['adult']) ? (int)$_POST['adult'] : 0;
    $child      = isset($_POST['child']) ? (int)$_POST['child'] : 0;
    $check_in   = isset($_POST['check_in']) ? $_POST['check_in'] : '';
    $check_out  = isset($_POST['check_out']) ? $_POST['check_out'] : '';
    $name       = isset($_POST['name']) ? trim($_POST['name']) : '';
    $surname    = isset($_POST['surname']) ? trim($_POST['surname']) : '';
    $phone      = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email      = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($villa_id <= 0 || empty($name) || empty($surname) || empty($phone) || empty($check_in) || empty($check_out)) {
        echo "Lütfen tüm zorunlu alanları doldurun.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO reservations (villa_id, adult, child, check_in, check_out, name, surname, phone, email, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt->execute([$villa_id, $adult, $child, $check_in, $check_out, $name, $surname, $phone, $email])) {
        $reservation_id = $pdo->lastInsertId();

        $message = "Merhaba,\n\nyakutvillam.com üzerinden \"{$reservation_id}\" numaralı rezervasyon talebim için ulaşıyorum. Bilgi ve destek alabilir miyim?";
        $encoded_message = urlencode($message);

        $stmtSetting = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
        $stmtSetting->execute();
        $setting = $stmtSetting->fetch();
        $admin_whatsapp = $setting ? $setting['setting_value'] : '';

        $clean_number = preg_replace('/\D/', '', $admin_whatsapp);

        $whatsapp_url = "https://wa.me/{$clean_number}?text={$encoded_message}";

        header("Location: " . $whatsapp_url);
        exit;
    } else {
        echo "Rezervasyon oluşturulurken bir hata oluştu.";
        exit;
    }
} else {
    echo "Geçersiz istek.";
    exit;
}
?>
