<?php
// DB prisijungimas
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "svetaines";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Prisijungti nepavyko: " . $conn->connect_error);

// Įrašymas į DB, jei forma pateikta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vardas'])) {
    $vardas = $conn->real_escape_string($_POST['vardas']);
    $email  = $conn->real_escape_string($_POST['email']);
    $zinute = $conn->real_escape_string($_POST['zinute']);
    
    if (!empty($vardas) && !empty($email) && !empty($zinute) && isset($_POST['rules'])) {
        $sqlInsert = "INSERT INTO atsiliepimai (vardas, email, zinute, created_at)
                      VALUES ('$vardas', '$email', '$zinute', NOW())";
        $conn->query($sqlInsert);
    }
}

// Rodome atsiliepimus
$sql = "SELECT * FROM atsiliepimai ORDER BY created_at DESC";
$result = $conn->query($sql);

echo '<table class="table table-striped mt-3">';
echo '<thead><tr><th>Vardas</th><th>El. paštas</th><th>Žinutė</th><th>Data</th></tr></thead><tbody>';

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($row['vardas']).'</td>';
        echo '<td>'.htmlspecialchars($row['email']).'</td>';
        echo '<td>'.htmlspecialchars($row['zinute']).'</td>';
        echo '<td>'.$row['created_at'].'</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">Nėra atsiliepimų</td></tr>';
}

echo '</tbody></table>';

$conn->close();
?>
