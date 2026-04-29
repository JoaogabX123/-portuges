<?php
/**
 * CONFIGURAÇÃO DE BANCO DE DADOS
 * Conexão MySQLi para o banco 'mais_portugues'
 */

// Configurações de conexão
$servername = "localhost";
$usuario = "root";
$senha = "";
$banco = "mais_portugues";
$port = 3306;

// Criar conexão MySQLi
$conexao = new mysqli($servername, $usuario, $senha, $banco, $port);

// Verificar conexão
if ($conexao->connect_error) {
    die(json_encode([
        'ok' => false,
        'erro' => 'Falha na conexão com o banco de dados: ' . $conexao->connect_error
    ]));
}

// Configurar charset UTF-8
if (!$conexao->set_charset("utf8mb4")) {
    die(json_encode([
        'ok' => false,
        'erro' => 'Erro ao definir charset: ' . $conexao->error
    ]));
}

// Habilitar autocommit (padrão MySQL)
$conexao->autocommit(true);
?>
