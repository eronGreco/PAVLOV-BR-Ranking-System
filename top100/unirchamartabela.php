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

function getSeasonTableName($finishedTime) {
    $date = new DateTime($finishedTime);
    $year = $date->format('Y');
    $month = $date->format('n');

    if ($month >= 7 && $month <= 12) {
        return "PlayerStatsUnidosSeason1" . $year;
    } else {
        return "PlayerStatsUnidosSeason2" . ($year - 1); // A temporada 2 começa no ano anterior
    }
}

// Consulta para agregar os dados de cada jogador
$sql = "SELECT PlayerStats.playerName, 
               SUM(PlayerStats.kills) AS totalKills, 
               SUM(PlayerStats.death) AS totalDeaths, 
               SUM(asssistant) AS totalAssistants, 
               SUM(headshot) AS totalHeadshots, 
               SUM(bombDefused) AS totalBombDefused, 
               SUM(bombPlanted) AS totalBombPlanted, 
               SUM(teamKill) AS totalTeamKills 
               Matches.finishedTime
        FROM PlayerStats
        INNER JOIN Matches ON PlayerStats.matchId = Matches.matchId
        GROUP BY PlayerStats.playerName";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Processa cada linha do resultado
    while($row = $result->fetch_assoc()) {
        $kda = ($row["totalKills"] + $row["totalAssistants"]) / max($row["totalDeaths"], 1);
    
        // Determinar o nome da tabela da temporada
        $seasonTable = getSeasonTableName($row['finishedTime']);
    
        // SQL para inserir/atualizar na tabela da temporada
        $updateSql = "INSERT INTO $seasonTable (playerName, totalKills, totalDeaths, totalAssistants, totalHeadshots, totalBombDefused, totalBombPlanted, totalTeamKills, KDA) 
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
