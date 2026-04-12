<?php
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
$arquivo = 'questoes.json';
$questoes = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

foreach ($questoes as $q) {
    if ($q['id'] == $id) {
        echo json_encode($q);
        exit;
    }
}

http_response_code(404);