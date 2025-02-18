<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('contact_heading', 'contact_description', 'contact_phone', 'contact_email', 'contact_address', 'contact_map')");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<link rel="stylesheet" href="/public/css/contact.css">

<main class="contact-container">
    <h1 class="contact-heading">
        <?php echo isset($settings['contact_heading']) ? $settings['contact_heading'] : "İletişim"; ?>
    </h1>
    <div class="contact-content">
        <div class="contact-info">
            <p class="contact-description">
                <?php echo isset($settings['contact_description']) ? $settings['contact_description'] : "Bizimle iletişime geçmek için aşağıdaki bilgileri kullanabilirsiniz."; ?>
            </p>
            <ul class="contact-details">
                <?php if(isset($settings['contact_phone']) && !empty($settings['contact_phone'])): ?>
                    <li><strong><?php echo __('contact_phone'); ?>:</strong> <?php echo $settings['contact_phone']; ?></li>
                <?php endif; ?>
                <?php if(isset($settings['contact_email']) && !empty($settings['contact_email'])): ?>
                    <li><strong><?php echo __('contact_email'); ?>:</strong> <?php echo $settings['contact_email']; ?></li>
                <?php endif; ?>
                <?php if(isset($settings['contact_address']) && !empty($settings['contact_address'])): ?>
                    <li><strong><?php echo __('address'); ?>:</strong> <?php echo $settings['contact_address']; ?></li>
                <?php endif; ?>
            </ul>
            <?php if(isset($settings['contact_map']) && !empty($settings['contact_map'])): ?>
    <div class="contact-map">
        <?php 
        echo $settings['contact_map']; 
        ?>
    </div>
<?php endif; ?>

        </div>
        <div class="contact-form">
            <h2><?php echo __('get_in_touch'); ?></h2>
            <form action="/public/send_contact.php" method="POST">
                <label for="name"><?php echo __('your_name'); ?>:</label>
                <input type="text" id="name" name="name" required>

                <label for="email"><?php echo __('your_email'); ?>:</label>
                <input type="email" id="email" name="email" required>

                <label for="subject"><?php echo __('subject'); ?>:</label>
                <input type="text" id="subject" name="subject" required>

                <label for="message"><?php echo __('message'); ?>:</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit" class="btn"><?php echo __('send_message'); ?></button>
            </form>
        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>
