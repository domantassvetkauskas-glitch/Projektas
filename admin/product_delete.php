<?php
require_once "../auth.php";
require_once "../settings.php";
$conn = db();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id > 0) {
    // pasiimam nuotrauką
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if ($row["image"]) {
            $path = __DIR__ . "/../" . $row["image"];
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: products.php");
exit;
