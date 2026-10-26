<?php
require_once 'api.php';

$tournamentId = $_GET['tournament_id'] ?? '';
$nomeLiga = $_GET['nome'] ?? 'Tabela do Campeonato';

if (empty($tournamentId)) {
    echo "<div style='background:#f8d7da; color:#721c24; padding:20px; font-family:sans-serif;'>ID do torneio não foi fornecido na URL. <a href='index.php'>Voltar</a></div>";
    exit;
}

function testarEndpointsTabela($tournamentId) {
    $apiKey = RAPIDAPI_KEY;
    $apiHost = RAPIDAPI_HOST;

    // Lista dos endpoints mais comuns em APIs de futebol na RapidAPI
    $endpointsPossiveis = [
        "v1/tournaments/table?tournament_id=" . $tournamentId,
        "v1/tournaments/get-standings?tournament_id=" . $tournamentId,
        "v1/standings?tournament_id=" . $tournamentId,
        "v1/tournaments/standing?id=" . $tournamentId
    ];

    $resultados = [];

    foreach ($endpointsPossiveis as $endpoint) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://{$apiHost}/{$endpoint}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: {$apiHost}",
                "x-rapidapi-key: {$apiKey}"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $dados = json_decode($response, true);
        $resultados[$endpoint] = $dados;
    }

    return $resultados;
}

$testes = testarEndpointsTabela($tournamentId);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><= htmlspecialchars($nomeLiga) ?> - Teste de Endpoints</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .back-btn {
            display: inline-block;
            background-color: #334155;
            color: #f8fafc;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .back-btn:hover { background-color: #475569; }
        .box-teste {
            background-color: #1e293b;
            padding: 20px;
            border-radius: 10px;
            color: #f8fafc;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container" style="max-width: 900px; margin-top: 30px;">
        <a href="index.php" class="back-btn">⬅ Voltar para os Jogos</a>

        <h1 style="color: #00e676; margin-bottom: 20px;"><?= htmlspecialchars($nomeLiga) ?></h1>

        <?php foreach ($testes as $rota => $resposta): ?>
            <div class="box-teste">
                <h3 style="color: #38bdf8; font-size: 1rem; margin-bottom: 8px;">Rota testada: <code><?= $rota ?></code></h3>
                <pre style="background: #0f172a; padding: 12px; border-radius: 6px; color: #cbd5e1; overflow-x: auto; max-height: 200px; font-size: 0.85rem;">
<?php print_r($resposta); ?>
                </pre>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>