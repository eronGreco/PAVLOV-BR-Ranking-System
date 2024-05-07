<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "ERROR REPORTING ativo.<br><br>----------------------<br><br>";
echo "--- O que caralhos esse código faz?<br>Esse script acessa a tabela 'coletadaapi' e insere as informações de jogadores filtrados pelo gamemode SND em uma nova tabela chamada 'sndUnir'.<br><br>----------------------<br><br>";

// Configurações do banco de dados
require_once "/home/u232758309/domains/pavlovbr.com.br/dbconfigPAVLOV.php";

// Criar conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); 
echo "Conexão com o banco de dados iniciada.<br>"; 

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
echo "Conexão verificada com sucesso.<br>";

// Consulta para filtrar dados com 'SND' em gameMode
$sql = "SELECT name, season, SUM(kills) AS kills, SUM(death) AS death, SUM(assistant) AS assistant, 
        SUM(headshot) AS headshot, SUM(bombDefused) AS bombDefused, SUM(bombPlanted) AS bombPlanted, 
        SUM(teamKill) AS teamKill
        FROM coletadaapi
        WHERE gameMode = 'SND'
        GROUP BY name, season";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Consulta realizada com sucesso e dados encontrados.<br>";

    // Criar a nova tabela sndUnidos se ainda não existir
    $conn->query("CREATE TABLE IF NOT EXISTS sndUnidos (
        name VARCHAR(255),
        season VARCHAR(255),
        kills INT,
        death INT,
        assistant INT,
        headshot INT,
        bombDefused INT,
        bombPlanted INT,
        teamKill INT,
        UNIQUE KEY unique_index (name, season)
    )");
    echo "Tabela sndUnidos verificada/criada com sucesso.<br><br>----------------------<br><br>";
    echo "A operação começará agora, inserindo/atualizando a tabela com as informações de cada jogador.<br><br>----------------------<br><br>";


    while($row = $result->fetch_assoc()) {
        $insertSql = "INSERT INTO sndUnidos (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill)
                      VALUES ('".$row["name"]."', '".$row["season"]."', ".$row["kills"].", ".$row["death"].", ".$row["assistant"].", 
                      ".$row["headshot"].", ".$row["bombDefused"].", ".$row["bombPlanted"].", ".$row["teamKill"].")
                      ON DUPLICATE KEY UPDATE
                      kills = VALUES(kills),
                      death = VALUES(death),
                      assistant = VALUES(assistant),
                      headshot = VALUES(headshot),
                      bombDefused = VALUES(bombDefused),
                      bombPlanted = VALUES(bombPlanted),
                      teamKill = VALUES(teamKill)";

        if ($conn->query($insertSql) === TRUE) {
            if ($conn->affected_rows > 0) {
                echo "" . $row["name"] . " | " . $row["season"] . "<br>";
            } else {
                echo "" . $row["name"] . " | " . $row["season"] . "<br>";
            }
        } else {
            echo "Erro jogador " . $row["name"] . ": " . $conn->error . "<br>";
        }
    }

    echo "<br>----------------------<br><br>Dados inseridos/atualizados com sucesso na tabela 'sndUnidos'.<br><br>----------------------<br><br>";
} else {
    echo "0 resultados encontrados na consulta.<br><br>----------------------<br><br>";
}

$conn->close();
echo "SCRIPT FINALIZADO";
?>