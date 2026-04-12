<?php
header('Content-Type: application/json');
$dados = json_decode(file_get_contents('php://input'), true);
$id = $dados['id'] ?? '';

$arquivo = 'questoes.json';
if (file_exists($arquivo)) {
    $questoes = json_decode(file_get_contents($arquivo), true);
    $questoes = array_filter($questoes, function($q) use ($id) {
        return $q['id'] != $id;
    });
    file_put_contents($arquivo, json_encode(array_values($questoes), JSON_PRETTY_PRINT));
}

echo json_encode(['ok' => true]);