<?php
session_start();
require_once "settings.php"; // turi būti db() funkcija

$conn = db();
$error = "";

// jei jau prisijungęs – galim mesti atgal į pradžią
if (isset($_SESSION["user_id"])) {
    // jei nori, gali palikti ir taip, kad leistų dar kartą prisijungti
    // header("Location: index.php");
    // exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Užpildykite el. paštą ir slaptažodį.";
    } else {
        // paimam ir is_admin lauką
        $stmt = mysqli_prepare($conn, "SELECT id, email, password, is_admin FROM users WHERE email = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $res  = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($res);

            if ($user && password_verify($password, $user["password"])) {
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["email"]    = $user["email"];
                $_SESSION["is_admin"] = (int)$user["is_admin"]; // 1 arba 0

                // po prisijungimo – atgal į pradžią
                header("Location: index.php");
                exit;
            } else {
                $error = "Neteisingas el. paštas arba slaptažodis.";
            }
        } else {
            $error = "Klaida jungiantis prie DB.";
        }
    }
}
?>
<!doctype html>
<html lang="lt">
<head>
    <meta charset="utf-8">
    <title>Prisijungimas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .auth-wrapper {
            max-width: 420px;
            margin: 40px auto;
            padding: 24px 28px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
            background: #fff;
        }
        .auth-wrapper h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .auth-wrapper label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .auth-wrapper input[type="email"],
        .auth-wrapper input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 1rem;
        }
        .auth-wrapper button[type="submit"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            background: #111;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }
        .auth-wrapper button[type="submit"]:hover {
            opacity: 0.9;
        }
        .auth-wrapper .msg {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        .auth-wrapper .msg.error {
            color: #c00;
        }
        .auth-wrapper .msg.success {
            color: #0a0;
        }
        .auth-wrapper .links {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .auth-wrapper .links a {
            color: #0077cc;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <h1>Prisijungti</h1>

        <?php if ($error): ?>
            <p class="msg error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>El. paštas
                <input type="email" name="email" required>
            </label>

            <label>Slaptažodis
                <input type="password" name="password" required>
            </label>

            <button type="submit" name="login" value="1">Prisijungti</button>
        </form>

        <div class="links">
            <p>Neturi paskyros? <a href="register.php">Registruokis čia</a></p>
            <p><a href="index.php">← Grįžti į pradžią</a></p>
        </div>
    </div>
</body>
</html>
