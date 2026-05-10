<?php
/**
 * CONTROLLER: LogoutController
 * Gerencia logout de usuários
 */

class LogoutController {
    /**
     * Fazer logout
     * GET /routes/logout.php
     */
    public static function fazer_logout() {
        try {
            // Sessão já foi iniciada em config.php
            session_unset();
            session_destroy();
            
            resposta_sucesso(null, 'Logout realizado com sucesso');
            
        } catch (Exception $e) {
            resposta_erro('Erro ao fazer logout', 500);
        }
    }
}
?>
