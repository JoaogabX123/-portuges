<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    exit;
}
echo json_encode(['ok' => true]);