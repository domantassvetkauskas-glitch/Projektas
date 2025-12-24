<?php include "settings.php"; ?>
<?php
// DB prisijungimas
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "svetaines";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Prisijungti nepavyko: " . $conn->connect_error);

// Statinis tekstas
$data = [
    'title' => 'Prekių sąrašas',
    'subtitle' => 'Naujausi mūsų inventoriaus duomenys.'
];


// SQL užklausa
$sql = "SELECT * FROM produktai";
$result = $conn->query($sql);

// Generuojame lentelės eilutes
$tableRows = "";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tableRows .= "<tr>
                          <td>{$row['name']}</td>
                          <td>{$row['size']}</td>
                          <td>{$row['price']} €</td>
                          <td>{$row['stock']}</td>
                       </tr>";
    }
} else {
    $tableRows = "<tr><td colspan='4'>Įrašų nėra</td></tr>";
}

$conn->close();

// Įkeliame HTML šabloną
$template = file_get_contents('template.html');

// Pakeičiame žymes
$template = str_replace("{{title}}", $data['title'], $template);
$template = str_replace("{{subtitle}}", $data['subtitle'], $template);
$template = str_replace("{{table_rows}}", $tableRows, $template);
$template = str_replace('{{company_phone}}', getSetting('company_phone'), $template);
$template = str_replace('{{company_email}}', getSetting('company_email'), $template);
$template = str_replace('{{about_text}}', getSetting('about_text'), $template);
$template = str_replace('{{company_address}}', getSetting('company_address'), $template);

// Atvaizduojame HTML
echo $template;
?>
