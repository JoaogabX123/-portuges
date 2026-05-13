<?php
/**
 * VIEW: Informações sobre o +Português
 * GET /app/views/info.php
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
    <title>+Português – Informações</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>';</script>
    <style>
        /* ── Info page ── herda o visual da landing (index.php) ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(30deg, cyan, rgb(35, 115, 235));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
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

        header h1 { font-size: 1.6rem; flex: 1; }

        .btn-voltar-header {
            background: rgba(82,202,250,0.15);
            border: 1px solid rgba(82,202,250,0.4);
            color: #52caff;
            border-radius: 6px;
            padding: 7px 16px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }

        .btn-voltar-header:hover {
            background: rgba(82,202,250,0.3);
            color: #fff;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 48px 20px 80px;
            gap: 32px;
        }

        /* Hero */
        .info-hero {
            text-align: center;
            max-width: 600px;
        }

        .info-hero h2 {
            font-size: 2.4rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 12px rgba(0,0,0,0.22);
            margin-bottom: 12px;
        }

        .info-hero p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.88);
            line-height: 1.75;
        }

        /* Cards grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }

        .info-card {
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 16px;
            padding: 26px 22px;
            color: #fff;
            transition: transform 0.2s, background 0.2s;
        }

        .info-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.08);
        }

        .info-card .icone {
            font-size: 2rem;
            margin-bottom: 12px;
            display: block;
        }

        .info-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-card p {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.82);
            line-height: 1.65;
        }

        /* Seção sobre */
        .info-sobre {
            background: rgba(0, 0, 0, 0.50);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.20);
            border-radius: 18px;
            padding: 32px 36px;
            max-width: 720px;
            width: 100%;
            color: #fff;
        }

        .info-sobre h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-sobre p {
            font-size: 0.93rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .info-sobre p:last-child { margin-bottom: 0; }

        .versao-badge {
            display: inline-block;
            background: rgba(82,202,250,0.25);
            border: 1px solid rgba(82,202,250,0.5);
            color: #c0efff;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* Termos link */
        .info-termos-link {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.75);
            text-align: center;
        }

        .info-termos-link a {
            color: #c0efff;
            text-decoration: underline;
            transition: color 0.2s;
        }

        .info-termos-link a:hover { color: #fff; }

        footer {
            background-color: rgba(0,0,0,0.8);
            color: #aaa;
            text-align: center;
            padding: 16px;
            font-size: 13px;
        }

        @media (max-width: 500px) {
            .info-hero h2 { font-size: 1.7rem; }
            .info-sobre { padding: 22px 18px; }
        }
    </style>
</head>
<body>
    <header>
        <a href="./?page=home" class="btn-voltar-header">← Voltar</a>
        <h1>+Português</h1>
    </header>

    <main>
        <!-- Hero -->
        <div class="info-hero">
            <h2>ℹ️ Sobre o +Português</h2>
            <p>Tudo o que você precisa saber sobre a plataforma, suas funcionalidades e como ela pode ajudar no seu trabalho com questões de Língua Portuguesa.</p>
        </div>

        <!-- Funcionalidades em cards -->
        <div class="info-grid">
            <div class="info-card">
                <span class="icone">📝</span>
                <h3>Questões Dissertativas</h3>
                <p>Crie questões abertas com enunciado, imagem de apoio e gabarito esperado, ideais para avaliar a produção textual dos alunos.</p>
            </div>
            <div class="info-card">
                <span class="icone">☑️</span>
                <h3>Questões Objetivas</h3>
                <p>Monte questões de múltipla escolha com até 5 alternativas (A–E), gabarito e explicação detalhada da resposta correta.</p>
            </div>
            <div class="info-card">
                <span class="icone">🗂️</span>
                <h3>Organização por Gênero</h3>
                <p>Filtre e organize suas questões por gênero textual e subgênero, facilitando a busca e a montagem de atividades temáticas.</p>
            </div>
            <div class="info-card">
                <span class="icone">📤</span>
                <h3>Compartilhamento</h3>
                <p>Envie questões para outros professores ou colegas diretamente pelo sistema, agilizando a colaboração e a troca de materiais.</p>
            </div>
            <div class="info-card">
                <span class="icone">✏️</span>
                <h3>Edição Completa</h3>
                <p>Edite qualquer questão a qualquer momento — tipo, conteúdo, imagem, alternativas e explicação — sem perder o histórico.</p>
            </div>
            <div class="info-card">
                <span class="icone">🔒</span>
                <h3>Acesso Seguro</h3>
                <p>Cada professor tem sua própria conta protegida. Suas questões são privadas e acessíveis apenas por você.</p>
            </div>
        </div>

        <!-- Sobre o sistema -->
        <div class="info-sobre">
            <h2>🎯 O que é o +Português? <span class="versao-badge">v1.0</span></h2>
            <p>
                O <strong>+Português</strong> é um banco de questões digital desenvolvido especialmente para professores
                de Língua Portuguesa. O sistema permite criar, organizar, editar e compartilhar questões dissertativas
                e objetivas de forma prática e centralizada.
            </p>
            <p>
                A plataforma foi pensada para reduzir o tempo gasto na elaboração de provas e atividades, oferecendo
                uma interface intuitiva e um fluxo simples: crie a questão, salve como rascunho ou poste diretamente,
                e encontre-a facilmente depois com os filtros de busca.
            </p>
            <p>
                Todas as questões ficam associadas à sua conta e podem ser acessadas de qualquer dispositivo com
                navegador, sem necessidade de instalação.
            </p>
        </div>

        <div class="info-termos-link">
            Ao utilizar o +Português, você concorda com nossos <a href="./?page=termos">Termos de Uso</a>.
        </div>
    </main>

    <footer>
        <p>&copy; 2024 +Português. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
