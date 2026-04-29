<?php
/**
 * LISTAR_QUESTOES.PHP
 * Retorna lista de questões com filtros opcionais
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

header('Content-Type: application/json; charset=utf-8');

require 'config.php';
require 'helpers.php';

try {
    // Verificar autenticação e obter ID do usuário
    $id_usuario = verificarAutenticacao();
    
    $busca = $_GET['busca'] ?? '';
    $tipo = $_GET['tipo'] ?? '';
    $status = $_GET['status'] ?? '';
    $genero = $_GET['genero'] ?? '';
    
    $filtros = [];
    $filtros['id_usuario_criador'] = $id_usuario;  // FILTRO obrigatório por usuário
    if (!empty($tipo)) $filtros['tipo'] = $tipo;
    if (!empty($status)) $filtros['status'] = $status;
    if (!empty($genero)) $filtros['genero'] = $genero;
    
    $questoes = BancoQuestoes::listar($filtros);
    
    if (!empty($busca)) {
        $questoes = array_filter($questoes, function($q) use ($busca) {
            $busca = strtolower($busca);
            return stripos(strtolower($q['titulo'] ?? ''), $busca) !== false ||
                   stripos(strtolower($q['enunciado'] ?? ''), $busca) !== false;
        });
        $questoes = array_values($questoes);
    }
    
    Resposta::sucesso([
        'total' => count($questoes),
        'questoes' => $questoes
    ]);
} catch (Exception $e) {
    Resposta::erro('Erro ao listar questões: ' . $e->getMessage(), 500);
}
?>