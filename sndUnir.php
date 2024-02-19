<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Consulta para filtrar dados com 'SND' em gameMode
$sql = "SELECT name, season, SUM(kills) AS kills, SUM(death) AS death, SUM(assistant) AS assistant, 
        SUM(headshot) AS headshot, SUM(bombDefused) AS bombDefused, SUM(bombPlanted) AS bombPlanted, 
        SUM(teamKill) AS teamKill
        FROM coletadaapi
        WHERE gameMode = 'SND'
        GROUP BY name, season";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Consulta realizada com sucesso e dados encontrados.<br>"; // Echo após realizar a consulta e encontrar dados

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
        teamKill INT
    )");
    echo "Tabela sndUnidos verificada/criada com sucesso.<br>"; // Echo após verificar/criar a tabela

    // Inserir os dados agregados na tabela sndUnidos
    while($row = $result->fetch_assoc()) {
        $insertSql = "INSERT INTO sndUnidos (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill)
                      VALUES ('".$row["name"]."', '".$row["season"]."', ".$row["kills"].", ".$row["death"].", ".$row["assistant"].", 
                      ".$row["headshot"].", ".$row["bombDefused"].", ".$row["bombPlanted"].", ".$row["teamKill"].")";

        $conn->query($insertSql);
    }

    echo "Dados inseridos com sucesso na tabela sndUnidos.<br>"; // Echo após a inserção dos dados
} else {
    echo "0 resultados encontrados na consulta.<br>"; // Echo se a consulta não retornar resultados
}

$conn->close();
echo "Conexão encerrada.<br>"; // Echo após encerrar a conexão
?>
