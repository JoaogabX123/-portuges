<?php
/**
 * EXCLUIR_QUESTAO.PHP
 * Deleta questão por ID
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

session_start();

header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'erro' => "Erro PHP: $errstr (Linha $errline)"
    ]);
    exit;
});

require 'config.php';
require 'helpers.php';

try {
    // Verificar autenticação
    verificarAutenticacao();
    
    $dados = obterDadosJSON();
    $id = $dados['id'] ?? '';
    
    if (empty($id)) {
        Resposta::erro('ID da questão é obrigatório', 400);
    }
    
    // Converter para inteiro
    $id = intval($id);
    
    $questao = BancoQuestoes::encontrarPorId($id);
    
    if (!$questao) {
        Resposta::erro('Questão não encontrada', 404);
    }
    
    if (!empty($questao['imagem'])) {
        Upload::deletarImagem($questao['imagem']);
    }
    
    BancoQuestoes::deletar($id);
    
    Resposta::sucesso(null, 'Questão deletada com sucesso');
} catch (Exception $e) {
    Resposta::erro('Erro ao excluir questão: ' . $e->getMessage(), 500);
}
?>