<?php
require_once "../auth.php";
require_once "../settings.php";
$conn = db();
?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8">
  <title>Admin zona</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="p-4">
  <h1>Admin zona</h1>
  <p>Prisijungęs kaip: <?= htmlspecialchars($_SESSION["email"] ?? "") ?></p>

  <ul>
    <li><a href="products.php">Prekių valdymas (CRUD)</a></li>
    <li><a href="settings.php">Svetainės nustatymai</a></li>
    <li><a href="../logout.php">Atsijungti</a></li>
  </ul>
</body>
</html>
