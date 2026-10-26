<?php
require_once 'api.php';
date_default_timezone_set('America/Sao_Paulo');

$dataAtualURL = $_GET['data'] ?? date('Y-m-d');
$dadosApi = buscarJogos();

if (isset($dadosApi['message']) || isset($dadosApi['error'])) {
    echo "<div style='background:#f8d7da; color:#721c24; padding:20px;'><pre>"; print_r($dadosApi); echo "</pre></div>";
    die();
}

$jogosAoVivo = [];
$jogosEncerrados = [];
$ligasUnicas = [];

$torneios = isset($dadosApi[0]) ? $dadosApi : [$dadosApi];

foreach ($torneios as $torneio) {
    if (!is_array($torneio)) continue;

    $nomeLiga = $torneio['name'] ?? 'Campeonato';
    $ligasUnicas[$nomeLiga] = $nomeLiga;

    $listaPartidas = $torneio['matches'] ?? [];

    if (is_array($listaPartidas)) {
        foreach ($listaPartidas as $partida) {
            if (!is_array($partida)) continue;

            $timestamp = $partida['timestamp'] ?? null;
            $dataFormatada = $timestamp ? date('d/m/Y H:i', $timestamp) : '';

            $statusInfo = $partida['match_status'] ?? [];
            $isInProgress = !empty($statusInfo['is_in_progress']);
            $minutoVivo = $statusInfo['live_minute'] ?? '';

            $dadosJogo = [
                'liga'        => $nomeLiga,
                'time_casa'   => $partida['home_team']['name'] ?? 'Casa',
                'time_fora'   => $partida['away_team']['name'] ?? 'Fora',
                'logo_casa'   => $partida['home_team']['small_image_path'] ?? '',
                'logo_fora'   => $partida['away_team']['small_image_path'] ?? '',
                'placar_casa' => $partida['scores']['home'] ?? 0,
                'placar_fora' => $partida['scores']['away'] ?? 0,
                'minuto'      => $minutoVivo ? $minutoVivo . "'" : 'Ao Vivo',
                'data_hora'   => $dataFormatada
            ];

            if ($isInProgress) {
                $jogosAoVivo[] = $dadosJogo;
            } else {
                $jogosEncerrados[] = $dadosJogo;
            }
        }
    }
}
ksort($ligasUnicas);
$horaAtual = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placar de Futebol - Resultados em Tempo Real</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="header">
        <h1>⚽ Placar Central Futebol</h1>
        <p>Resultados ao vivo, estatísticas e campeonatos</p>
    </div>

    <!-- CONTÊINER PRINCIPAL: Lado a Lado (Sidebar à Esquerda) -->
    <div class="main-container">
        
        <!-- BARRA LATERAL (FIXA À ESQUERDA) -->
        <div class="sidebar">
            
            <!-- LIGAS PRINCIPAIS -->
            <h3>⭐ Ligas Principais</h3>
            <button class="league-btn" onclick="selecionarLiga('Brasileirao', this)">🇧🇷 Brasileirão</button>
            <button class="league-btn" onclick="selecionarLiga('Premier League', this)">🏴󠁧󠁢󠁥󠁮󠁧󠁿 Premier League</button>
            <button class="league-btn" onclick="selecionarLiga('Champions League', this)">🇪🇺 Champions League</button>
            <button class="league-btn" onclick="selecionarLiga('La Liga', this)">🇪🇸 La Liga</button>

            <!-- CAMPEONATOS DO DIA -->
            <h3 style="margin-top: 20px;">🏆 Campeonatos do Dia</h3>
            <button class="league-btn active" onclick="selecionarLiga('todas', this)">🌐 Todas as Ligas</button>
            
            <?php foreach ($torneios as $torneio): ?>
                <?php 
                    if (!is_array($torneio)) continue;
                    $nomeLiga = $torneio['name'] ?? 'Campeonato';
                ?>
                <button class="league-btn" onclick="selecionarLiga('<?= htmlspecialchars($nomeLiga, ENT_QUOTES) ?>', this)">
                    <?= htmlspecialchars($nomeLiga) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <!-- CONTEÚDO DA DIREITA -->
        <div class="content-area">
            
            <!-- BARRA DE PESQUISA E DATAS -->
            <form method="GET" action="index.php" class="top-bar">
                <div class="search-box" style="flex: 1;">
                    <input type="text" id="inputBusca" onkeyup="filtrarJogos()" placeholder="🔍 Buscar time (ex: Flamengo, Real Madrid)...">
                </div>
                <div class="date-box">
                    <input type="date" name="data" value="<?= htmlspecialchars($dataAtualURL) ?>" onchange="this.form.submit()">
                </div>
            </form>

            <div class="refresh-info">
                Data: <strong><?= date('d/m/Y', strtotime($dataAtualURL)) ?></strong> | Atualizado às <?= $horaAtual ?>
            </div>

            <!-- JOGOS EM ANDAMENTO -->
            <h2 class="section-title">Ao Vivo <span class="badge-live">LIVE</span></h2>

            <div>
                <?php if (empty($jogosAoVivo)): ?>
                    <p style="color: #94a3b8; background: #1e293b; padding: 15px; border-radius: 8px;">Nenhum jogo em andamento nesta data.</p>
                <?php else: ?>
                    <?php foreach ($jogosAoVivo as $jogo): ?>
                        <div class="match-card" data-liga="<?= htmlspecialchars($jogo['liga']) ?>">
                            <div style="flex: 1;">
                                <div class="league-name-tag"><?= htmlspecialchars($jogo['liga']) ?></div>
                                <div class="teams">
                                    <div class="team-info right">
                                        <span class="team-name"><?= htmlspecialchars($jogo['time_casa']) ?></span>
                                        <?php if($jogo['logo_casa']): ?>
                                            <img src="<?= $jogo['logo_casa'] ?>" class="team-logo" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <span class="score"><?= $jogo['placar_casa'] ?> - <?= $jogo['placar_fora'] ?></span>
                                    
                                    <div class="team-info">
                                        <?php if($jogo['logo_fora']): ?>
                                            <img src="<?= $jogo['logo_fora'] ?>" class="team-logo" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span class="team-name"><?= htmlspecialchars($jogo['time_fora']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="match-status live-status">
                                ⏱️ <?= htmlspecialchars($jogo['minuto']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- JOGOS FINALIZADOS -->
            <h2 class="section-title" style="margin-top: 20px;">Partidas / Encerrados</h2>

            <div>
                <?php if (empty($jogosEncerrados)): ?>
                    <p style="color: #94a3b8; background: #1e293b; padding: 15px; border-radius: 8px;">Nenhum jogo listado para esta data.</p>
                <?php else: ?>
                    <?php foreach ($jogosEncerrados as $jogo): ?>
                        <div class="match-card" data-liga="<?= htmlspecialchars($jogo['liga']) ?>">
                            <div style="flex: 1;">
                                <div class="league-name-tag"><?= htmlspecialchars($jogo['liga']) ?></div>
                                <div class="teams">
                                    <div class="team-info right">
                                        <span class="team-name"><?= htmlspecialchars($jogo['time_casa']) ?></span>
                                        <?php if($jogo['logo_casa']): ?>
                                            <img src="<?= $jogo['logo_casa'] ?>" class="team-logo" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <span class="score" style="color: #f8fafc; background: #334155;"><?= $jogo['placar_casa'] ?> - <?= $jogo['placar_fora'] ?></span>
                                    
                                    <div class="team-info">
                                        <?php if($jogo['logo_fora']): ?>
                                            <img src="<?= $jogo['logo_fora'] ?>" class="team-logo" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span class="team-name"><?= htmlspecialchars($jogo['time_fora']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="match-status">
                                Finalizado
                                <?php if($jogo['data_hora']): ?>
                                    <span class="match-date"><?= htmlspecialchars($jogo['data_hora']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- SCRIPT DE FILTRAGEM -->
    <script>
        let ligaAtiva = 'todas';

        function selecionarLiga(liga, elementoBotao) {
            ligaAtiva = liga;
            document.querySelectorAll('.league-btn').forEach(btn => btn.classList.remove('active'));
            elementoBotao.classList.add('active');
            filtrarJogos();
        }

        function filtrarJogos() {
            const termoBusca = document.getElementById('inputBusca').value.toLowerCase().trim();
            const todosOsCards = document.querySelectorAll('.match-card');

            todosOsCards.forEach(card => {
                const ligaDoCard = card.getAttribute('data-liga');
                const textoCard = card.innerText.toLowerCase();

                const atendeLiga = (ligaAtiva === 'todas' || ligaDoCard === ligaAtiva);
                const atendeBusca = (termoBusca === '' || textoCard.includes(termoBusca));

                if (atendeLiga && atendeBusca) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>