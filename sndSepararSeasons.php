<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "erongrecomelo_pavlovSND";
$password = "X&XV{V[+#&e5";
$dbname = "erongrecomelo_pavlovSND";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para inserir dados em uma tabela específica
function insertSeasonData($conn, $season, $tableName) {
    $sql = "SELECT *, (kills + assistant) / (CASE WHEN death = 0 THEN 1 ELSE death END) AS KDA FROM sndUnidos WHERE season = '$season'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $insertSql = "INSERT INTO $tableName (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill, KDA)
                          VALUES ('".$row["name"]."', '".$row["season"]."', ".$row["kills"].", ".$row["death"].", ".$row["assistant"].", 
                          ".$row["headshot"].", ".$row["bombDefused"].", ".$row["bombPlanted"].", ".$row["teamKill"].", ".$row["KDA"].")";

            $conn->query($insertSql);
        }
        echo "Dados inseridos com sucesso na tabela $tableName\n";
    } else {
        echo "0 resultados para a temporada $season\n";
    }
}

// Inserir dados da Season1 na tabela sndSeason1
insertSeasonData($conn, 'Season1', 'sndSeason1');

// Inserir dados da Season2 na tabela sndSeason2
insertSeasonData($conn, 'Season2', 'sndSeason2');

$conn->close();
?>
