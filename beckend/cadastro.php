<?php
/**
 * API de Cadastro de Usuários
 * Endpoint: POST /beckend/cadastro.php
 * Compatível com frontend que espera campo 'detail' ou 'mensagem'
 */

// Configurações iniciais
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'mensagem' => 'Método não permitido. Use POST.',
        'detail' => 'Método não permitido. Use POST.'
    ]);
    exit();
}


require_once __DIR__ . '/../database/config.php';

/**
 * Formata resposta de erro (compatível com frontend)
 */
function sendError($message, $httpCode = 400) {
    http_response_code($httpCode);
    echo json_encode([
        'mensagem' => $message,
        'detail' => $message,
        'error' => true
    ]);
    exit();
}

/**
 * Formata resposta de sucesso
 */
function sendSuccess($message = 'Cadastro realizado com sucesso') {
    http_response_code(200);
    echo json_encode([
        'mensagem' => $message,
        'detail' => $message,
        'success' => true
    ]);
    exit();
}

try {
    // Obter e validar input
    $jsonInput = file_get_contents('php://input');
    $input = json_decode($jsonInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('JSON inválido', 400);
    }
    
    // Verificar campos obrigatórios (suporta 'name' ou 'nome')
    $nome = $input['nome'] ?? $input['name'] ?? null;
    $email = $input['email'] ?? null;
    $senha = $input['senha'] ?? $input['password'] ?? null;
    
    if (!$nome) {
        sendError('Campo nome é obrigatório', 400);
    }
    if (!$email) {
        sendError('Campo email é obrigatório', 400);
    }
    if (!$senha) {
        sendError('Campo senha é obrigatório', 400);
    }
    
    // Validar email
    $email = trim(strtolower($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('E-mail inválido', 400);
    }
    
    // Validar senha (mesmas regras do frontend)
    $passwordErrors = [];
    if (strlen($senha) < 8) {
        $passwordErrors[] = 'mínimo de 8 caracteres';
    }
    if (!preg_match('/[A-Z]/', $senha)) {
        $passwordErrors[] = 'letra maiúscula';
    }
    if (!preg_match('/[a-z]/', $senha)) {
        $passwordErrors[] = 'letra minúscula';
    }
    if (!preg_match('/\d/', $senha)) {
        $passwordErrors[] = 'número';
    }
    
    if (!empty($passwordErrors)) {
        sendError('Senha deve conter: ' . implode(', ', $passwordErrors), 400);
    }
    
    // Sanitizar nome
    $nome = trim($nome);
    $nome = preg_replace('/\s+/', ' ', $nome);
    $nome = htmlspecialchars($nome, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    if (strlen($nome) < 3) {
        sendError('Nome deve ter pelo menos 3 caracteres', 400);
    }
    
    // Conectar ao banco
    if (class_exists('Database')) {
        $db = Database::getInstance()->getConnection();
    } else {
        // Fallback: tenta criar conexão PDO a partir das constantes definidas em config.php
        if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER')) {
            throw new Exception('Configuração de banco de dados ausente ou classe Database não encontrada.');
        }
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $db = new PDO($dsn, DB_USER, defined('DB_PASS') ? DB_PASS : '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    
    // Verificar se email já existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    
    if ($stmt->fetch()) {
        sendError('E-mail já cadastrado. Utilize outro e-mail ou faça login.', 409);
    }
    
    // Gerar hash da senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
    
    if ($senhaHash === false) {
        sendError('Erro ao processar a senha', 500);
    }
    
    // Inserir usuário
    $sql = "INSERT INTO usuarios (email, senha, nome, tipo, status, criado_em) 
            VALUES (:email, :senha, :nome, 'professor', 1, NOW())";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        ':email' => $email,
        ':senha' => $senhaHash,
        ':nome' => $nome
    ]);
    
    if (!$result) {
        sendError('Erro ao criar usuário no banco de dados', 500);
    }
    
    // Sucesso!
    sendSuccess('Cadastro realizado com sucesso!');
    
} catch (PDOException $e) {
    // Erro de duplicidade (segurança extra)
    if ($e->errorInfo[1] == 1062) {
        sendError('E-mail já cadastrado', 409);
    }
    
    // Log do erro (sem expor ao cliente)
    error_log("Erro no cadastro: " . $e->getMessage());
    sendError('Erro interno no servidor. Tente novamente mais tarde.', 500);
    
} catch (Exception $e) {
    error_log("Erro inesperado: " . $e->getMessage());
    sendError('Erro inesperado. Tente novamente mais tarde.', 500);
}
?>