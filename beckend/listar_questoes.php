<?php
session_start();
header('Content-Type: application/json');

$arquivo = 'questoes.json';
$questoes = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

$busca = $_GET['busca'] ?? '';

if ($busca !== '') {
    $questoes = array_filter($questoes, function($q) use ($busca) {
        return (stripos($q['titulo'], $busca) !== false) || (stripos($q['enunciado'], $busca) !== false);
    });
}

// Reindexar array e retornar
echo json_encode(array_values($questoes));