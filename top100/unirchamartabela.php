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

function getSeasonTableName($finishedTime) {
    $date = new DateTime($finishedTime);
    $month = $date->format('n');

    if ($month >= 7 && $month <= 12) {
        // Nome da tabela para Season 1
        return "PlayerStatsUnidosSeason1";
    } else {
        // Nome da tabela para Season 2
        return "PlayerStatsUnidosSeason2";
    }
}

// Consulta para agregar os dados de cada jogador para Season 1
$sqlSeason1 = "SELECT PlayerStats.playerName, 
               PlayerStats.matchId,
               SUM(PlayerStats.kills) AS totalKills, 
               SUM(PlayerStats.death) AS totalDeaths, 
               SUM(PlayerStats.asssistant) AS totalAssistants, 
               SUM(PlayerStats.headshot) AS totalHeadshots, 
               SUM(PlayerStats.bombDefused) AS totalBombDefused, 
               SUM(PlayerStats.bombPlanted) AS totalBombPlanted, 
               SUM(PlayerStats.teamKill) AS totalTeamKills,
               Matches.finishedTime
        FROM PlayerStats
        INNER JOIN Matches ON PlayerStats.matchId = Matches.matchId
        WHERE Matches.finishedTime BETWEEN '2023-07-01' AND '2023-12-31'
        GROUP BY PlayerStats.playerName, PlayerStats.matchId, Matches.finishedTime";

// Consulta para agregar os dados de cada jogador para Season 2
$sqlSeason2 = "SELECT PlayerStats.playerName, 
               PlayerStats.matchId,
               SUM(PlayerStats.kills) AS totalKills, 
               SUM(PlayerStats.death) AS totalDeaths, 
               SUM(PlayerStats.asssistant) AS totalAssistants, 
               SUM(PlayerStats.headshot) AS totalHeadshots, 
               SUM(PlayerStats.bombDefused) AS totalBombDefused, 
               SUM(PlayerStats.bombPlanted) AS totalBombPlanted, 
               SUM(PlayerStats.teamKill) AS totalTeamKills,
               Matches.finishedTime
        FROM PlayerStats
        INNER JOIN Matches ON PlayerStats.matchId = Matches.matchId
        WHERE Matches.finishedTime BETWEEN '2024-01-01' AND '2024-06-30'
        GROUP BY PlayerStats.playerName, PlayerStats.matchId, Matches.finishedTime";

// Executa as consultas para Season 1 e Season 2
$resultSeason1 = $conn->query($sqlSeason1);
$resultSeason2 = $conn->query($sqlSeason2);

if ($resultSeason1->num_rows > 0) {
    while($row = $resultSeason1->fetch_assoc()) {
        $kda = ($row["totalKills"] + $row["totalAssistants"]) / max($row["totalDeaths"], 1);
        $kdaFormatted = number_format($kda, 2);
        
        $seasonTable = getSeasonTableName($row['finishedTime']);
        $seasonNumber = (strpos($seasonTable, 'Season1') !== false) ? 1 : 2;
        
        $kdaString = "'" . $kdaFormatted . "'";
        
        $updateSql = "INSERT INTO $seasonTable (playerName, matchId, totalKills, totalDeaths, totalAssistants, totalHeadshots, totalBombDefused, totalBombPlanted, totalTeamKills, KDA) 
                      VALUES ('{$row["playerName"]}', {$row["matchId"]}, {$row["totalKills"]}, {$row["totalDeaths"]}, {$row["totalAssistants"]}, {$row["totalHeadshots"]}, {$row["totalBombDefused"]}, {$row["totalBombPlanted"]}, {$row["totalTeamKills"]}, $kdaString)
                      ON DUPLICATE KEY UPDATE 
                          totalKills=VALUES(totalKills), 
                          totalDeaths=VALUES(totalDeaths),
                          totalAssistants=VALUES(totalAssistants),
                          totalHeadshots=VALUES(totalHeadshots),
                          totalBombDefused=VALUES(totalBombDefused),
                          totalBombPlanted=VALUES(totalBombPlanted),
                          totalTeamKills=VALUES(totalTeamKills),
                          KDA=VALUES(KDA)";
        
        if ($conn->query($updateSql) === TRUE) {
            echo "Jogador: " . $row["playerName"] . " | Partida: " . $row["matchId"] . " | Season: " . $seasonNumber . "<br>\n";
        } else {
            echo "Erro ao atualizar os dados para o jogador: " . $row["playerName"] . " - " . $conn->error . "<br>\n";
        }
    }
} else {
    echo "0 resultados encontrados na tabela PlayerStats para Season 1.<br>\n";
}

if ($resultSeason2->num_rows > 0) {
    while($row = $resultSeason2->fetch_assoc()) {
        $kda = ($row["totalKills"] + $row["totalAssistants"]) / max($row["totalDeaths"], 1);
        $kdaFormatted = number_format($kda, 2);
        
        $seasonTable = getSeasonTableName($row['finishedTime']);
        $seasonNumber = (strpos($seasonTable, 'Season1') !== false) ? 1 : 2;
        
        $kdaString = "'" . $kdaFormatted . "'";
        
        $updateSql = "INSERT INTO $seasonTable (playerName, matchId, totalKills, totalDeaths, totalAssistants, totalHeadshots, totalBombDefused, totalBombPlanted, totalTeamKills, KDA) 
                      VALUES ('{$row["playerName"]}', {$row["matchId"]}, {$row["totalKills"]}, {$row["totalDeaths"]}, {$row["totalAssistants"]}, {$row["totalHeadshots"]}, {$row["totalBombDefused"]}, {$row["totalBombPlanted"]}, {$row["totalTeamKills"]}, $kdaString)
                      ON DUPLICATE KEY UPDATE 
                          totalKills=VALUES(totalKills), 
                          totalDeaths=VALUES(totalDeaths),
                          totalAssistants=VALUES(totalAssistants),
                          totalHeadshots=VALUES(totalHeadshots),
                          totalBombDefused=VALUES(totalBombDefused),
                          totalBombPlanted=VALUES(totalBombPlanted),
                          totalTeamKills=VALUES(totalTeamKills),
                          KDA=VALUES(KDA)";
        
        if ($conn->query($updateSql) === TRUE) {
            echo "Jogador: " . $row["playerName"] . " | Partida: " . $row["matchId"] . " | Season: " . $seasonNumber . "<br>\n";
        } else {
            echo "Erro ao atualizar os dados para o jogador: " . $row["playerName"] . " - " . $conn->error . "<br>\n";
        }
    }
} else {
    echo "0 resultados encontrados na tabela PlayerStats para Season 2.<br>\n";
}

$conn->close();
?>
