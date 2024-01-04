<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "erongrecomelo_pavlovSND";
$password = "X&XV{V[+#&e5";
$dbname = "erongrecomelo_pavlovSND";

// Crie a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão bem-sucedida.<br>\n";

// SQL para criar a tabela PlayerStatsUnidos, se ainda não existir
$sql = "CREATE TABLE IF NOT EXISTS PlayerStatsUnidos (
    playerName VARCHAR(50),
    totalKills INT,
    totalDeaths INT,
    totalAssistants INT,
    totalHeadshots INT,
    totalBombDefused INT,
    totalBombPlanted INT,
    totalTeamKills INT,
    KDA FLOAT,
    PRIMARY KEY (playerName)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela PlayerStatsUnidos criada ou já existente.<br>\n";
} else {
    echo "Erro ao criar a tabela:<br> " . $conn->error;
}

// Consulta para agregar os dados de cada jogador
$sql = "SELECT playerName, 
               SUM(kills) AS totalKills, 
               SUM(death) AS totalDeaths, 
               SUM(asssistant) AS totalAssistants, 
               SUM(headshot) AS totalHeadshots, 
               SUM(bombDefused) AS totalBombDefused, 
               SUM(bombPlanted) AS totalBombPlanted, 
               SUM(teamKill) AS totalTeamKills 
        FROM PlayerStats 
        GROUP BY playerName";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Processa cada linha do resultado
    while($row = $result->fetch_assoc()) {
        $kda = ($row["totalKills"] + $row["totalAssistants"]) / max($row["totalDeaths"], 1); // Evita divisão por zero

        // Prepara o SQL para inserir ou atualizar os dados na tabela PlayerStatsUnidos
        $updateSql = "INSERT INTO PlayerStatsUnidos (playerName, totalKills, totalDeaths, totalAssistants, totalHeadshots, totalBombDefused, totalBombPlanted, totalTeamKills, KDA) 
                      VALUES ('{$row["playerName"]}', {$row["totalKills"]}, {$row["totalDeaths"]}, {$row["totalAssistants"]}, {$row["totalHeadshots"]}, {$row["totalBombDefused"]}, {$row["totalBombPlanted"]}, {$row["totalTeamKills"]}, $kda)
                      ON DUPLICATE KEY UPDATE 
                          totalKills=VALUES(totalKills), 
                          totalDeaths=VALUES(totalDeaths),
                          totalAssistants=VALUES(totalAssistants),
                          totalHeadshots=VALUES(totalHeadshots),
                          totalBombDefused=VALUES(totalBombDefused),
                          totalBombPlanted=VALUES(totalBombPlanted),
                          totalTeamKills=VALUES(totalTeamKills),
                          KDA=VALUES(KDA)";

        // Executa o SQL
        if ($conn->query($updateSql) === TRUE) {
            echo "Dados atualizados para o jogador: " . $row["playerName"] . "<br>\n";
        } else {
            echo "Erro ao atualizar os dados para o jogador: " . $row["playerName"] . " - " . $conn->error . "<br>\n";
        }
    }
} else {
    echo "0 resultados encontrados na tabela PlayerStats.<br>\n";
}

$conn->close();
?>
