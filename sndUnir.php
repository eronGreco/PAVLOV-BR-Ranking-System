<?php
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

// Consulta para filtrar dados com 'SND' em gameMode
$sql = "SELECT name, season, SUM(kills) AS kills, SUM(death) AS death, SUM(assistant) AS assistant, 
        SUM(headshot) AS headshot, SUM(bombDefused) AS bombDefused, SUM(bombPlanted) AS bombPlanted, 
        SUM(teamKill) AS teamKill
        FROM coletadaapi
        WHERE gameMode = 'SND'
        GROUP BY name, season";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
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

    // Inserir os dados agregados na tabela sndUnidos
    while($row = $result->fetch_assoc()) {
        $insertSql = "INSERT INTO sndUnidos (name, season, kills, death, assistant, headshot, bombDefused, bombPlanted, teamKill)
                      VALUES ('".$row["name"]."', '".$row["season"]."', ".$row["kills"].", ".$row["death"].", ".$row["assistant"].", 
                      ".$row["headshot"].", ".$row["bombDefused"].", ".$row["bombPlanted"].", ".$row["teamKill"].")";

        $conn->query($insertSql);
    }

    echo "Dados inseridos com sucesso na tabela sndUnidos";
} else {
    echo "0 resultados";
}

$conn->close();
?>
