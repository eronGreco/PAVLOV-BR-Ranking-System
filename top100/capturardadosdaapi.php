<?php
echo "Início do script.<br>";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = "localhost";
$username = "erongrecomelo_pavlovSND";
$password = "X&XV{V[+#&e5";
$dbname = "erongrecomelo_pavlovSND";
echo "Configurações do banco de dados definidas.<br>";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão com o banco de dados estabelecida.<br>";

// Fazer chamada à API para obter partidas
$apiUrl = "http://api.pavlovbr.com.br/api/PavlovShackStats/GetMatches";
$partidas = json_decode(file_get_contents($apiUrl), true);
echo "Chamada à API realizada. Total de partidas obtidas: " . count($partidas) . "<br>";

foreach ($partidas as $partida) {
    echo "Processando partida com ID: " . $partida['matchId'] . "<br>";

    if ($partida['gameMode'] === 'SND') {
        echo "Partida com gameMode SND encontrada.<br>";
    
        // Verificar se a partida já existe no banco de dados
        $sql = "SELECT matchId FROM Matches WHERE matchId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $partida['matchId']);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            // Ajustar finishedTime para 3 horas antes
            $finishedTime = new DateTime($partida['finishedTime']);
            $finishedTime->sub(new DateInterval('PT3H'));
            $adjustedFinishedTime = $finishedTime->format('Y-m-d H:i:s');
    
            // Inserir informações gerais da partida no banco de dados
            $sql = "INSERT INTO Matches (matchId, mapName, gameMode, team0Score, team1Score, playersMatch, finishedTime) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issiiis", $partida['matchId'], $partida['mapName'], $partida['gameMode'], $partida['team0Score'], $partida['team1Score'], $partida['playersMatch'], $adjustedFinishedTime);
            $stmt->execute();
            echo "Informações gerais da partida com ID " . $partida['matchId'] . " inseridas no banco de dados.<br>";
    
            // Processar jogadores apenas se a partida for nova
            processarJogadores($conn, $partida);
        } else {
            echo "Partida com ID " . $partida['matchId'] . " já existe no banco de dados.<br>";
        }
    } else {
        echo "Partida ignorada: gameMode não é SND.<br>";
    }
    
}

function inserirJogador($conn, $matchId, $jogador) {
    $sql = "INSERT INTO PlayerStats (matchId, playerName, kills, death, asssistant, headshot, bombDefused, bombPlanted, teamKill) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiiiiiii", $matchId, $jogador['name'], $jogador['kill'], $jogador['death'], $jogador['asssistant'], $jogador['headshot'], $jogador['bombDefused'], $jogador['bombPlanted'], $jogador['teamKill']);
    if ($stmt->execute()) {
        echo "Jogador " . $jogador['name'] . " inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir jogador " . $jogador['name'] . ": " . $conn->error . "<br>";
    }
}

function processarJogadores($conn, $partida) {
    $detalhesUrl = "http://api.pavlovbr.com.br/api/PavlovShackStats/TeamsMatch?matchId=" . $partida['matchId'];
    $detalhesPartida = json_decode(file_get_contents($detalhesUrl), true);
    echo "Detalhes da partida com ID " . $partida['matchId'] . " obtidos da API.<br>";

    foreach (['team0', 'team1'] as $team) {
        foreach ($detalhesPartida[$team] as $jogador) {
            // Inserir detalhes do jogador no banco de dados
            inserirJogador($conn, $partida['matchId'], $jogador);
        }
    }
}


$conn->close();
echo "Conexão com o banco de dados fechada.<br>";

?>