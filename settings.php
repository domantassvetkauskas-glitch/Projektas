<?php

function db() {
    $conn = mysqli_connect("localhost", "root", "", "svetaines");
    if (!$conn) {
        die("DB neprisijungė: " . mysqli_connect_error());
    }
    return $conn;
}

function getSetting($key) {
    $conn = db();

    $key = mysqli_real_escape_string($conn, $key);
    $sql = "SELECT setting_value FROM settings WHERE setting_key='$key' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        return "SQL ERROR: " . mysqli_error($conn);
    }

    $row = mysqli_fetch_assoc($res);
    return $row ? $row['setting_value'] : "";
}
?>
