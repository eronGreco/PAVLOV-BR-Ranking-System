let currentSearchResult = [];
const MIN_SEARCH_LENGTH = 4;

async function fetchPlayerData(name) {
    try {
        const response = await fetch('https://status.pavlovbr.com.br/top100/chamartabelaSeason2.php');
        if (response.ok) {
            const players = await response.json();
            return players.find(p => p.name.toLowerCase() === name.toLowerCase());
        } else {
            throw new Error('Falha ao carregar dados dos jogadores');
        }
    } catch (error) {
        console.error("Erro:", error);
    }
}

function calculateScore(player) {
    return ((player.kills || 0) * 2) - ((player.death || 0) * 2) + ((player.headshot || 0) * 1) +
           ((player.assistant || 0) * 1) + ((player.bombDefused || 0) * 3) +
           ((player.bombPlanted || 0) * 2) - ((player.teamKill || 0) * 5);
}

async function searchPlayer(input) {
    if (input.length >= MIN_SEARCH_LENGTH) {
        try {
            const response = await fetch('https://status.pavlovbr.com.br/top100/chamartabelaSeason2.php');
            if (response.ok) {
                const players = await response.json();
                currentSearchResult = players.filter(p => p.name.toLowerCase().includes(input.toLowerCase()));
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
    const name = document.getElementById('searchInput').value.trim().toLowerCase();
    
    // Primeiro, buscamos todos os jogadores se o currentSearchResult estiver vazio
    if (currentSearchResult.length === 0) {
        await searchPlayer(name);
    }

    // Depois da busca, tentamos encontrar o jogador
    const player = currentSearchResult.find(p => p.name.toLowerCase() === name);

    if (player) {
        displayPlayerData(player);
    } else {
        alert('Jogador não encontrado. Por favor, verifique o nome e tente novamente.');
    }
}

function displayPlayerData(playerData) {
    if (playerData) {
        document.getElementById('name').textContent = playerData.name || 'Jogador Desconhecido';
        document.getElementById('playerScore').textContent = 'Pontuação : ' + (calculateScore(playerData) || 0);
        document.getElementById('kills').textContent = 'Matou: ' + (playerData.kills || 0);
        document.getElementById('death').textContent = 'Morreu: ' + (playerData.death || 0);
        document.getElementById('assistant').textContent = 'Assistências: ' + (playerData.assistant || 0);
        document.getElementById('headshot').textContent = 'Tiros na Cabeça: ' + (playerData.headshot || 0);
        document.getElementById('bombDefused').textContent = 'Bombas Desarmadas: ' + (playerData.BombDefused || 0);
        document.getElementById('bombPlanted').textContent = 'Bombas Plantadas: ' + (playerData.BombPlanted || 0);
        document.getElementById('teamKill').textContent = 'Aliados Mortos: ' + (playerData.teamKill || 0);
        document.getElementById('KDA').textContent = 'KDA: ' + (playerData.KDA || 0);
    }
}
