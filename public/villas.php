<?php
include_once '../includes/header.php';
include_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM villas ORDER BY id DESC");
$villas = $stmt->fetchAll();

$stmtOther = $pdo->query("SELECT * FROM other_villas ORDER BY id DESC");
$otherVillas = $stmtOther->fetchAll();
?>

<link rel="stylesheet" href="/public/css/villas.css">

<main class="villas-container">
   <h2 class="section-title"><?php echo __('villas'); ?></h2>
   <div class="villa-grid">
       <?php foreach ($villas as $villa): ?>
           <div class="villa-card">
               <?php if (!empty($villa['image'])): ?>
                   <img src="/uploads/<?php echo htmlspecialchars($villa['image']); ?>" alt="<?php echo htmlspecialchars($villa['name_tr']); ?>">
               <?php else: ?>
                   <img src="/public/default_villa.jpg" alt="Villa Resmi">
               <?php endif; ?>
               <div class="villa-info">
                   <h3><?php echo htmlspecialchars($villa['name_tr']); ?></h3>
                   <p class="villa-location"><?php echo htmlspecialchars($villa['location']); ?></p>
                   <p class="villa-price">
                       <?php 
                       if ($villa['price'] == 0) {
                           echo "Bilgi almak için iletişime geçin.";
                       } else {
                           echo number_format($villa['price'], 2) . " TL";
                       }
                       ?>
                   </p>
                   <a href="villa_detail.php?id=<?php echo $villa['id']; ?>" class="btn"><?php echo __('details'); ?></a>
               </div>
           </div>
       <?php endforeach; ?>
   </div>
   
   <?php if(count($otherVillas) > 0): ?>
   <h2 class="section-title">Diğer Villalar</h2>
   <div class="villa-grid">
       <?php foreach ($otherVillas as $villa): ?>
           <div class="villa-card">
               <?php if (!empty($villa['image'])): ?>
                   <img src="/uploads/<?php echo htmlspecialchars($villa['image']); ?>" alt="<?php echo htmlspecialchars($villa['name_tr']); ?>">
               <?php else: ?>
                   <img src="/public/default_villa.jpg" alt="Villa Resmi">
               <?php endif; ?>
               <div class="villa-info">
                   <h3><?php echo htmlspecialchars($villa['name_tr']); ?></h3>
                   <p class="villa-location"><?php echo htmlspecialchars($villa['location']); ?></p>
                   <p class="villa-price">
                       <?php 
                       if ($villa['price'] == 0) {
                           echo "Bilgi almak için iletişime geçin.";
                       } else {
                           echo number_format($villa['price'], 2) . " TL";
                       }
                       ?>
                   </p>
                   <a href="villa_detail.php?id=<?php echo $villa['id']; ?>&type=other" class="btn"><?php echo __('details'); ?></a>
               </div>
           </div>
       <?php endforeach; ?>
   </div>
   <?php endif; ?>
</main>

<?php include_once '../includes/footer.php'; ?>
