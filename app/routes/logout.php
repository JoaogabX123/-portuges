<?php
/**
 * ROTA: Logout
 * Endpoint para desautenticação de usuários
 * GET /app/routes/logout.php
 */

require_once __DIR__ . '/../config/config.php';

header(HEADER_JSON);

LogoutController::fazer_logout();
?>
