<?php
require_once "../auth.php";
require_once "../settings.php";
$conn = db();

$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8">
  <title>Prekių valdymas</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="p-4">
  <h1>Prekės</h1>

  <p><a href="product_form.php">+ Nauja prekė</a></p>

  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Pavadinimas</th>
      <th>Dydžiai</th>
      <th>Kaina</th>
      <th>Sandėlyje</th>
      <th>Veiksmai</th>
    </tr>
    <?php if ($res && $res->num_rows > 0): ?>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$row["id"] ?></td>
          <td><?= htmlspecialchars($row["name"]) ?></td>
          <td><?= htmlspecialchars($row["sizes"]) ?></td>
          <td><?= number_format($row["price"], 2, ',', ' ') ?> €</td>
          <td><?= (int)$row["stock"] ?></td>
          <td>
            <a href="product_form.php?id=<?= (int)$row["id"] ?>">Redaguoti</a> |
            <a href="product_delete.php?id=<?= (int)$row["id"] ?>" onclick="return confirm('Trinti prekę?')">Trinti</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">Nėra prekių</td></tr>
    <?php endif; ?>
  </table>

  <p><a href="index.php">← Atgal į admin pradžią</a></p>
</body>
</html>
