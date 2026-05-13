<?php
/**
 * VIEW: Termos de Uso
 * GET /app/views/termos.php
 * Acessível tanto para usuários logados quanto para visitantes (landing)
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>+Português – Termos de Uso</title>
    <style>
        /* ── Termos de uso — mesmo estilo da landing (index.php) ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(30deg, cyan, rgb(35, 115, 235));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: rgba(0,0,0,0.8);
            color: #eff9ff;
            padding: 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header h1 { font-size: 2rem; }

        .nav-botoes {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            display: flex;
            gap: 10px;
        }

        .nav-botoes a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .btn-voltar {
            background-color: rgb(82,202,250);
            color: #000;
        }

        .btn-voltar:hover {
            background-color: rgb(60,141,248);
            color: white;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px 20px 80px;
            gap: 28px;
        }

        /* Título da página */
        .termos-titulo {
            text-align: center;
            max-width: 680px;
        }

        .termos-titulo h2 {
            font-size: 2.6rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.25);
            margin-bottom: 10px;
        }

        .termos-titulo p {
            font-size: 1rem;
            color: rgba(255,255,255,0.82);
            line-height: 1.7;
        }

        /* Card principal de conteúdo */
        .termos-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 18px;
            padding: 40px 44px;
            max-width: 820px;
            width: 100%;
            color: #fff;
        }

        .termos-secao {
            margin-bottom: 30px;
        }

        .termos-secao:last-child { margin-bottom: 0; }

        .termos-secao h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .termos-secao p,
        .termos-secao li {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.92);
            line-height: 1.8;
        }

        .termos-secao ul {
            padding-left: 20px;
            margin-top: 6px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .termos-secao li { list-style: disc; }

        .destaque {
            background: rgba(0, 70, 120, 0.35);
            border-left: 3px solid rgba(82,202,250,0.85);
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            margin-top: 10px;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.95);
            line-height: 1.7;
        }

        /* Data de atualização */
        .termos-data {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.55);
            text-align: right;
            margin-top: 20px;
            font-style: italic;
        }

        /* Botão de aceite */
        .termos-aceite {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            max-width: 820px;
            width: 100%;
        }

        .termos-aceite p {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.78);
            text-align: center;
        }

        .termos-aceite .botoes {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .termos-aceite .botoes a {
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            transition: all 0.2s;
            box-shadow: 0 4px 18px rgba(0,0,0,0.3);
        }

        .btn-aceitar {
            background-color: #fff;
            color: rgb(35,115,235);
        }

        .btn-aceitar:hover {
            background-color: rgb(82,202,250);
            color: #000;
            transform: translateY(-2px);
        }

        .btn-recusar {
            background-color: rgba(0,0,0,0.35);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.4);
        }

        .btn-recusar:hover {
            background-color: rgba(0,0,0,0.55);
            transform: translateY(-2px);
        }

        footer {
            background-color: rgba(0,0,0,0.8);
            color: #aaa;
            text-align: center;
            padding: 18px;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .termos-card { padding: 24px 18px; }
            .termos-titulo h2 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <header>
        <h1>+Português</h1>
        <div class="nav-botoes">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="./?page=home" class="btn-voltar">← Voltar</a>
            <?php else: ?>
                <a href="./?page=login" class="btn-voltar">Entrar</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="termos-titulo">
            <h2>📄 Termos de Uso</h2>
            <p>Leia atentamente os termos e condições de uso da plataforma <strong>+Português</strong> antes de utilizar os serviços.</p>
        </div>

        <div class="termos-card">

            <div class="termos-secao">
                <h3>1. 🎯 Sobre a Plataforma</h3>
                <p>O <strong>+Português</strong> é um sistema de banco de questões voltado para professores de Língua Portuguesa. A plataforma permite criar, armazenar, organizar, editar e compartilhar questões dissertativas e objetivas de forma digital e centralizada.</p>
                <div class="destaque">
                    Ao criar uma conta e utilizar o sistema, o usuário declara ter lido, entendido e concordado com todos os termos descritos neste documento.
                </div>
            </div>

            <div class="termos-secao">
                <h3>2. 👤 Cadastro e Responsabilidades do Usuário</h3>
                <ul>
                    <li>O acesso à plataforma requer cadastro com nome, e-mail válido e senha.</li>
                    <li>Cada usuário é responsável pela veracidade e atualização dos seus dados cadastrais.</li>
                    <li>O usuário é inteiramente responsável pela guarda e segurança de sua senha de acesso.</li>
                    <li>É proibido compartilhar credenciais de acesso com terceiros.</li>
                    <li>Atividades suspeitas devem ser reportadas imediatamente à administração do sistema.</li>
                </ul>
            </div>

            <div class="termos-secao">
                <h3>3. 📝 Conteúdo Criado pelo Usuário</h3>
                <p>Todo o conteúdo inserido na plataforma — questões, enunciados, imagens, explicações e alternativas — é de responsabilidade exclusiva do usuário que o criou.</p>
                <ul>
                    <li>O usuário garante que possui os direitos necessários sobre o conteúdo que publica.</li>
                    <li>É vedada a inserção de conteúdo ofensivo, discriminatório, ilegal ou que viole direitos de terceiros.</li>
                    <li>Imagens utilizadas devem respeitar os direitos autorais aplicáveis.</li>
                    <li>O sistema pode remover conteúdo que viole estes termos sem aviso prévio.</li>
                </ul>
            </div>

            <div class="termos-secao">
                <h3>4. 🔒 Privacidade e Proteção de Dados</h3>
                <p>O +Português coleta apenas os dados necessários para o funcionamento do sistema (nome, e-mail e senha criptografada). As informações não são compartilhadas com terceiros sem consentimento expresso do usuário, exceto por obrigação legal.</p>
                <ul>
                    <li>Os dados são armazenados de forma segura com criptografia adequada.</li>
                    <li>O usuário pode solicitar a exclusão de sua conta e respectivos dados a qualquer momento.</li>
                    <li>Cookies são utilizados apenas para manutenção de sessão autenticada.</li>
                </ul>
            </div>

            <div class="termos-secao">
                <h3>5. 🚫 Uso Indevido</h3>
                <p>São consideradas práticas proibidas e passíveis de suspensão ou exclusão de conta:</p>
                <ul>
                    <li>Tentativas de acesso não autorizado a contas de outros usuários.</li>
                    <li>Uso de bots, scripts ou automações para manipular o sistema.</li>
                    <li>Inserção de conteúdo gerado com fins de spam ou desestabilização da plataforma.</li>
                    <li>Qualquer ação que comprometa a segurança ou a integridade dos dados de outros usuários.</li>
                </ul>
            </div>

            <div class="termos-secao">
                <h3>6. ✏️ Alterações nos Termos</h3>
                <p>O +Português reserva-se o direito de modificar estes Termos de Uso a qualquer momento. Alterações relevantes serão comunicadas por e-mail ou mediante aviso na plataforma. O uso continuado após as alterações implica na aceitação dos novos termos.</p>
            </div>

            <div class="termos-secao">
                <h3>7. ⚖️ Limitação de Responsabilidade</h3>
                <p>O +Português não se responsabiliza por perdas de dados decorrentes de falhas técnicas, uso indevido da plataforma pelo próprio usuário, ou eventos fora do controle da administração do sistema. Recomendamos que o usuário mantenha cópias de segurança do seu conteúdo quando necessário.</p>
            </div>

            <div class="termos-secao">
                <h3>8. 📬 Contato</h3>
                <p>Em caso de dúvidas, solicitações ou denúncias relacionadas a estes termos, entre em contato com a administração da plataforma pelo e-mail disponibilizado no rodapé da página ou diretamente pela interface do sistema.</p>
            </div>

            <div class="termos-data">Última atualização: dezembro de 2024</div>
        </div>

        <!-- Aceite (exibido apenas para não-logados) -->
        <?php if (!isset($_SESSION['usuario_id'])): ?>
        <div class="termos-aceite">
            <p>Ao criar uma conta, você concorda automaticamente com os termos acima.</p>
            <div class="botoes">
                <a href="./?page=signup" class="btn-aceitar">Aceitar e Criar Conta</a>
                <a href="./" class="btn-recusar">Voltar ao Início</a>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 +Português. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
