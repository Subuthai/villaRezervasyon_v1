<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include_once '../config/db.php';
include_once 'admin_header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? ORDER BY created_at DESC");
    $stmt->execute([$search]);
} else {
    $stmt = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC");
}
$reservations = $stmt->fetchAll();
?>

<div class="dashboard-container">
  <div class="form-container">
    <h2>Rezervasyon Yönetimi</h2>
    <form method="GET" action="">
      <label for="search">Rezervasyon Numarası Ara:</label>
      <input type="text" id="search" name="search" placeholder="Rezervasyon numarası" value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit">Ara</button>
    </form>
    <?php if($reservations && count($reservations) > 0): ?>
      <table class="reservation-table">
        <thead>
          <tr>
            <th>Rezervasyon No</th>
            <th>Villa ID</th>
            <th>Ad Soyad</th>
            <th>Telefon</th>
            <th>Giriş Tarihi</th>
            <th>Çıkış Tarihi</th>
            <th>Oluşturma Tarihi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($reservations as $res): ?>
          <tr>
            <td><?php echo $res['id']; ?></td>
            <td><?php echo $res['villa_id']; ?></td>
            <td><?php echo htmlspecialchars($res['name'] . ' ' . $res['surname']); ?></td>
            <td><?php echo htmlspecialchars($res['phone']); ?></td>
            <td><?php echo htmlspecialchars($res['check_in']); ?></td>
            <td><?php echo htmlspecialchars($res['check_out']); ?></td>
            <td><?php echo htmlspecialchars($res['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Rezervasyon bulunamadı.</p>
    <?php endif; ?>
  </div>
</div>

<style>
  .reservation-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
  }
  .reservation-table th, .reservation-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
  }
  .reservation-table th {
      background-color: #1a1a1a;
      color: #fff;
  }
  .reservation-table tr:nth-child(even) {
      background-color: #f2f2f2;
  }
  .reservation-table tr:hover {
      background-color: #ddd;
  }
  form input[type="text"] {
      padding: 8px;
      margin-right: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
  }
  form button {
      padding: 8px 16px;
      background-color: #1a1a1a;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s ease;
  }
  form button:hover {
      background-color: #333;
  }
</style>

<?php
include_once 'admin_footer.php';
?>
