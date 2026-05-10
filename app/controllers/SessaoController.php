<?php
/**
 * CONTROLLER: SessaoController
 * Gerencia verificação de sessão
 */

class SessaoController {
    /**
     * Verificar sessão ativa
     * GET /routes/usuarios.php?acao=verificar_sessao
     */
    public static function verificar_sessao() {
        try {
            // Sessão já foi iniciada em config.php - não chamar session_start() novamente
            
            if (!isset($_SESSION['usuario_id'])) {
                resposta_erro('Não autenticado', 401);
            }
            
            $usuario_id = $_SESSION['usuario_id'];
            $usuario_email = $_SESSION['usuario_email'] ?? 'desconhecido';
            $tempo_sessao = time() - ($_SESSION['login_time'] ?? 0);
            
            resposta_sucesso([
                'usuario_id' => $usuario_id,
                'usuario_email' => $usuario_email,
                'tempo_sessao' => $tempo_sessao
            ], 'Usuário autenticado');
            
        } catch (Exception $e) {
            resposta_erro('Erro ao verificar sessão', 500);
        }
    }
    
    /**
     * Criar novo usuário (Signup)
     * POST /routes/usuarios.php?acao=criar
     */
    public static function criar_usuario() {
        try {
            $dados = obterDadosJSON();
            
            $resultado = Usuario::criar($dados);
            
            if (!$resultado['sucesso']) {
                if (isset($resultado['erros'])) {
                    resposta_validacao($resultado['erros']);
                } else {
                    resposta_erro($resultado['erro'], 400);
                }
            }
            
            resposta_sucesso([
                'id' => $resultado['id'],
                'email' => $resultado['email'],
                'nome' => $resultado['nome']
            ], 'Cadastro realizado com sucesso');
            
        } catch (Exception $e) {
            resposta_erro('Erro no servidor: ' . $e->getMessage(), 500);
        }
    }
}
?>
