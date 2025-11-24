<?php
header('Content-Type: application/json; charset=utf-8');

// valida CEP (apenas números)
$cep = preg_replace('/\D/', '', $_GET['cep'] ?? '');
if (strlen($cep) !== 8) {
    http_response_code(400);
    echo json_encode(['error' => 'CEP inválido']);
    exit;
}

// consulta ViaCEP
$viacepUrl = "https://viacep.com.br/ws/{$cep}/json/";
$opts = ['http' => ['method' => "GET", 'header' => "User-Agent: site-adocao/1.0\r\n"]];
$context = stream_context_create($opts);
$viacepJson = @file_get_contents($viacepUrl, false, $context);
if (!$viacepJson) {
    http_response_code(502);
    echo json_encode(['error' => 'Erro ao consultar ViaCEP']);
    exit;
}
$viacep = json_decode($viacepJson, true);
if (empty($viacep) || isset($viacep['erro'])) {
    http_response_code(404);
    echo json_encode(['error' => 'CEP não encontrado']);
    exit;
}

// monta string para geocoding
$parts = [];
if (!empty($viacep['logradouro'])) $parts[] = $viacep['logradouro'];
if (!empty($viacep['bairro'])) $parts[] = $viacep['bairro'];
if (!empty($viacep['localidade'])) $parts[] = $viacep['localidade'];
if (!empty($viacep['uf'])) $parts[] = $viacep['uf'];
$search = implode(', ', $parts) . ', Brasil';

// Nominatim (server-side) — respeite rate limits e inclua User-Agent / email
$nominatim = "https://nominatim.openstreetmap.org/search?format=jsonv2&q=" . urlencode($search) . "&limit=1";
$opts2 = ['http' => ['method' => "GET", 'header' => "User-Agent: site-adocao/1.0 (contato@localhost)\r\nAccept-Language: pt-BR\r\n"]];
$context2 = stream_context_create($opts2);
$nJson = @file_get_contents($nominatim, false, $context2);
if (!$nJson) {
    http_response_code(502);
    echo json_encode(['error' => 'Erro ao consultar serviço de geocoding']);
    exit;
}
$n = json_decode($nJson, true);
if (empty($n) || !isset($n[0]['lat'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Localização não encontrada']);
    exit;
}

$item = $n[0];
echo json_encode([
    'lat' => (float)$item['lat'],
    'lon' => (float)$item['lon'],
    'display_name' => $item['display_name'] ?? '',
    'address' => $viacep
]);
exit;
