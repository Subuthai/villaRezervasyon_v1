<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$stmtFaq = $pdo->query("SELECT * FROM faq ORDER BY id DESC");
$faqs = $stmtFaq->fetchAll();

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
if (!in_array($lang, ['tr', 'en', 'ar'])) {
    $lang = 'tr';
}
$question_field = "question_" . $lang;
$answer_field = "answer_" . $lang;
?>
<link rel="stylesheet" href="/public/css/faq.css">

<main class="faq-container">
    <h1 class="faq-heading"><?php echo __('faq_heading') ?: "Sıkça Sorulan Sorular"; ?></h1>
    <div class="faq-list">
        <?php if(count($faqs) > 0): ?>
            <?php foreach($faqs as $faq): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <?php echo htmlspecialchars($faq[$question_field]); ?>
                    </div>
                    <div class="faq-answer">
                        <?php echo nl2br(htmlspecialchars($faq[$answer_field])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php echo __('nofaq')?></p>
        <?php endif; ?>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>
