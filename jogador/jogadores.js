// Constantes e Variáveis Globais
const MIN_SEARCH_LENGTH = 4;
let currentSearchResult = [];

// Funções
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
    console.log('searchPlayer foi chamada com input:', input);
    if (input.length >= MIN_SEARCH_LENGTH) {
        try {
            const response = await fetch('https://status.pavlovbr.com.br/top100/chamartabelaSeason2.php');
            if (response.ok) {
                const players = await response.json();
                currentSearchResult = players.filter(p => p.name.toLowerCase().includes(input.toLowerCase()));
                console.log('Resultados da busca:', currentSearchResult);
            } else {
                console.error('Erro ao buscar jogadores');
            }
        } catch (error) {
            console.error("Erro:", error);
        }
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
        document.getElementById('bombDefused').textContent = 'Bombas Desarmadas: ' + (playerData.bombDefused || 0);
        document.getElementById('bombPlanted').textContent = 'Bombas Plantadas: ' + (playerData.bombPlanted || 0);
        document.getElementById('teamKill').textContent = 'Aliados Mortos: ' + (playerData.teamKill || 0);
        document.getElementById('KDA').textContent = 'KDA: ' + (playerData.KDA || 0);
    }
}

async function confirmPlayer() {
    const name = document.getElementById('searchInput').value.trim().toLowerCase();
    console.log('confirmPlayer foi chamada com name:', name);
    
    if (currentSearchResult.length === 0) {
        await searchPlayer(name);
    }

    const player = currentSearchResult.find(p => p.name.toLowerCase() === name);

    if (player) {
        displayPlayerData(player);
    } else {
        alert('Jogador não encontrado. Por favor, verifique o nome e tente novamente.');
    }
}

// Event Listener
document.addEventListener('DOMContentLoaded', function () {
    // Coloque aqui qualquer código que precisa ser executado após o carregamento do DOM
});
