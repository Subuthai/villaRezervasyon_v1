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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>FAQ ID belirtilmedi.</p>";
    include_once 'admin_footer.php';
    exit;
}

$faq_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
$stmt->execute([$faq_id]);
$faq = $stmt->fetch();

if (!$faq) {
    echo "<p>Belirtilen FAQ bulunamadı.</p>";
    include_once 'admin_footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_tr = trim($_POST['question_tr']);
    $question_en = trim($_POST['question_en']);
    $question_ar = trim($_POST['question_ar']);
    $answer_tr   = trim($_POST['answer_tr']);
    $answer_en   = trim($_POST['answer_en']);
    $answer_ar   = trim($_POST['answer_ar']);

    if (empty($question_tr) || empty($question_en) || empty($question_ar) ||
        empty($answer_tr) || empty($answer_en) || empty($answer_ar)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        $stmtUpdate = $pdo->prepare("UPDATE faq SET question_tr = ?, question_en = ?, question_ar = ?, answer_tr = ?, answer_en = ?, answer_ar = ? WHERE id = ?");
        if ($stmtUpdate->execute([$question_tr, $question_en, $question_ar, $answer_tr, $answer_en, $answer_ar, $faq_id])) {
            $success = "FAQ başarıyla güncellendi.";
            $stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
            $stmt->execute([$faq_id]);
            $faq = $stmt->fetch();
        } else {
            $error = "FAQ güncellenirken hata oluştu.";
        }
    }
}
?>

<link rel="stylesheet" href="/public/css/faq_admin.css">

<div class="faq-admin-container">
    <h2>FAQ Düzenle</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="faq-form">
        <label for="question_tr">Soru (TR):</label>
        <input type="text" id="question_tr" name="question_tr" value="<?php echo htmlspecialchars($faq['question_tr']); ?>" required>
        
        <label for="question_en">Question (EN):</label>
        <input type="text" id="question_en" name="question_en" value="<?php echo htmlspecialchars($faq['question_en']); ?>" required>
        
        <label for="question_ar">السؤال (AR):</label>
        <input type="text" id="question_ar" name="question_ar" value="<?php echo htmlspecialchars($faq['question_ar']); ?>" required>
        
        <label for="answer_tr">Cevap (TR):</label>
        <textarea id="answer_tr" name="answer_tr" rows="4" required><?php echo htmlspecialchars($faq['answer_tr']); ?></textarea>
        
        <label for="answer_en">Answer (EN):</label>
        <textarea id="answer_en" name="answer_en" rows="4" required><?php echo htmlspecialchars($faq['answer_en']); ?></textarea>
        
        <label for="answer_ar">الجواب (AR):</label>
        <textarea id="answer_ar" name="answer_ar" rows="4" required><?php echo htmlspecialchars($faq['answer_ar']); ?></textarea>
        
        <button type="submit" class="btn">Güncelle</button>
    </form>
</div>

<?php include_once 'admin_footer.php'; ?>
