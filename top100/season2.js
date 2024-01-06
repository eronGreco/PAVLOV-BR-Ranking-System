document.addEventListener('DOMContentLoaded', function() {
  fetch('https://status.pavlovbr.com.br/top100/chamartabelaSeason2.php')
    .then(response => response.json())
    .then(data => {
      const sortedPlayers = data.sort((a, b) => calculateScore(b) - calculateScore(a)).slice(0, 100);
      const leaderboardList = document.querySelector('.leaderboard-list');

      sortedPlayers.forEach((player, index) => {
        const playerCard = document.createElement('div');
        playerCard.className = 'player-card';

        if (index === 0) {
          playerCard.classList.add('top1');
        } else if (index === 1) {
          playerCard.classList.add('top2');
        } else if (index === 2) {
          playerCard.classList.add('top3');
        }

        const playerScore = calculateScore(player);
        const badgeTitle = getBadgeTitle(Math.floor(playerScore / 220) + 1); // é necessário alterar o valor na function 'getBadgeUrl' também
        const playerName = window.innerWidth <= 600 ? truncatePlayerName(player.playerName) : player.playerName;
        const badgeUrl = getBadgeUrl(playerScore);
        const insignias = {
          killer: {
            src: player.totalKills > 2000 ? 'images/killON.png' : 'images/killOFF.png',
            title: 'Mais de 2000 abates'
          },
          bombPlanter: {
            src: player.totalBombPlanted > 100 ? 'images/bombON.png' : 'images/bombOFF.png',
            title: 'Mais de 100 bombas plantadas'
          },
          bombDefuser: {
            src: player.totalBombDefused > 100 ? 'images/desbombON.png' : 'images/desbombOFF.png',
            title: 'Mais de 100 bombas desarmadas'
          },
          kdaStar: {
            src: player.KDA > 2.0 ? 'images/kdaON.png' : 'images/kdaOFF.png',
            title: 'KDA acima de 2.0'
          }
        };

        playerCard.innerHTML = `
        <div class="player-header">
        <img class="player-badge" src="${badgeUrl}" alt="Player Badge" style="margin-right: 20px;">
        <div class="player-info">
          <div class="player-name">${playerName}<br><span class="badge-title">${badgeTitle}</span></div>
          <div class="player-rank">Ranking: #${index + 1}</div>
          <div class="player-score">Pontuação: ${playerScore}</div>
        </div>
        <div class="player-insignias">
          <img src="${insignias.killer.src}" alt="Killer Insignia" title="${insignias.killer.title}">
          <img src="${insignias.bombPlanter.src}" alt="Bomb Planter Insignia" title="${insignias.bombPlanter.title}">
          <img src="${insignias.bombDefuser.src}" alt="Bomb Defuser Insignia" title="${insignias.bombDefuser.title}">
          <img src="${insignias.kdaStar.src}" alt="KDA Star Insignia" title="${insignias.kdaStar.title}">
        </div>
      </div>
      <div class="player-stats">
            <div class="stat"><img src="images/kill.png" alt="Matou"> Matou: ${player.totalKills}</div>
            <div class="stat"><img src="images/death.png" alt="Morreu"> Morreu: ${player.totalDeaths}</div>
            <div class="stat"><img src="images/assist.png" alt="Assistências"> Assistências: ${player.totalAssistants}</div>
            <div class="stat"><img src="images/headshot.png" alt="Headshot"> Tiros na Cabeça: ${player.totalHeadshots}</div>
            <div class="stat"><img src="images/desbomb.png" alt="Bombas Desarmadas"> Bombas Desarmadas: ${player.totalBombDefused}</div>
            <div class="stat"><img src="images/bomb.png" alt="Bombas Plantadas"> Bombas Plantadas: ${player.totalBombPlanted}</div>
            <div class="stat"><img src="images/tk.png" alt="Aliados Mortos"> Aliados Mortos: ${player.totalTeamKills}</div>
            <div class="stat"><img src="images/kda.png" alt="KDA"> KDA: ${player.KDA}</div>
          </div>
        `;
        leaderboardList.appendChild(playerCard);

        if (index === 2 || index === 9) {
          const divider = document.createElement('div');
          divider.className = 'divider';
          leaderboardList.appendChild(divider);
        }
      });
    })

    .catch(error => console.error('Error:', error));
});

function getBadgeUrl(score) {
  const badgeNumber = Math.floor(score / 220) + 1; // Consistente com o resto do código
  return `images/badges/badge${Math.min(badgeNumber, 17)}.png`;
}

function truncatePlayerName(name) {
  return name.length > 20 ? name.substring(0, 20) + '...' : name;
}

function calculateScore(player) {
  return player.totalKills * 2 +
         player.totalDeaths * -2 +
         player.totalHeadshots * 1 +
         player.totalAssistants * 1 +
         player.totalBombDefused * 3 +
         player.totalBombPlanted * 2 +
         player.totalTeamKills * -5;
}

function getBadgeTitle(badgeNumber) {
  const titles = ["Soldado", "Cabo", "Terceiro-Sargento", "Segundo-Sargento", "Primeiro-Sargento", "Subtenente",
                 "Aspirante", "Segundo-Tenente", "Primeiro-Tenente", "Capitão", "Major", "Tenente-Coronel",
                 "Coronel", "General de Brigada", "General de Divisão", "General de Exército", "Marechal"
                ];
  if (badgeNumber < 1) {
      badgeNumber = 1;
  } else if (badgeNumber > titles.length) {
      badgeNumber = titles.length;
  }
  return titles[badgeNumber - 1];
}


function calculateKDA(kills, deaths, assists) {
  return deaths === 0 ? kills + assists : (kills + assists) / deaths;
}

// 
//
//
// FILTRO
//
//
// 


document.addEventListener('DOMContentLoaded', function() {
  var searchBox = document.getElementById('search-box');
  var clearButton = document.getElementById('clear-button');
  var divider = document.querySelector('.divider');

  searchBox.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
      filterPlayers();
    }
    toggleClearButton();
    toggleDivider();
  });

  document.getElementById('search-button').addEventListener('click', filterPlayers);

  clearButton.addEventListener('click', function() {
    searchBox.value = '';
    filterPlayers();
    toggleClearButton();
    toggleDivider();
  });

  function filterPlayers() {
    var filter = searchBox.value.toUpperCase();
    var playerCards = document.getElementsByClassName('player-card');

    for (var i = 0; i < playerCards.length; i++) {
      var playerName = playerCards[i].getElementsByClassName('player-name')[0];
      if (playerName) {
        var txtValue = playerName.textContent || playerName.innerText;
        playerCards[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 || filter === '' ? '' : 'none';
      }       
    }
    toggleDivider(); // Chama a função para mostrar ou esconder a .divider
  }

  function toggleClearButton() {
    clearButton.style.display = searchBox.value.length > 0 ? 'block' : 'none';
  }

  function toggleDivider() {
    var dividers = document.querySelectorAll('.leaderboard-list .divider');
        dividers.forEach(function(divider) {
      if(searchBox.value.length > 0) {
        divider.classList.add('hidden');
      } else {
        divider.classList.remove('hidden');
      }
    });
  }
  

  // Inicializa o estado do botão X e da .divider
  toggleClearButton();
  toggleDivider();
});

