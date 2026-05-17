<?php
/**
 * VIEW: Configurações do Usuário
 * GET /app/views/configuracoes.php
 */

// Sessão já foi iniciada em config.php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ./?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>+Português – Configurações</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>'; const API_URL = '<?php echo API_URL; ?>'; const UPLOAD_URL = '<?php echo UPLOAD_URL; ?>';</script>
    <style>
        /* ── Configurações ── */
        .config-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
            font-family: Arial, Helvetica, sans-serif;
        }

        .config-container header {
            background-color: rgba(0,0,0,0.85);
            color: #eff9ff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .config-container header h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .btn-voltar-header {
            background: rgba(82,202,250,0.15);
            border: 1px solid rgba(82,202,250,0.4);
            color: #52caff;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }

        .btn-voltar-header:hover {
            background: rgba(82,202,250,0.3);
            color: #fff;
        }

        .config-main {
            max-width: 680px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .config-titulo {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a2a4a;
            margin-bottom: 6px;
        }

        .config-subtitulo {
            font-size: 0.9rem;
            color: #6b7a99;
            margin-bottom: 32px;
        }

        .config-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(30,60,120,0.08);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .config-card-header {
            background: linear-gradient(90deg, #1e3c78, #2373eb);
            color: #fff;
            padding: 14px 22px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .config-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .campo-config {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .campo-config label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1a2a4a;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .campo-config input {
            border: 1.5px solid #dde3f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.95rem;
            color: #1a2a4a;
            background: #f7f9fd;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .campo-config input:focus {
            border-color: #2373eb;
            box-shadow: 0 0 0 3px rgba(35,115,235,0.12);
            background: #fff;
        }

        .campo-config input:disabled {
            background: #eef1f8;
            color: #8a97b5;
            cursor: not-allowed;
        }

        .campo-config .hint {
            font-size: 0.78rem;
            color: #8a97b5;
        }

        .config-botoes {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 4px;
        }

        .btn-config-cancelar {
            background: transparent;
            border: 1.5px solid #dde3f0;
            color: #6b7a99;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-config-cancelar:hover {
            border-color: #aab0c8;
            color: #1a2a4a;
        }

        .btn-config-salvar {
            background: linear-gradient(135deg, #1e3c78, #2373eb);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: 9px 22px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-config-salvar:hover {
            opacity: 0.88;
            transform: translateY(-1px);
        }

        .btn-config-salvar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .recuperacao-opcoes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .recuperacao-opcao {
            border: 2px solid #dde3f0;
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .recuperacao-opcao:hover {
            border-color: #2373eb;
            background: #f0f6ff;
        }

        .recuperacao-opcao.selecionada {
            border-color: #2373eb;
            background: #eef5ff;
        }

        .recuperacao-opcao input[type="radio"] {
            margin-top: 2px;
            accent-color: #2373eb;
        }

        .recuperacao-opcao .opcao-titulo {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1a2a4a;
        }

        .recuperacao-opcao .opcao-desc {
            font-size: 0.78rem;
            color: #6b7a99;
            margin-top: 2px;
        }

        .aviso-config {
            display: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .aviso-config.sucesso {
            display: block;
            background: #e6f9ee;
            color: #1a7a4a;
            border: 1px solid #a3e6c4;
        }

        .aviso-config.erro {
            display: block;
            background: #fff0f0;
            color: #c0392b;
            border: 1px solid #f5b7b1;
        }

        .senha-toggle {
            position: relative;
        }

        .senha-toggle input {
            padding-right: 44px;
        }

        .senha-toggle button {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #6b7a99;
            padding: 0;
        }

        @media (max-width: 500px) {
            .recuperacao-opcoes { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="config-container">
    <header>
        <a href="./?page=home" class="btn-voltar-header">← Voltar</a>
        <h1>+Português</h1>
    </header>

    <main class="config-main">
        <div class="config-titulo">⚙️ Configurações</div>
        <div class="config-subtitulo">Gerencie seus dados de perfil, senha e preferências de segurança.</div>

        <div class="aviso-config" id="aviso_config"></div>

        <!-- Dados do perfil -->
        <div class="config-card">
            <div class="config-card-header">👤 Dados do Usuário</div>
            <div class="config-card-body">
                <div class="campo-config">
                    <label>Nome Completo</label>
                    <input type="text" id="cfg_nome" placeholder="Carregando...">
                </div>
                <div class="campo-config">
                    <label>E-mail</label>
                    <input type="email" id="cfg_email" placeholder="Carregando...">
                    <span class="hint">O e-mail é usado para login e recuperação de senha.</span>
                </div>
                <div class="config-botoes">
                    <button class="btn-config-cancelar" onclick="recarregarDados()">Cancelar</button>
                    <button class="btn-config-salvar" id="btn_salvar_perfil" onclick="salvarPerfil()">Salvar Perfil</button>
                </div>
            </div>
        </div>

        <!-- Alterar senha -->
        <div class="config-card">
            <div class="config-card-header">🔒 Alterar Senha</div>
            <div class="config-card-body">
                <div class="campo-config">
                    <label>Senha Atual</label>
                    <div class="senha-toggle">
                        <input type="password" id="cfg_senha_atual" placeholder="Digite sua senha atual">
                        <button onclick="toggleSenha('cfg_senha_atual', this)" type="button" title="Mostrar/ocultar">👁️</button>
                    </div>
                </div>
                <div class="campo-config">
                    <label>Nova Senha</label>
                    <div class="senha-toggle">
                        <input type="password" id="cfg_nova_senha" placeholder="Mínimo 8 caracteres">
                        <button onclick="toggleSenha('cfg_nova_senha', this)" type="button" title="Mostrar/ocultar">👁️</button>
                    </div>
                </div>
                <div class="campo-config">
                    <label>Confirmar Nova Senha</label>
                    <div class="senha-toggle">
                        <input type="password" id="cfg_confirmar_senha" placeholder="Repita a nova senha">
                        <button onclick="toggleSenha('cfg_confirmar_senha', this)" type="button" title="Mostrar/ocultar">👁️</button>
                    </div>
                </div>
                <div class="config-botoes">
                    <button class="btn-config-cancelar" onclick="limparCamposSenha()">Cancelar</button>
                    <button class="btn-config-salvar" onclick="alterarSenha()">Alterar Senha</button>
                </div>
            </div>
        </div>

        <!-- Recuperação de senha -->
        <div class="config-card">
            <div class="config-card-header">🔑 Forma de Recuperar Senha</div>
            <div class="config-card-body">
                <p style="font-size:0.88rem;color:#6b7a99;margin-bottom:4px;">
                    Escolha como você deseja recuperar o acesso à sua conta caso esqueça a senha:
                </p>
                <div class="recuperacao-opcoes">
                    <label class="recuperacao-opcao" id="opcao_email_label">
                        <input type="radio" name="recuperacao" value="email" checked onchange="selecionarOpcao('email')">
                        <div>
                            <div class="opcao-titulo">📧 Por e-mail</div>
                            <div class="opcao-desc">Enviaremos um link de redefinição para seu e-mail cadastrado.</div>
                        </div>
                    </label>
                    <label class="recuperacao-opcao" id="opcao_perguntas_label">
                        <input type="radio" name="recuperacao" value="perguntas" onchange="selecionarOpcao('perguntas')">
                        <div>
                            <div class="opcao-titulo">❓ Perguntas secretas</div>
                            <div class="opcao-desc">Responda perguntas de segurança previamente configuradas.</div>
                        </div>
                    </label>
                </div>

                <!-- Pergunta secreta (aparece ao selecionar "perguntas") -->
                <div id="secao_pergunta" style="display:none;margin-top:12px;display:none;flex-direction:column;gap:12px;">
                    <div class="campo-config">
                        <label>Pergunta de Segurança</label>
                        <select id="cfg_pergunta" style="border:1.5px solid #dde3f0;border-radius:8px;padding:10px 14px;font-size:0.9rem;color:#1a2a4a;background:#f7f9fd;outline:none;">
                            <option value="">Selecione uma pergunta...</option>
                            <option value="animal">Qual era o nome do seu primeiro animal de estimação?</option>
                            <option value="escola">Qual foi o nome da sua primeira escola?</option>
                            <option value="cidade">Em qual cidade você nasceu?</option>
                            <option value="mae">Qual é o nome de solteira da sua mãe?</option>
                        </select>
                    </div>
                    <div class="campo-config">
                        <label>Resposta</label>
                        <input type="text" id="cfg_resposta_secreta" placeholder="Digite sua resposta...">
                        <span class="hint">A resposta é sensível a maiúsculas e minúsculas.</span>
                    </div>
                </div>

                <div class="config-botoes" style="margin-top:8px;">
                    <button class="btn-config-salvar" onclick="salvarRecuperacao()">Salvar Preferência</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let dadosUsuario = null;

    window.addEventListener('DOMContentLoaded', async () => {
        await recarregarDados();
        // Marca a opção selecionada visualmente ao carregar
        selecionarOpcao('email');
    });

    async function recarregarDados() {
        try {
            const res = await fetch(`${API_URL}usuarios&acao=verificar_sessao`, {
                credentials: 'include'
            });
            if (!res.ok) { window.location.href = './?page=login'; return; }
            const resposta = await res.json();
            dadosUsuario = resposta.dados?.usuario || resposta.usuario || resposta.dados || resposta;
            document.getElementById('cfg_nome').value  = dadosUsuario.nome  || '';
            document.getElementById('cfg_email').value = dadosUsuario.email || '';
        } catch (e) {
            mostrarAviso('Erro ao carregar dados do usuário.', 'erro');
        }
    }

    async function salvarPerfil() {
        const nome  = document.getElementById('cfg_nome').value.trim();
        const email = document.getElementById('cfg_email').value.trim();

        if (!nome || !email) {
            mostrarAviso('Preencha nome e e-mail antes de salvar.', 'erro');
            return;
        }

        const btn = document.getElementById('btn_salvar_perfil');
        btn.disabled = true;
        btn.textContent = 'Salvando...';

        try {
            const res = await fetch(`${API_URL}usuarios&acao=atualizar_perfil`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome, email })
            });
            const data = await res.json();
            if (data.ok) {
                mostrarAviso('✔ Perfil atualizado com sucesso!', 'sucesso');
            } else {
                mostrarAviso(data.erro || 'Erro ao salvar perfil.', 'erro');
            }
        } catch (e) {
            mostrarAviso('Erro de conexão: ' + e.message, 'erro');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Salvar Perfil';
        }
    }

    async function alterarSenha() {
        const atual     = document.getElementById('cfg_senha_atual').value;
        const nova      = document.getElementById('cfg_nova_senha').value;
        const confirmar = document.getElementById('cfg_confirmar_senha').value;

        if (!atual || !nova || !confirmar) {
            mostrarAviso('Preencha todos os campos de senha.', 'erro');
            return;
        }
        if (nova.length < 8) {
            mostrarAviso('A nova senha precisa ter pelo menos 8 caracteres.', 'erro');
            return;
        }
        if (nova !== confirmar) {
            mostrarAviso('A nova senha e a confirmação não coincidem.', 'erro');
            return;
        }

        try {
            const res = await fetch(`${API_URL}usuarios&acao=alterar_senha`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ senha_atual: atual, nova_senha: nova })
            });
            const data = await res.json();
            if (data.ok) {
                mostrarAviso('✔ Senha alterada com sucesso!', 'sucesso');
                limparCamposSenha();
            } else {
                mostrarAviso(data.erro || 'Erro ao alterar senha.', 'erro');
            }
        } catch (e) {
            mostrarAviso('Erro de conexão: ' + e.message, 'erro');
        }
    }

    function limparCamposSenha() {
        ['cfg_senha_atual','cfg_nova_senha','cfg_confirmar_senha'].forEach(id => {
            document.getElementById(id).value = '';
        });
    }

    function toggleSenha(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }

    function selecionarOpcao(valor) {
        document.getElementById('opcao_email_label').classList.toggle('selecionada', valor === 'email');
        document.getElementById('opcao_perguntas_label').classList.toggle('selecionada', valor === 'perguntas');
        const secao = document.getElementById('secao_pergunta');
        secao.style.display = valor === 'perguntas' ? 'flex' : 'none';
    }

    async function salvarRecuperacao() {
        const metodo = document.querySelector('input[name="recuperacao"]:checked')?.value || 'email';
        const pergunta = document.getElementById('cfg_pergunta')?.value || '';
        const resposta  = document.getElementById('cfg_resposta_secreta')?.value.trim() || '';

        if (metodo === 'perguntas' && (!pergunta || !resposta)) {
            mostrarAviso('Selecione uma pergunta e informe a resposta.', 'erro');
            return;
        }

        try {
            const res = await fetch(`${API_URL}usuarios&acao=salvar_recuperacao`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ metodo, pergunta, resposta })
            });
            const data = await res.json();
            if (data.ok) {
                mostrarAviso('✔ Preferência de recuperação salva!', 'sucesso');
            } else {
                mostrarAviso(data.erro || 'Erro ao salvar preferência.', 'erro');
            }
        } catch (e) {
            mostrarAviso('Erro de conexão: ' + e.message, 'erro');
        }
    }

    function mostrarAviso(msg, tipo) {
        const el = document.getElementById('aviso_config');
        el.textContent = msg;
        el.className = 'aviso-config ' + tipo;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { el.className = 'aviso-config'; }, 5000);
    }
</script>
</body>
</html>
