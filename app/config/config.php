<?php
/**
 * CONFIGURAÇÃO GLOBAL - PROJETO +PORTUGUÊS
 * Unifica banco de dados + constantes da aplicação
 */

// ============================================
// 1. SESSÃO (antes de qualquer coisa)
// ============================================
define('TEMPO_SESSAO', 3600); // 1 hora em segundos

// Configurar parâmetros da sessão ANTES de session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => TEMPO_SESSAO,
        'path' => '/',
        'httponly' => true,
        'secure' => false, // true em produção com HTTPS
        'samesite' => 'Lax'
    ]);
    session_start();
}

// ============================================
// 2. AMBIENTE E DEBUG
// ============================================
define('AMBIENTE', 'desenvolvimento'); // 'desenvolvimento' ou 'producao'
define('DEBUG_MODE', AMBIENTE === 'desenvolvimento');

// ============================================
// 3. BANCO DE DADOS - MySQLi
// ============================================
$db_config = [
    'servername' => 'localhost',
    'usuario'    => 'root',
    'senha'      => '',
    'banco'      => 'mais_portugues',
    'port'       => 3306,
    'charset'    => 'utf8mb4'
];

// Criar conexão MySQLi
global $conexao;
$conexao = new mysqli(
    $db_config['servername'],
    $db_config['usuario'],
    $db_config['senha'],
    $db_config['banco'],
    $db_config['port']
);

// Verificar conexão
if ($conexao->connect_error) {
    die(json_encode([
        'ok' => false,
        'erro' => 'Falha na conexão com o banco de dados: ' . $conexao->connect_error
    ]));
}

// Configurar charset UTF-8
if (!$conexao->set_charset($db_config['charset'])) {
    die(json_encode([
        'ok' => false,
        'erro' => 'Erro ao definir charset: ' . $conexao->error
    ]));
}

// Habilitar autocommit (padrão MySQL)
$conexao->autocommit(true);

// ============================================
// 4. CAMINHOS DO PROJETO
// ============================================
define('APP_ROOT', dirname(dirname(__DIR__)));
define('APP_PATH', APP_ROOT . '/app');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('BASE_URL', '/-portuges/');

// ============================================
// 5. CONFIGURAÇÕES DE UPLOAD
// ============================================
define('TAMANHO_MAXIMO_UPLOAD', 5 * 1024 * 1024); // 5MB
define('TIPOS_PERMITIDOS', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('EXTENSOES_PERMITIDAS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// ============================================
// 6. GÊNEROS E TIPOS DE QUESTÃO
// ============================================
define('GENEROS_TEXTO', [
    'narrativo'      => 'Narrativo',
    'argumentativo'  => 'Argumentativo',
    'descritivo'     => 'Descritivo',
    'expositivo'     => 'Expositivo',
    'instrucional'   => 'Instrucional'
]);

define('STATUS_QUESTAO', [
    'rascunho'   => 'Rascunho',
    'publicada'  => 'Publicada'
]);

define('TIPOS_QUESTAO', [
    'objetiva'     => 'Objetiva (Múltipla Escolha)',
    'dissertativa' => 'Dissertativa'
]);

define('ALTERNATIVAS', ['A', 'B', 'C', 'D', 'E']);

// ============================================
// 7. CONFIGURAÇÕES DE RESPOSTA HTTP
// ============================================
define('HEADER_JSON', 'Content-Type: application/json; charset=utf-8');

// ============================================
// 8. AUTOLOAD DE CLASSES
// ============================================

/**
 * Autoload para classes do modelo
 */
spl_autoload_register(function($classe) {
    $paths = [
        APP_PATH . '/models/',
        APP_PATH . '/controllers/',
    ];
    
    foreach ($paths as $path) {
        $arquivo = $path . $classe . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return true;
        }
    }
    
    return false;
});

// ============================================
// 9. HELPER FUNCTIONS
// ============================================

/**
 * Sanitizar texto contra XSS
 */
function sanitizarTexto($texto) {
    return trim(htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'));
}

/**
 * Obter dados JSON do POST
 */
function obterDadosJSON() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Verificar autenticação
 */
function verificarAutenticacao() {
    // Sessão já foi iniciada no início de config.php - não chamar session_start()
    
    if (!isset($_SESSION['usuario_id'])) {
        header(HEADER_JSON);
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'erro' => 'Não autenticado'
        ]);
        exit;
    }
    
    return (int) $_SESSION['usuario_id'];
}

/**
 * Resposta JSON de sucesso
 */
function resposta_sucesso($dados = null, $mensagem = '') {
    header(HEADER_JSON);
    http_response_code(200);
    
    $resposta = ['ok' => true];
    if (!empty($mensagem)) {
        $resposta['mensagem'] = $mensagem;
    }
    if ($dados !== null) {
        $resposta['dados'] = $dados;
    }
    
    echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Resposta JSON de erro
 */
function resposta_erro($mensagem, $codigo = 400) {
    header(HEADER_JSON);
    http_response_code($codigo);
    
    $resposta = [
        'ok' => false,
        'erro' => $mensagem
    ];
    
    if (DEBUG_MODE) {
        $resposta['debug'] = [
            'codigo_http' => $codigo,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Resposta JSON de validação
 */
function resposta_validacao($erros) {
    header(HEADER_JSON);
    http_response_code(422);
    
    echo json_encode([
        'ok' => false,
        'erro' => 'Erro de validação',
        'erros' => is_array($erros) ? $erros : [$erros]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
?>
