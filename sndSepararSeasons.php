<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "Configurações de erro inicializadas.<br>----------------------<br>";

// Configurações do banco de dados
require_once "/home/erongrecomelo/dbconfigPAVLOV.php"; // Assegure-se de que o caminho está correto e seguro

// Criar conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); // Use as constantes definidas em dbconfig.php
echo "Conexão iniciada.<br>"; // Echo após iniciar a conexão

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error); // Encerra a execução se a conexão falhar
}
echo "Conexão verificada com sucesso.<br>----------------------<br>"; // Echo após verificar a conexão


function upsertPlayerData($conn, $tableName, $data) {
    $stmt = $conn->prepare("INSERT INTO $tableName (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill, KDA)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                            kills = VALUES(kills), death = VALUES(death), assistant = VALUES(assistant),
                            headshot = VALUES(headshot), bombDefused = VALUES(bombDefused), bombPlanted = VALUES(bombPlanted),
                            teamKill = VALUES(teamKill), KDA = VALUES(KDA)");
    
    foreach ($data as $row) {
        $stmt->bind_param("ssiiiiiiid", ...array_values($row));
        $stmt->execute();
    }
    echo "Dados inseridos/atualizados com sucesso na tabela $tableName<br>----------------------<br>";
}

function insertSeasonData($conn, $season, $tableName) {
    $sql = "SELECT name, '$season' AS season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill, (kills + assistant) / (CASE WHEN death = 0 THEN 1 ELSE death END) AS KDA FROM sndUnidos WHERE season = '$season'";
    $result = $conn->query($sql);
    $data = [];

    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    if (count($data) > 0) {
        upsertPlayerData($conn, $tableName, $data);
    } else {
        echo "0 resultados encontrados para a temporada $season<br>";
    }
    echo "----------------------<br>";
}

$activeSeason = 'Season2';
$tableName = 'snd' . $activeSeason;
insertSeasonData($conn, $activeSeason, $tableName);

$conn->close();
echo "Conexão com o banco de dados fechada.<br>";
?>
