<?php
require_once "../auth.php";
require_once "../settings.php";
$conn = db();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$name = $sizes = "";
$price = 0;
$stock = 0;
$image = "";
$msg = "";

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $name  = $row["name"];
        $sizes = $row["sizes"];
        $price = $row["price"];
        $stock = $row["stock"];
        $image = $row["image"];
    }
}

// failo įkėlimas
function uploadImage($field) {
    if (empty($_FILES[$field]["name"])) return null;
    if ($_FILES[$field]["error"] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($_FILES[$field]["name"], PATHINFO_EXTENSION));
    $allow = ["jpg","jpeg","png","webp"];
    if (!in_array($ext, $allow)) return null;

    $dir = __DIR__ . "/../nuotraukos/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $name = time() . "_" . rand(1000, 9999) . "." . $ext;
    $path = $dir . $name;

    if (move_uploaded_file($_FILES[$field]["tmp_name"], $path)) {
        return "nuotraukos/" . $name; // santykinis kelias
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"] ?? "");
    $sizes = trim($_POST["sizes"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $stock = (int)($_POST["stock"] ?? 0);

    if ($name === "" || $price <= 0) {
        $msg = "Pavadinimas ir kaina privalomi.";
    } else {
        $newImage = uploadImage("image");

        if ($id > 0) {
            if ($newImage) {
                $stmt = $conn->prepare("UPDATE products SET name=?, sizes=?, price=?, stock=?, image=? WHERE id=?");
                $stmt->bind_param("ssdisi", $name, $sizes, $price, $stock, $newImage, $id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name=?, sizes=?, price=?, stock=? WHERE id=?");
                $stmt->bind_param("ssdii", $name, $sizes, $price, $stock, $id);
            }
            $stmt->execute();
            $msg = "Prekė atnaujinta.";
        } else {
            $img = $newImage ?: null;
            $stmt = $conn->prepare("INSERT INTO products (name, sizes, price, stock, image) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssdiss", $name, $sizes, $price, $stock, $img);
            $stmt->execute();
            $msg = "Prekė sukurta.";
            $id = $conn->insert_id;
        }
    }
}
?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8">
  <title><?= $id>0 ? "Redaguoti prekę" : "Nauja prekė" ?></title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="p-4">
  <h1><?= $id>0 ? "Redaguoti prekę" : "Nauja prekė" ?></h1>
  <p style="color:green;"><?= htmlspecialchars($msg) ?></p>

  <form method="post" enctype="multipart/form-data">
    <p>
      <label>Pavadinimas<br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
      </label>
    </p>
    <p>
      <label>Dydžiai<br>
        <input type="text" name="sizes" value="<?= htmlspecialchars($sizes) ?>">
      </label>
    </p>
    <p>
      <label>Kaina (€)<br>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
      </label>
    </p>
    <p>
      <label>Sandėlyje (vnt.)<br>
        <input type="number" name="stock" value="<?= htmlspecialchars($stock) ?>">
      </label>
    </p>
    <p>
      <label>Nuotrauka<br>
        <input type="file" name="image">
      </label><br>
      <?php if ($image): ?>
        <img src="../<?= htmlspecialchars($image) ?>" style="max-width:150px; margin-top:8px;">
      <?php endif; ?>
    </p>

    <button type="submit">Išsaugoti</button>
  </form>

  <p><a href="products.php">← Atgal į sąrašą</a></p>
</body>
</html>
