<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "ERROR REPORTING ativo.<br><br>----------------------<br><br>";
echo "--- O que caralhos esse código faz?<br>Esse script acessa a tabela 'sndUnidos' e mescla as informações de cada jogador em uma única linha, em uma nova tabela chamada 'sndSeason2'.<br>Essa tabela deverá ser manualmente criada (e alterada no código), toda vez que uma nova Temporada estiver ativa.<br><br>----------------------<br><br>";

// Configurações do banco de dados
require_once "/home/u232758309/domains/pavlovbr.com.br/dbconfigPAVLOV.php";

// Criar conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); 
echo "Conexão com o banco de dados iniciada.<br>"; 

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
echo "Conexão verificada com sucesso.<br><br>----------------------<br><br>";
echo "A operação começará agora, inserindo/atualizando a tabela com as informações de cada jogador.<br><br>----------------------<br><br>";

function upsertPlayerData($conn, $tableName, $data) {
    $stmt = $conn->prepare("INSERT INTO $tableName (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill, KDA)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                            kills = VALUES(kills), death = VALUES(death), assistant = VALUES(assistant),
                            headshot = VALUES(headshot), bombDefused = VALUES(bombDefused), bombPlanted = VALUES(bombPlanted),
                            teamKill = VALUES(teamKill), KDA = VALUES(KDA)");
    
    foreach ($data as $row) {
        $stmt->bind_param("ssiiiiiiid", ...array_values($row));
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "" . $row['name'] . " | " . $row['season'] . "<br>";
            } else {
                echo "" . $row['name'] . " | " . $row['season'] . "<br>";
            }
        } else {
            echo "Erro ao inserir/atualizar dados para o jogador " . $row['name'] . ": " . $conn->error . "<br>";
        }
    }
    echo "<br>----------------------<br><br>Operações completas na tabela: $tableName<br><br>----------------------<br><br>";
}

function insertSeasonData($conn, $season, $tableName) {
    $sql = "SELECT name, '$season' AS season, SUM(kills) as kills, SUM(death) as death, SUM(assistant) as assistant, SUM(headshot) as headshot, SUM(bombDefused) as bombDefused, SUM(bombPlanted) as bombPlanted, SUM(teamKill) as teamKill, AVG((kills + assistant) / (CASE WHEN death = 0 THEN 1 ELSE death END)) AS KDA FROM sndUnidos WHERE season = '$season' GROUP BY name";
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
}

$activeSeason = 'Season2';
$tableName = 'snd' . $activeSeason;
insertSeasonData($conn, $activeSeason, $tableName);

$conn->close();
echo "SCRIPT FINALIZADO";
?>