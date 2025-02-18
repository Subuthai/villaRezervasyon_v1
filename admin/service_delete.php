<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include_once '../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Servis ID belirtilmedi.";
    exit;
}

$service_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    echo "Belirtilen servis bulunamadı.";
    exit;
}

$stmtDelete = $pdo->prepare("DELETE FROM services WHERE id = ?");
$stmtDelete->execute([$service_id]);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servis Siliniyor</title>
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
        <h2>Servis Siliniyor...</h2>
        <p>Lütfen bekleyin.</p>
    </div>
    <script>
        setTimeout(function(){
            window.location.href = "services.php";
        }, 1500);
    </script>
</body>
</html>
