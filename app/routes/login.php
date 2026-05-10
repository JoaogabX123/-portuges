<?php
/**
 * ROTA: Login
 * Endpoint para autenticação de usuários
 * POST /app/routes/login.php
 */

require_once __DIR__ . '/../config/config.php';

header(HEADER_JSON);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    LoginController::fazer_login();
} else {
    resposta_erro('Método não permitido', 405);
}
?>
