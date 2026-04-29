<?php
/**
 * SIGNUP.PHP
 * Cria nova conta de usuário com email, nome e senha
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

header('Content-Type: application/json; charset=utf-8');

require 'config.php';
require 'helpers.php';

try {
    
    $dados = obterDadosJSON();
    
    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
        Resposta::erro('Nome, email e senha são obrigatórios', 400);
    }
    
    $nome = sanitizarTexto($dados['nome']);
    $email = sanitizarTexto($dados['email']);
    $senha = $dados['senha'];
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Resposta::erro('Email inválido', 400);
    }
    
    // Validar senha (mínimo 8 caracteres, letra maiúscula, minúscula e número)
    if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || 
        !preg_match('/[a-z]/', $senha) || !preg_match('/\d/', $senha)) {
        Resposta::erro('Senha deve ter mínimo 8 caracteres, uma letra maiúscula, uma minúscula e um número', 400);
    }
    
    global $conexao;
    
    // Verificar se email já existe
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception('Erro ao preparar query: ' . $conexao->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        Resposta::erro('Email já cadastrado', 409);
    }
    
    $stmt->close();
    
    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Inserir novo usuário
    $stmt = $conexao->prepare("INSERT INTO usuarios (email, senha, nome, tipo, status, criado_em) VALUES (?, ?, ?, 'professor', 1, NOW())");
    if (!$stmt) {
        throw new Exception('Erro ao preparar query: ' . $conexao->error);
    }
    
    $stmt->bind_param("sss", $email, $senha_hash, $nome);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Erro ao inserir usuário no banco de dados');
    }
    
    $novo_id = $conexao->insert_id;
    $stmt->close();
    
    // COMMIT para persistir os dados no banco
    $conexao->commit();
    
    Resposta::sucesso(['id' => $novo_id, 'email' => $email, 'nome' => $nome], 'Cadastro realizado com sucesso');
    
} catch (Exception $e) {
    $conexao->rollback();
    Resposta::erro('Erro no servidor: ' . $e->getMessage(), 500);
}
?>
