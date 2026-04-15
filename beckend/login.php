<?php
/**
 * LOGIN.PHP
 * Autentica usuário com email e senha
 */

require 'config.php';
require 'helpers.php';

try {
    session_start();
    
    $dados = obterDadosJSON();
    
    if (empty($dados['email']) || empty($dados['senha'])) {
        Resposta::erro('Email e senha são obrigatórios', 400);
    }
    
    $email = sanitizarTexto($dados['email']);
    $senha = $dados['senha'];
    
    // TODO: Integrar com banco de dados
    if ($email === 'admin@admin.com' && $senha === '123') {
        $_SESSION['usuario_id'] = 1;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['login_time'] = time();
        
        Resposta::sucesso(null, 'Login realizado com sucesso');
    } else {
        Resposta::erro('Email ou senha inválidos', 401);
    }
} catch (Exception $e) {
    Resposta::erro('Erro no servidor: ' . $e->getMessage(), 500);
}
?>