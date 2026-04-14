<?php
session_start();
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if ($dados['email'] === 'admin@admin.com' && $dados['senha'] === '123') {
    $_SESSION['usuario'] = $dados['email'];
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'erro' => 'E-mail ou senha inválidos!']);
}