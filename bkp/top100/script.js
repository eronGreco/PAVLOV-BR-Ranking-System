document.addEventListener('DOMContentLoaded', function() {
  fetch('http://api.pavlovbr.com.br/api/PavlovShackStats/PlayersStats')
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
        const badgeNumber = Math.floor(playerScore / 555) + 1;
        const badgeTitle = getBadgeTitle(badgeNumber);
        const playerName = window.innerWidth <= 600 ? truncatePlayerName(player.playerName) : player.playerName;
        const kdr = calculateKDR(player.kills, player.death, player.assist);
        const badgeUrl = `images/badges/badge${Math.min(badgeNumber, 18)}.png`;
        const insignias = {
          killer: {
            src: player.kills > 3000 ? 'images/killON.png' : 'images/killOFF.png',
            title: 'Mais de 3000 abates'
          },
          bombPlanter: {
            src: player.bombPlanted > 150 ? 'images/bombON.png' : 'images/bombOFF.png',
            title: 'Mais de 150 bombas plantadas'
          },
          bombDefuser: {
            src: player.bombDefused > 100 ? 'images/desbombON.png' : 'images/desbombOFF.png',
            title: 'Mais de 100 bombas desarmadas'
          },
          kdrStar: {
            src: kdr > 2.0 ? 'images/kdrON.png' : 'images/kdrOFF.png',
            title: 'KDR acima de 2.0'
          }
        };

        playerCard.innerHTML = `
          <div class="player-header">
          <img class="player-badge" src="${badgeUrl}" alt="Player Badge" style="margin-right: 20px;">
          <div class="player-info">
          <div class="player-name"><span class="badge-title">${badgeTitle}</span> ${playerName}</div>
              <div class="player-rank">Ranking: #${index + 1}</div>
              <div class="player-score">Pontuação: ${playerScore}</div>
            </div>
            <div class="player-insignias">
              <img src="${insignias.killer.src}" alt="Killer Insignia" title="${insignias.killer.title}">
              <img src="${insignias.bombPlanter.src}" alt="Bomb Planter Insignia" title="${insignias.bombPlanter.title}">
              <img src="${insignias.bombDefuser.src}" alt="Bomb Defuser Insignia" title="${insignias.bombDefuser.title}">
              <img src="${insignias.kdrStar.src}" alt="KDR Star Insignia" title="${insignias.kdrStar.title}">
            </div>
          </div>
          <div class="player-stats">
            <div class="stat"><img src="images/kill.png" alt="Matou"> Matou: ${player.kills}</div>
            <div class="stat"><img src="images/death.png" alt="Morreu"> Morreu: ${player.death}</div>
            <div class="stat"><img src="images/assist.png" alt="Assistências"> Assistências: ${player.assist}</div>
            <div class="stat"><img src="images/headshot.png" alt="Headshot"> Tiros na Cabeça: ${player.headShot}</div>
            <div class="stat"><img src="images/desbomb.png" alt="Bombas Desarmadas"> Bombas Desarmadas: ${player.bombDefused}</div>
            <div class="stat"><img src="images/bomb.png" alt="Bombas Plantadas"> Bombas Plantadas: ${player.bombPlanted}</div>
            <div class="stat"><img src="images/tk.png" alt="Aliados Mortos"> Aliados Mortos: ${player.teamKill}</div>
            <div class="stat"><img src="images/kdr.png" alt="KDR"> KDR: ${kdr.toFixed(2)}</div>

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
  const badgeNumber = Math.floor(score / 555) + 1;
  return `images/badges/badge${Math.min(badgeNumber, 18)}.png`;
}

function truncatePlayerName(name) {
  return name.length > 20 ? name.substring(0, 20) + '...' : name;
}

function calculateScore(player) {
  return player.kills * 2 +
         player.death * -2 +
         player.headShot * 1 +
         player.assist * 1 +
         player.bombDefused * 3 +
         player.bombPlanted * 2 +
         player.teamKill * -5;
}

function getBadgeTitle(badgeNumber) {
  const titles = ["Recruta", "Soldado", "Soldado", "Soldado", "Cabo", "Cabo",
                 "Sargento", "Sargento", "Subtenente", "Sargento-Mor", "Tenente",
                 "Capitão", "Major", "Coronel", "General de Brigada", "General de Divisão",
                 "General de Exército", "Marechal"];
  return titles[badgeNumber - 1];
}

function calculateKDR(kills, deaths, assists) {
  return deaths === 0 ? kills + assists : (kills + assists) / deaths;
}
