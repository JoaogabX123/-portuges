<?php
/**
 * TESTE DE LISTAR QUESTÕES
 * Acesse: http://localhost/Projeto%20+Portugues/app/routes/teste_questoes.php
 */

require_once __DIR__ . '/../config/config.php';

// Mostrar erros como texto simples
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE DE LISTAR QUESTÕES ===\n\n";

echo "Session ID: " . session_id() . "\n";
echo "Usuario ID: " . ($_SESSION['usuario_id'] ?? 'VAZIO') . "\n\n";

header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar autenticação
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['ok' => false, 'erro' => 'Não autenticado']);
        exit;
    }
    
    $id_usuario = $_SESSION['usuario_id'];
    echo "// Usuario ID do teste: $id_usuario\n";
    
    // Tentar listar questões
    $filtros = ['id_usuario_criador' => $id_usuario];
    
    echo "// Chamando Questao::listar...\n";
    $questoes = Questao::listar($filtros);
    
    echo json_encode([
        'ok' => true,
        'dados' => [
            'total' => count($questoes),
            'questoes' => $questoes
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'ok' => false, 
        'erro' => $e->getMessage(),
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

?>
