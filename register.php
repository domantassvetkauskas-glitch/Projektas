<?php
session_start();
require_once "settings.php";

$conn = db();
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $msg = "Užpildykite visus laukus.";
    } else {
        // ar yra toks vartotojas?
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($res)) {
            $msg = "Toks el. paštas jau užregistruotas.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (email, password) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                $msg = "Paskyra sukurta! Dabar galite prisijungti.";
            } else {
                $msg = "Klaida kuriant paskyrą.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8">
  <title>Registracija</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="p-4">
  <h1>Registracija</h1>

  <?php if ($msg): ?>
    <p style="color:<?= strpos($msg,'Klaida')!==false ? 'red' : 'green' ?>;">
      <?= htmlspecialchars($msg) ?>
    </p>
  <?php endif; ?>

  <form method="post">
    <p>
      <label>El. paštas<br>
        <input type="email" name="email" required>
      </label>
    </p>
    <p>
      <label>Slaptažodis<br>
        <input type="password" name="password" required>
      </label>
    </p>
    <p>
      <button type="submit" name="register" value="1">Registruotis</button>
    </p>
  </form>

  <p><a href="login.php">← Grįžti į prisijungimą</a></p>
  <p><a href="index.php">← Grįžti į pradžią</a></p>
</body>
</html>
