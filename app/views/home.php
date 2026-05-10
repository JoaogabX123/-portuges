<?php
/**
 * VIEW: Home (Dashboard)
 * Página principal com lista de questões
 * GET /app/views/home.php
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
    <title>+Português - Dashboard</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>';</script>
</head>
<body>
    <header>
        <h1>+Português</h1>
        <input type="text" id="campo_busca" placeholder="Pesquisar questão..." oninput="carregarQuestoes()">
        <button class="btn-logout" onclick="fazerLogout()">Sair</button>
    </header>

    <main>
        <div class="aviso" id="aviso"></div>
        <div class="lista_de_questoes" id="lista">
            <div class="vazio">Carregando...</div>
        </div>
    </main>

    <button class="btn-add" onclick="abrirModalTipo()">+ Adicionar questão</button>
    <button id="topo" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>

    <!-- Modal de tipo -->
    <div class="modal-overlay" id="modal_tipo">
        <div class="modal">
            <h2>Qual tipo de questão?</h2>
            <div class="modal-botoes">
                <a href="./?page=criacao_objetiva">Objetiva</a>
                <a href="./?page=criacao_dissertativa">Dissertativa</a>
            </div>
            <button class="modal-fechar" onclick="fecharModalTipo()">Cancelar</button>
        </div>
    </div>

    <script>
        let carregandoQuestoes = false;

        window.addEventListener('DOMContentLoaded', async () => {
            try {
                console.log('🔍 Verificando sessão...');
                const res = await fetch(`${BASE_URL}app/routes/usuarios.php?acao=verificar_sessao`, { 
                    credentials: 'include' 
                });
                
                console.log('📡 Resposta da sessão:', res.status, res.ok);
                
                if (!res.ok) {
                    console.log('❌ Sessão inválida, redirecionando para login');
                    window.location.href = './?page=login';
                    return;
                }

                const dados = await res.json();
                console.log('✅ Sessão verificada:', dados);
                
                const params = new URLSearchParams(window.location.search);
                const msg = params.get('msg');
                const aviso = document.getElementById('aviso');
                
                if (msg === 'sucesso') {
                    aviso.textContent = '✔ Questão salva com sucesso!';
                    aviso.className = 'aviso sucesso';
                } else if (msg === 'excluida') {
                    aviso.textContent = '🗑 Questão excluída.';
                    aviso.className = 'aviso excluida';
                }

                console.log('📋 Carregando questões...');
                carregarQuestoes();
            } catch (e) {
                console.error('❌ Erro ao verificar sessão:', e);
                window.location.href = './?page=login';
            }
        });

        async function carregarQuestoes() {
            if (carregandoQuestoes) return;
            
            carregandoQuestoes = true;
            try {
                const busca = document.getElementById('campo_busca').value;
                const url = `${BASE_URL}app/routes/questoes.php?acao=listar&busca=${encodeURIComponent(busca)}`;
                
                console.log('🔗 Requisição:', url);
                const res = await fetch(url, { credentials: 'include' });
                
                console.log('📊 Status da resposta:', res.status);
                
                if (!res.ok) {
                    console.error('❌ Erro HTTP:', res.status);
                    window.location.href = './?page=login';
                    return;
                }

                const resposta = await res.json();
                console.log('✅ Questões carregadas:', resposta);
                
                if (!resposta.ok) {
                    console.error('❌ Erro na resposta:', resposta);
                    return;
                }

                const questoes = resposta.dados?.questoes || [];
                const lista = document.getElementById('lista');

                if (!questoes.length) {
                    lista.innerHTML = '<div class="vazio">Nenhuma questão encontrada. Adicione a primeira!</div>';
                    return;
                }

                lista.innerHTML = questoes.map(q => `
                    <div class="questao-card"
                         onclick="window.location='./?page=questao_${q.tipo}&id=${encodeURIComponent(q.id)}'">
                        <span>
                            ${q.titulo || '(sem título)'}
                            <span class="tipo-badge">${q.tipo}</span>
                            ${q.status === 'rascunho' ? '<span class="badge-rascunho">rascunho</span>' : ''}
                        </span>
                        <button class="genero-btn">${q.genero || '-'}</button>
                    </div>
                `).join('');
            } catch (e) {
                console.error('❌ Erro ao carregar questões:', e);
            } finally {
                carregandoQuestoes = false;
            }
        }

        function abrirModalTipo() {
            document.getElementById('modal_tipo').classList.add('ativo');
        }

        function fecharModalTipo() {
            document.getElementById('modal_tipo').classList.remove('ativo');
        }

        async function fazerLogout() {
            await fetch(`${BASE_URL}app/routes/logout.php`, { credentials: 'include' });
            window.location.href = './?page=login';
        }
    </script>
</body>
</html>
