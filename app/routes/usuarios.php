<?php
/**
 * ROTA: Usuários
 * Endpoints para gerenciamento de usuários
 * GET  /app/routes/usuarios.php?acao=verificar_sessao
 * POST /app/routes/usuarios.php?acao=criar
 */

require_once __DIR__ . '/../config/config.php';

header(HEADER_JSON);

$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'verificar_sessao';

try {
    switch ($acao) {
        case 'verificar_sessao':
            SessaoController::verificar_sessao();
            break;
        
        case 'criar':
            SessaoController::criar_usuario();
            break;

        case 'atualizar_perfil':
            SessaoController::atualizar_perfil();
            break;

        case 'alterar_senha':
            SessaoController::alterar_senha();
            break;

        case 'salvar_recuperacao':
            SessaoController::salvar_recuperacao();
            break;
        
        default:
            resposta_erro('Ação não reconhecida', 400);
    }
} catch (Exception $e) {
    resposta_erro('Erro: ' . $e->getMessage(), 500);
}
?>
