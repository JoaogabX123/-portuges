<?php
/**
 * DEBUG ROUTER
 * Acesse: http://localhost/Projeto%20+Portugues/public/debug.php
 */

require_once __DIR__ . '/../app/config/config.php';

echo "<h2>Debug Router</h2>";
echo "<hr>";

echo "<h3>Variáveis de Caminho</h3>";
echo "APP_ROOT: " . APP_ROOT . "<br>";
echo "APP_PATH: " . APP_PATH . "<br>";
echo "PUBLIC_PATH: " . PUBLIC_PATH . "<br>";
echo "<br>";

echo "<h3>GET Parameters</h3>";
echo "page: " . ($_GET['page'] ?? 'não definido') . "<br>";
echo "<br>";

echo "<h3>Session</h3>";
echo "usuario_id: " . ($_SESSION['usuario_id'] ?? 'não definido') . "<br>";
echo "authenticated: " . (isset($_SESSION['usuario_id']) ? 'sim' : 'não') . "<br>";
echo "<br>";

// Simular o router
$page = isset($_GET['page']) ? sanitizarTexto($_GET['page']) : null;
$authenticated = isset($_SESSION['usuario_id']);

if ($page && !$authenticated && !in_array($page, ['login', 'signup'])) {
    $page = 'login';
}

if (!$page) {
    $page = $authenticated ? 'home' : 'index';
}

if ($page === 'login' && $authenticated) {
    $page = 'home';
}

echo "<h3>Resultado</h3>";
echo "page final: " . $page . "<br>";
echo "<br>";

$view_path = APP_PATH . '/views/' . $page . '.php';
echo "view_path: " . $view_path . "<br>";
echo "arquivo existe? " . (file_exists($view_path) ? "✅ SIM" : "❌ NÃO") . "<br>";

if (file_exists($view_path)) {
    echo "<br><b>Incluindo arquivo...</b><br>";
    include $view_path;
}

?>
