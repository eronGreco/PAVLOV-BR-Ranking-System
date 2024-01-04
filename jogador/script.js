let currentSearchResult = [];
const MIN_SEARCH_LENGTH = 4;

async function fetchPlayerData(playerName) {
    try {
        const response = await fetch('https://status.pavlovbr.com.br/top100/chamartabela.php');
        if (response.ok) {
            const players = await response.json();
            return players.find(p => p.playerName.toLowerCase() === playerName.toLowerCase());
        } else {
            throw new Error('Falha ao carregar dados dos jogadores');
        }
    } catch (error) {
        console.error("Erro:", error);
    }
}

function calculateScore(player) {
    return ((player.totalKills || 0) * 2) - ((player.totalDeaths || 0) * 2) + ((player.totalHeadshots || 0) * 1) +
           ((player.totalAssistants || 0) * 1) + ((player.totalBombDefused || 0) * 3) +
           ((player.totalBombPlanted || 0) * 2) - ((player.totalTeamKills || 0) * 5);
}

async function searchPlayer(input) {
    if (input.length >= MIN_SEARCH_LENGTH) {
        try {
            const response = await fetch('https://status.pavlovbr.com.br/top100/chamartabela.php');
            if (response.ok) {
                const players = await response.json();
                currentSearchResult = players.filter(p => p.playerName.toLowerCase().includes(input.toLowerCase()));
                // Implemente aqui a lógica para mostrar sugestões de nomes de jogadores
            } else {
                console.error('Erro ao buscar jogadores');
            }
        } catch (error) {
            console.error("Erro:", error);
        }
    }
}

async function confirmPlayer() {
    const playerName = document.getElementById('searchInput').value.trim().toLowerCase();
    
    // Primeiro, buscamos todos os jogadores se o currentSearchResult estiver vazio
    if (currentSearchResult.length === 0) {
        await searchPlayer(playerName);
    }

    // Depois da busca, tentamos encontrar o jogador
    const player = currentSearchResult.find(p => p.playerName.toLowerCase() === playerName);

    if (player) {
        displayPlayerData(player);
    } else {
        alert('Jogador não encontrado. Por favor, verifique o nome e tente novamente.');
    }
}


function calculateKDA(player) {
    const kills = parseInt(player.totalKills) || 0;
    const assists = parseInt(player.totalAssistants) || 0;
    const deaths = parseInt(player.totalDeaths) || 1; // Evitar divisão por zero

    return ((kills + assists) / deaths).toFixed(2); // Arredonda para duas casas decimais
}

function displayPlayerData(playerData) {
    if (playerData) {
        document.getElementById('playerName').textContent = playerData.playerName || 'Jogador Desconhecido';
        document.getElementById('playerScore').textContent = 'Pontuação Total: ' + (calculateScore(playerData) || 0);
        document.getElementById('kills').textContent = 'Matou: ' + (playerData.totalKills || 0);
        document.getElementById('deaths').textContent = 'Morreu: ' + (playerData.totalDeaths || 0);
        document.getElementById('assists').textContent = 'Assistências: ' + (playerData.totalAssistants || 0);
        document.getElementById('headshots').textContent = 'Tiros na Cabeça: ' + (playerData.totalHeadshots || 0);
        document.getElementById('bombDefused').textContent = 'Bombas Desarmadas: ' + (playerData.totalBombDefused || 0);
        document.getElementById('bombPlanted').textContent = 'Bombas Plantadas: ' + (playerData.totalBombPlanted || 0);
        document.getElementById('teamKill').textContent = 'Aliados Mortos: ' + (playerData.totalTeamKills || 0);
        document.getElementById('kda').textContent = 'KDA: ' + calculateKDA(playerData); // Adicionando KDA
    }
}
