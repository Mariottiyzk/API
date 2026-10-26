<?php
require_once 'api.php';

$matchId = $_GET['id'] ?? '';

if (empty($matchId)) {
    header("Location: index.php");
    exit;
}

// Endpoint de detalhes que estamos testando
$endpointDetalhes = "v1/events/data?id=" . $matchId;
$dadosDetalhes = buscarJogos($endpointDetalhes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Partida - Placar Central</title>
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
        .back-btn:hover {
            background-color: #475569;
        }
        .details-box {
            background-color: #1e293b;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>

    <div class="container" style="margin-top: 30px;">
        <a href="index.php" class="back-btn">⬅ Voltar para os Jogos</a>

        <div class="details-box">
            <h2>Estatísticas e Detalhes da Partida</h2>
            <p style="color: #94a3b8; margin-top: 5px;">ID do Jogo: <?= htmlspecialchars($matchId) ?></p>

            <hr style="border: 0; border-top: 1px solid #334155; margin: 20px 0;">

            <!-- MOSTRA O RETORNO EXATO DA API PARA DEBUGAR -->
            <pre style="text-align: left; background: #0f172a; padding: 15px; border-radius: 8px; color: #38bdf8; overflow-x: auto; max-height: 500px;">
<?php print_r($dadosDetalhes); ?>
            </pre>
        </div>
    </div>

</body>
</html>