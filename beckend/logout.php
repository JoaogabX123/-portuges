<?php
/**
 * LOGOUT.PHP
 * Encerra a sessão do usuário
 */

require 'config.php';
require 'helpers.php';

try {
    session_start();
    session_destroy();
    
    Resposta::sucesso(null, 'Logout realizado com sucesso');
} catch (Exception $e) {
    Resposta::erro('Erro ao fazer logout', 500);
}
?>