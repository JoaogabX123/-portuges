<?php
/**
 * TESTE DE ROTAS DIRETAS
 * Acesse: http://localhost/Projeto%20+Portugues/app/routes/usuarios.php?acao=verificar_sessao
 */

require_once __DIR__ . '/../config/config.php';

// Se vir erro HTML, será exibido aqui como plain text para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE DE SESSÃO ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Usuario ID: " . ($_SESSION['usuario_id'] ?? 'VAZIO') . "\n\n";

// Tentar chamar o método diretamente
echo "=== CHAMANDO CONTROLLER ===\n";

try {
    header('Content-Type: application/json; charset=utf-8');
    
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['ok' => false, 'erro' => 'Não autenticado']);
        exit;
    }
    
    $usuario_id = $_SESSION['usuario_id'];
    $usuario_email = $_SESSION['usuario_email'] ?? 'desconhecido';
    $tempo_sessao = time() - ($_SESSION['login_time'] ?? 0);
    
    echo json_encode([
        'ok' => true,
        'dados' => [
            'usuario_id' => $usuario_id,
            'usuario_email' => $usuario_email,
            'tempo_sessao' => $tempo_sessao
        ],
        'mensagem' => 'Usuário autenticado'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
}

?>
