<?php
/**
 * LOGIN.PHP
 * Autentica usuário com email e senha usando banco de dados
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
    
    // Buscar usuário no banco de dados
    global $conexao;
    
    $stmt = $conexao->prepare("SELECT id, email, senha, nome FROM usuarios WHERE email = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception('Erro ao preparar query: ' . $conexao->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        Resposta::erro('Email ou senha inválidos', 401);
    }
    
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    
    // Verificar senha
    // A senha no banco está com hash password_hash(), então usar password_verify()
    if (!password_verify($senha, $usuario['senha'])) {
        // Fallback para senha em texto plano (para compatibilidade com dados legados)
        if ($senha !== $usuario['senha']) {
            Resposta::erro('Email ou senha inválidos', 401);
        }
    }
    
    // Atualizar último login
    $stmt = $conexao->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Erro ao preparar query: ' . $conexao->error);
    }
    
    $stmt->bind_param("i", $usuario['id']);
    $stmt->execute();
    $stmt->close();
    
    // Definir variáveis de sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['login_time'] = time();
    
    Resposta::sucesso(null, 'Login realizado com sucesso');
    
} catch (Exception $e) {
    Resposta::erro('Erro no servidor: ' . $e->getMessage(), 500);
}
?>