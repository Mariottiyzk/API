<?php
require_once 'config.php';

// Se der erro de endpoint, trocaremos '/v1/events/list' pelo caminho exato depois
function buscarJogos($endpoint = 'api/flashscore/v2/matches/live?sport_id=1&timezone=Europe%2FBerlin') {
    $apiKey = RAPIDAPI_KEY;
    $apiHost = RAPIDAPI_HOST;

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://{$apiHost}/{$endpoint}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: {$apiHost}",
            "x-rapidapi-key: {$apiKey}"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['error' => "Erro cURL: " . $err];
    }

    $dadosDecodificados = json_decode($response, true);
    
    // Retorna os dados como array, ou um erro se o formato for inválido
    return is_array($dadosDecodificados) ? $dadosDecodificados : ['error' => 'Resposta JSON inválida'];

    function buscarTabela($tournamentId) {
    $apiKey = RAPIDAPI_KEY;
    $apiHost = RAPIDAPI_HOST;

    // Substitua "v1/tournaments/standings" pelo endpoint exato que você achou na RapidAPI
    $endpoint = "v1/tournaments/standings?tournament_id=" . $tournamentId;

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://{$apiHost}/{$endpoint}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
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
    return is_array($dados) ? $dados : [];
}
}
?>

