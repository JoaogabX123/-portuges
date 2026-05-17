<?php
/**
 * Entrada pública única para a API.
 * Mantém app/routes fora das URLs do frontend e funciona com DocumentRoot em /public.
 */

$rota = $_GET['rota'] ?? '';
$rotasPermitidas = [
    'login' => __DIR__ . '/../app/routes/login.php',
    'logout' => __DIR__ . '/../app/routes/logout.php',
    'usuarios' => __DIR__ . '/../app/routes/usuarios.php',
    'questoes' => __DIR__ . '/../app/routes/questoes.php',
];

if (!isset($rotasPermitidas[$rota])) {
    require_once __DIR__ . '/../app/config/config.php';
    resposta_erro('Rota de API não reconhecida', 404);
}

require $rotasPermitidas[$rota];
?>
