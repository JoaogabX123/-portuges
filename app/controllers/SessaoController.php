<?php
/**
 * CONTROLLER: SessaoController
 * Gerencia verificacao de sessao e dados do usuario logado.
 */

class SessaoController {
    /**
     * GET /routes/usuarios.php?acao=verificar_sessao
     */
    public static function verificar_sessao() {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                resposta_erro('Nao autenticado', 401);
            }

            $usuario_id = (int) $_SESSION['usuario_id'];
            $usuario = Usuario::buscarPorId($usuario_id);

            resposta_sucesso([
                'usuario_id' => $usuario_id,
                'usuario_email' => $usuario['email'] ?? ($_SESSION['usuario_email'] ?? ''),
                'usuario' => $usuario,
                'tempo_sessao' => time() - ($_SESSION['login_time'] ?? time())
            ], 'Usuario autenticado');
        } catch (Exception $e) {
            resposta_erro('Erro ao verificar sessao: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /routes/usuarios.php?acao=criar
     */
    public static function criar_usuario() {
        try {
            $dados = obterDadosJSON();
            $resultado = Usuario::criar($dados);

            if (!$resultado['sucesso']) {
                if (isset($resultado['erros'])) {
                    resposta_validacao($resultado['erros']);
                }
                resposta_erro($resultado['erro'], 400);
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

    /**
     * POST /routes/usuarios.php?acao=atualizar_perfil
     */
    public static function atualizar_perfil() {
        try {
            $id = verificarAutenticacao();
            $dados = obterDadosJSON();

            $nome = trim($dados['nome'] ?? '');
            $email = trim($dados['email'] ?? '');

            if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                resposta_validacao(['Informe um nome e um email valido']);
            }

            Usuario::atualizarPerfil($id, $nome, $email);

            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;

            resposta_sucesso(['usuario' => Usuario::buscarPorId($id)], 'Perfil atualizado com sucesso');
        } catch (Exception $e) {
            resposta_erro('Erro ao atualizar perfil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /routes/usuarios.php?acao=alterar_senha
     */
    public static function alterar_senha() {
        try {
            $id = verificarAutenticacao();
            $dados = obterDadosJSON();

            Usuario::alterarSenha(
                $id,
                $dados['senha_atual'] ?? '',
                $dados['nova_senha'] ?? ''
            );

            resposta_sucesso(null, 'Senha alterada com sucesso');
        } catch (Exception $e) {
            resposta_erro('Erro ao alterar senha: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /routes/usuarios.php?acao=salvar_recuperacao
     */
    public static function salvar_recuperacao() {
        try {
            verificarAutenticacao();
            $dados = obterDadosJSON();

            $metodo = $dados['metodo'] ?? 'email';
            if (!in_array($metodo, ['email', 'perguntas'], true)) {
                resposta_validacao(['Metodo de recuperacao invalido']);
            }

            if ($metodo === 'perguntas' && (empty($dados['pergunta']) || empty($dados['resposta']))) {
                resposta_validacao(['Selecione uma pergunta e informe a resposta']);
            }

            $_SESSION['recuperacao'] = [
                'metodo' => $metodo,
                'pergunta' => $dados['pergunta'] ?? ''
            ];

            resposta_sucesso(null, 'Preferencia de recuperacao salva');
        } catch (Exception $e) {
            resposta_erro('Erro ao salvar recuperacao: ' . $e->getMessage(), 500);
        }
    }
}
?>
