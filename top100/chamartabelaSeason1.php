<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Configurações do banco de dados
require_once "/home/u232758309/domains/pavlovbr.com.br/dbconfigPAVLOV.php";

// Criar conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(array("error" => "Conexão falhou: " . $conn->connect_error)));
}

$sql = "SELECT * FROM sndSeason1";
$result = $conn->query($sql);

$players = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $players[] = $row;
    }
    echo json_encode($players);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "0 results"));
}

$conn->close();
?>
