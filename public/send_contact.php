<?php
session_start();
include_once '../config/db.php';
include_once '../includes/language.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "Lütfen tüm alanları doldurun.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mesaj İletildi</title>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #f7f7f7;
                }
                .modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
                .modal-content {
                    background: #fff;
                    padding: 2rem 3rem;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                    font-size: 1.5rem;
                    color: #333;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="modal" id="thankYouModal">
                <div class="modal-content">
                    Mesajınız iletildi. Teşekkür ederiz!
                </div>
            </div>
            <script>
                setTimeout(function(){
                    window.location.href = "/public/contact.php";
                }, 1500);
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "Mesaj gönderilirken bir hata oluştu.";
        exit;
    }
} else {
    echo "Geçersiz istek.";
    exit;
}
?>
