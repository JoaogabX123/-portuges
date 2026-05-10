<?php
/**
 * PUBLIC ENTRY POINT - /public/index.php
 * Ponto único de acesso - Router central
 * Renderiza views sem redirecionar HTTP
 */

// Requer configuração global e autoloader (com session_start())
require_once __DIR__ . '/../app/config/config.php';

// Determinar view a renderizar
$page = isset($_GET['page']) ? sanitizarTexto($_GET['page']) : null;
$authenticated = isset($_SESSION['usuario_id']);

// Se tenta acessar página protegida sem autenticação
if ($page && !$authenticated && !in_array($page, ['login', 'signup'])) {
    $page = 'login';
}

// Definir view padrão baseado no status de autenticação
if (!$page) {
    $page = $authenticated ? 'home' : 'index';
}

// Se logado e tenta acessar login, redireciona para home
if ($page === 'login' && $authenticated) {
    $page = 'home';
}

// Caminho do arquivo view
$view_path = APP_PATH . '/views/' . $page . '.php';

// Verificar se arquivo existe
if (!file_exists($view_path)) {
    http_response_code(404);
    die('Página não encontrada: ' . htmlspecialchars($page));
}

// Renderizar view
require $view_path;
?>
