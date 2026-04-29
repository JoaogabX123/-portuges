<?php
/**
 * BUSCAR_QUESTAO.PHP
 * Busca questão específica por ID
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

header('Content-Type: application/json; charset=utf-8');

require 'config.php';
require 'helpers.php';

try {
    // Tentar obter ID do GET primeiro (para compatibilidade), depois do POST JSON
    $id = $_GET['id'] ?? '';
    
    // Se não houver GET, tenta POST JSON
    if (empty($id)) {
        $dados = obterDadosJSON();
        $id = $dados['id'] ?? '';
    }
    
    if (empty($id)) {
        Resposta::erro('ID da questão é obrigatório', 400);
    }
    
    // Converter para inteiro para MySQLi
    $id = intval($id);
    
    $questao = BancoQuestoes::encontrarPorId($id);
    
    if (!$questao) {
        Resposta::erro('Questão não encontrada', 404);
    }
    
    Resposta::sucesso($questao);
} catch (Exception $e) {
    Resposta::erro('Erro ao buscar questão: ' . $e->getMessage(), 500);
}
?>