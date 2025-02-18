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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_faq'])) {
    $question_tr = trim($_POST['question_tr']);
    $question_en = trim($_POST['question_en']);
    $question_ar = trim($_POST['question_ar']);
    $answer_tr = trim($_POST['answer_tr']);
    $answer_en = trim($_POST['answer_en']);
    $answer_ar = trim($_POST['answer_ar']);
    
    if (empty($question_tr) || empty($question_en) || empty($question_ar) ||
        empty($answer_tr) || empty($answer_en) || empty($answer_ar)) {
        $error = "Tüm alanları doldurun.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO faq (question_tr, question_en, question_ar, answer_tr, answer_en, answer_ar) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$question_tr, $question_en, $question_ar, $answer_tr, $answer_en, $answer_ar])) {
            $success = "FAQ başarıyla eklendi.";
        } else {
            $error = "FAQ eklenirken hata oluştu.";
        }
    }
}

$stmtFaq = $pdo->query("SELECT * FROM faq ORDER BY id DESC");
$faqs = $stmtFaq->fetchAll();
?>

<link rel="stylesheet" href="/public/css/faq_admin.css">

<div class="faq-admin-container">
    <h2>FAQ Yönetimi</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="faq-form">
        <h3>Yeni FAQ Ekle</h3>
        <label for="question_tr">Soru (TR):</label>
        <input type="text" id="question_tr" name="question_tr" required>
        
        <label for="question_en">Question (EN):</label>
        <input type="text" id="question_en" name="question_en" required>
        
        <label for="question_ar">السؤال (AR):</label>
        <input type="text" id="question_ar" name="question_ar" required>
        
        <label for="answer_tr">Cevap (TR):</label>
        <textarea id="answer_tr" name="answer_tr" rows="4" required></textarea>
        
        <label for="answer_en">Answer (EN):</label>
        <textarea id="answer_en" name="answer_en" rows="4" required></textarea>
        
        <label for="answer_ar">الجواب (AR):</label>
        <textarea id="answer_ar" name="answer_ar" rows="4" required></textarea>
        
        <button type="submit" name="add_faq" class="btn">FAQ Ekle</button>
    </form>

    <div class="faq-list">
        <h3>Mevcut FAQ'lar</h3>
        <?php if(count($faqs) > 0): ?>
            <?php foreach($faqs as $faq): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <strong>TR:</strong> <?php echo htmlspecialchars($faq['question_tr']); ?><br>
                        <strong>EN:</strong> <?php echo htmlspecialchars($faq['question_en']); ?><br>
                        <strong>AR:</strong> <?php echo htmlspecialchars($faq['question_ar']); ?>
                    </div>
                    <div class="faq-answer">
                        <strong>Cevap (TR):</strong> <?php echo nl2br(htmlspecialchars($faq['answer_tr'])); ?><br>
                        <strong>Answer (EN):</strong> <?php echo nl2br(htmlspecialchars($faq['answer_en'])); ?><br>
                        <strong>الجواب (AR):</strong> <?php echo nl2br(htmlspecialchars($faq['answer_ar'])); ?>
                    </div>
                    <div class="faq-actions">
                        <a href="faq_edit.php?id=<?php echo $faq['id']; ?>" class="btn edit">Düzenle</a>
                        <a href="faq_delete.php?id=<?php echo $faq['id']; ?>" class="btn delete" onclick="return confirm('Bu FAQ\'ı silmek istediğinize emin misiniz?')">Sil</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz FAQ eklenmemiş.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'admin_footer.php'; ?>
