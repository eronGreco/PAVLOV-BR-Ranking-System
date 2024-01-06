<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Consulta para agregar os dados de cada jogador
$sql = "SELECT Matches.matchId, Matches.finishedTime, PlayerStats.playerName, 
               SUM(PlayerStats.kills) AS totalKills, 
               SUM(PlayerStats.death) AS totalDeaths, 
               SUM(PlayerStats.asssistant) AS totalAssistants, 
               SUM(PlayerStats.headshot) AS totalHeadshots, 
               SUM(PlayerStats.bombDefused) AS totalBombDefused, 
               SUM(PlayerStats.bombPlanted) AS totalBombPlanted, 
               SUM(PlayerStats.teamKill) AS totalTeamKills
        FROM Matches
        INNER JOIN PlayerStats ON Matches.matchId = PlayerStats.matchId
        GROUP BY Matches.matchId, PlayerStats.playerName, Matches.finishedTime";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Processa cada linha do resultado
    while($row = $result->fetch_assoc()) {
        $kda = ($row["totalKills"] + $row["totalAssistants"]) / max($row["totalDeaths"], 1);
        $kdaFormatted = number_format($kda, 2);

        $seasonTable = (date('n', strtotime($row['finishedTime'])) >= 7) ? "PlayerStatsUnidosSeason1" : "PlayerStatsUnidosSeason2";

        $kdaString = "'" . $kdaFormatted . "'";

        // SQL para inserir/atualizar na tabela da temporada
        $updateSql = "INSERT INTO $seasonTable (matchId, playerName, totalKills, totalDeaths, totalAssistants, totalHeadshots, totalBombDefused, totalBombPlanted, totalTeamKills, KDA) 
                      VALUES ('{$row["matchId"]}', '{$row["playerName"]}', {$row["totalKills"]}, {$row["totalDeaths"]}, {$row["totalAssistants"]}, {$row["totalHeadshots"]}, {$row["totalBombDefused"]}, {$row["totalBombPlanted"]}, {$row["totalTeamKills"]}, $kdaString)
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
            echo "Jogador: " . $row["playerName"] . " | Partida: " . $row["matchId"] . " | Season: " . $seasonTable . "<br>\n";
        } else {
            echo "Erro ao atualizar os dados para o jogador: " . $row["playerName"] . " - " . $conn->error . "<br>\n";
        }
    }
} else {
    echo "0 resultados encontrados na tabela PlayerStats.<br>\n";
}

$conn->close();
?>
