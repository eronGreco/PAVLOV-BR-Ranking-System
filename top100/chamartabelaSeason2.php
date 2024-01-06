<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$servername = "localhost";
$username = "erongrecomelo_pavlovSND";
$password = "X&XV{V[+#&e5";
$dbname = "erongrecomelo_pavlovSND";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sndSeason2"; // Modifique esta consulta conforme necessário
$result = $conn->query($sql);

$players = array();
if ($result->num_rows > 0) {
    // Saída dos dados de cada linha
    while($row = $result->fetch_assoc()) {
        array_push($players, $row);
    }
} else {
    echo "0 results";
}

$conn->close();

echo json_encode($players);
?>
