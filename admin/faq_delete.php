<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include_once '../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "FAQ ID belirtilmedi.";
    exit;
}

$faq_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
$stmt->execute([$faq_id]);
$faq = $stmt->fetch();

if (!$faq) {
    echo "Belirtilen FAQ bulunamadı.";
    exit;
}

$stmtDelete = $pdo->prepare("DELETE FROM faq WHERE id = ?");
$stmtDelete->execute([$faq_id]);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Siliniyor</title>
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
        <h2>FAQ Siliniyor...</h2>
        <p>Lütfen bekleyin.</p>
    </div>
    <script>
        setTimeout(function(){
            window.location.href = "faq_admin.php";
        }, 1500);
    </script>
</body>
</html>
