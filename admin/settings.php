<?php
require_once "../auth.php";
require_once "../settings.php";
$conn = db();

$msg = "";

// kokius laukus leidžiam redaguoti
$fields = [
    "company_name"    => "Įmonės pavadinimas",
    "site_title"      => "Svetainės pavadinimas",
    "company_phone"   => "Telefono numeris",
    "company_email"   => "El. paštas",
    "company_address" => "Adresas",
    "about_text"      => "Įmonės „Apie“ tekstas"
];

// logo tvarkymas atskirai
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // tekstiniai nustatymai
    foreach ($fields as $key => $label) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            $stmt = $conn->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
        }
    }

    // logotipas (failas)
    if (!empty($_FILES["company_logo"]["name"]) && $_FILES["company_logo"]["error"] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES["company_logo"]["name"], PATHINFO_EXTENSION));
        $allow = ["jpg","jpeg","png","webp","svg"];
        if (in_array($ext, $allow)) {
            $dir = __DIR__ . "/../nuotraukos/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $name = "logo_" . time() . "." . $ext;
            $path = $dir . $name;

            if (move_uploaded_file($_FILES["company_logo"]["tmp_name"], $path)) {
                $relPath = "nuotraukos/" . $name;
                $stmt = $conn->prepare("UPDATE settings SET setting_value=? WHERE setting_key='company_logo'");
                $stmt->bind_param("s", $relPath);
                $stmt->execute();
            }
        }
    }

    $msg = "Nustatymai išsaugoti.";
}

// dabartinės reikšmės
$current = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $current[$row["setting_key"]] = $row["setting_value"];
    }
}

?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8">
  <title>Svetainės nustatymai</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="p-4">
  <h1>Svetainės nustatymai</h1>
  <p style="color:green;"><?= htmlspecialchars($msg) ?></p>

  <form method="post" enctype="multipart/form-data">
    <?php foreach ($fields as $key => $label): ?>
      <p>
        <label><?= htmlspecialchars($label) ?><br>
        <?php if ($key === "about_text"): ?>
          <textarea name="<?= $key ?>" rows="5" cols="60"><?= htmlspecialchars($current[$key] ?? "") ?></textarea>
        <?php else: ?>
          <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($current[$key] ?? "") ?>">
        <?php endif; ?>
        </label>
      </p>
    <?php endforeach; ?>

    <p>
      <label>Įmonės logotipas<br>
        <input type="file" name="company_logo">
      </label><br>
      <?php if (!empty($current["company_logo"])): ?>
        <img src="../<?= htmlspecialchars($current["company_logo"]) ?>" style="max-height:80px; margin-top:8px;">
      <?php endif; ?>
    </p>

    <button type="submit">Išsaugoti</button>
  </form>

  <p><a href="index.php">← Atgal į admin pradžią</a></p>
</body>
</html>
