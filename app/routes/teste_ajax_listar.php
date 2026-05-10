<?php
/**
 * TESTE: Simular requisição AJAX da home.php
 * Acesse: http://localhost/Projeto%20+Portugues/app/routes/teste_ajax_listar.php?busca=
 */

require_once __DIR__ . '/../config/config.php';

echo "=== TESTE: LISTAR COM BUSCA VAZIA ===\n\n";

// Log tudo
error_log("=== TESTE INICIADO ===");
error_log("Session ID: " . session_id());
error_log("Usuario ID: " . ($_SESSION['usuario_id'] ?? 'VAZIO'));

header('Content-Type: application/json; charset=utf-8');

try {
    // Executar o mesmo código que QuestaoController::listar() faz
    $id_usuario = $_SESSION['usuario_id'] ?? null;
    
    if (!$id_usuario) {
        error_log("Erro: usuario_id não está na sessão!");
        echo json_encode(['ok' => false, 'erro' => 'Não autenticado']);
        exit;
    }
    
    error_log("Usuario ID: $id_usuario");
    
    $busca = $_GET['busca'] ?? '';
    error_log("Busca: " . ($busca ?: '(vazia)'));
    
    // Simular o que o controller faz
    $filtros = [];
    $filtros['id_usuario_criador'] = $id_usuario;
    
    error_log("Chamando Questao::listar com filtros: " . json_encode($filtros));
    
    $questoes = Questao::listar($filtros);
    
    error_log("Total de questões: " . count($questoes));
    
    // Aplicar busca em memória
    if (!empty($busca)) {
        $questoes = array_filter($questoes, function($q) use ($busca) {
            $busca_lower = strtolower($busca);
            return stripos(strtolower($q['titulo'] ?? ''), $busca_lower) !== false ||
                   stripos(strtolower($q['enunciado'] ?? ''), $busca_lower) !== false;
        });
        $questoes = array_values($questoes);
    }
    
    error_log("Após filtro: " . count($questoes));
    
    $resposta = [
        'ok' => true,
        'dados' => [
            'total' => count($questoes),
            'questoes' => $questoes
        ]
    ];
    
    error_log("Resposta pronta");
    
    echo json_encode($resposta);
    
} catch (Exception $e) {
    error_log("EXCEÇÃO: " . $e->getMessage());
    error_log("Arquivo: " . $e->getFile());
    error_log("Linha: " . $e->getLine());
    
    echo json_encode([
        'ok' => false,
        'erro' => $e->getMessage(),
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine()
    ]);
}

?>
