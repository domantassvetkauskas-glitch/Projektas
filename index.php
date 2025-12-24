<?php
session_start();
require_once "settings.php"; // čia yra db() ir getSetting()

// DB prisijungimas
$conn = db();

// PREKĖS IŠ LENTELĖS „produktai“
$tableRows = "";
$sql       = "SELECT * FROM produktai ORDER BY id DESC";
$result    = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name  = htmlspecialchars($row['name']);
        $size  = htmlspecialchars($row['size']);
        $price = number_format($row['price'], 2, ',', ' ');
        $stock = htmlspecialchars($row['stock']);

        $tableRows .= "<tr>";
        $tableRows .= "<td>{$name}</td>";
        $tableRows .= "<td>{$size}</td>";
        $tableRows .= "<td>{$price} €</td>";
        $tableRows .= "<td>{$stock}</td>";
        $tableRows .= "</tr>";
    }
} else {
    $tableRows = "<tr><td colspan='4'>Šiuo metu nėra prekių.</td></tr>";
}

// Tekstai po lentele
$data = [
    'title'    => 'Prekių sąrašas',
    'subtitle' => 'Naujausi mūsų inventoriaus duomenys.'
];

// Vartotojo statusas
if (isset($_SESSION["user_id"])) {
    $email = htmlspecialchars($_SESSION["email"]);

    $links = [];

    // Admin linką rodome tik jei is_admin = 1
    if (!empty($_SESSION["is_admin"])) {
        $links[] = '<a href="admin/index.php">Admin</a>';
    }

    $links[] = '<a href="logout.php">Atsijungti</a>';

    $user_status = 'Prisijungęs kaip ' . $email . ' | ' . implode(' | ', $links);
} else {
    $user_status = '<a href="login.php">Prisijungti</a>';
}


// ŠABLONAS
$template = file_get_contents('template.html');

// pakeičiam lentelę ir tekstus
$template = str_replace("{{title}}",      $data['title'],    $template);
$template = str_replace("{{subtitle}}",   $data['subtitle'], $template);
$template = str_replace("{{table_rows}}", $tableRows,        $template);

// nustatymai iš settings
$template = str_replace('{{company_phone}}',   getSetting('company_phone'),   $template);
$template = str_replace('{{company_email}}',   getSetting('company_email'),   $template);
$template = str_replace('{{about_text}}',      getSetting('about_text'),      $template);
$template = str_replace('{{company_address}}', getSetting('company_address'), $template);

// puslapio pavadinimas (titelis) iš settings – vietoj PHP kodo template faile
$template = str_replace("<?php echo getSetting('site_title'); ?>", getSetting('site_title'), $template);

// vartotojo statusas
$template = str_replace('{{user_status}}', $user_status, $template);

// Atvaizduojame HTML
echo $template;

mysqli_close($conn);
?>
