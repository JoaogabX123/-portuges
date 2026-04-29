<?php
/**
 * LOGOUT.PHP
 * Encerra a sessão do usuário
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

header('Content-Type: application/json; charset=utf-8');

require 'config.php';
require 'helpers.php';

try {
    session_destroy();
    
    Resposta::sucesso(null, 'Logout realizado com sucesso');
} catch (Exception $e) {
    Resposta::erro('Erro ao fazer logout', 500);
}
?>