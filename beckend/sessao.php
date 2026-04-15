<?php
/**
 * SESSAO.PHP
 * Verifica se usuário está autenticado
 */

require 'config.php';
require 'helpers.php';

try {
    $usuarioId = verificarAutenticacao();
    
    $info = [
        'usuario_id' => $usuarioId,
        'usuario_email' => $_SESSION['usuario_email'] ?? 'desconhecido',
        'tempo_sessao' => time() - ($_SESSION['login_time'] ?? 0)
    ];
    
    Resposta::sucesso($info, 'Usuário autenticado');
} catch (Exception $e) {
    Resposta::erro('Erro ao verificar sessão', 500);
}
?>