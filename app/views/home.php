<?php
/**
 * VIEW: Home (Dashboard)
 * Página principal com lista de questões
 */

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

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
</head>

<body>

    <header>
        <h1>+Português</h1>

        <div class="filtros">
            <input 
                type="text" 
                id="campo_busca" 
                placeholder="Pesquisar questão..."
                oninput="carregarQuestoes()"
            >

            <input 
                type="text" 
                id="filtro_genero" 
                placeholder="Filtrar por gênero"
                oninput="carregarQuestoes()"
            >

            <input 
                type="text" 
                id="filtro_subgenero" 
                placeholder="Filtrar por subgênero"
                oninput="carregarQuestoes()"
            >
        </div>

        <div class="usuario-menu">

            <button class="btn-usuario" type="button" onclick="window.location.href='./?page=configuracoes'">
                👤
            </button>

            <div class="dropdown-usuario" id="dropdownUsuario">

                <a href="./?page=configuracoes">
                    ⚙️ Configurações
                </a>

                <a href="./?page=info">
                    ℹ️ Info
                </a>

                <a href="./?page=termos">
                    📄 Termos de Uso
                </a>

                <hr>

                <a 
                    href="javascript:void(0);" 
                    onclick="fazerLogout()"
                    class="logout-option"
                >
                    🚪 Sair
                </a>

            </div>
        </div>
    </header>

    <main>

        <div class="aviso" id="aviso"></div>

        <div class="lista_de_questoes" id="lista">
            <div class="vazio">
                Carregando...
            </div>
        </div>

    </main>

    <!-- BOTÃO ADICIONAR -->
    <button class="btn-add" onclick="abrirModalTipo()">
        + Adicionar questão
    </button>

    <!-- BOTÃO TOPO -->
    <button 
        id="topo"
        onclick="window.scrollTo({ top:0, behavior:'smooth' })"
    >
        ↑
    </button>

    <!-- MODAL TIPO -->
    <div class="modal-overlay" id="modal_tipo">

        <div class="modal">

            <h2>Qual tipo de questão?</h2>

            <div class="modal-botoes">

                <a href="./?page=criacao_objetiva">
                    Objetiva
                </a>

                <a href="./?page=criacao_dissertativa">
                    Dissertativa
                </a>

            </div>

            <button 
                class="modal-fechar"
                onclick="fecharModalTipo()"
            >
                Cancelar
            </button>

        </div>
    </div>

    <!-- MODAL ENVIO -->
    <div class="modal-overlay" id="modal_envio">

        <div class="modal">

            <h2>Enviar Questão</h2>

            <div class="formulario">

                <div class="campo">
                    <label for="email_destinatario">
                        Email do destinatário:
                    </label>

                    <input 
                        type="email"
                        id="email_destinatario"
                        placeholder="exemplo@email.com"
                    >
                </div>

                <div class="campo">

                    <label for="descricao_envio">
                        Descrição do envio:
                    </label>

                    <textarea 
                        id="descricao_envio"
                        placeholder="Descreva o motivo do envio..."
                        style="height:80px;"
                    ></textarea>

                </div>

                <div class="campo">

                    <label for="questao_envio">
                        Questão:
                    </label>

                    <textarea 
                        id="questao_envio"
                        disabled
                        style="height:100px;"
                    ></textarea>

                </div>

            </div>

            <div 
                class="modal-botoes"
                style="margin-top:20px; gap:10px;"
            >

                <button 
                    class="btn salvar"
                    onclick="enviarQuestao()"
                >
                    Enviar
                </button>

                <button 
                    class="btn cancelar"
                    onclick="fecharModalEnvio()"
                >
                    Cancelar
                </button>

            </div>

        </div>
    </div>

    <!-- MODAL EXCLUSÃO -->
    <div class="modal-overlay" id="modal_confirmacao_excluir">

        <div class="modal">

            <h2>Confirmar Exclusão</h2>

            <p style="margin-bottom:20px; color:#666;">
                Tem certeza que deseja excluir esta questão?
                Esta ação não pode ser desfeita.
            </p>

            <div class="modal-botoes" style="gap:10px;">

                <button
                    class="btn excluir"
                    onclick="confirmarExcluir()"
                    style="flex:1;"
                >
                    Excluir
                </button>

                <button
                    class="btn cancelar"
                    onclick="fecharModalConfirmacao()"
                    style="flex:1;"
                >
                    Cancelar
                </button>

            </div>

        </div>
    </div>

    <script>

        let carregandoQuestoes = false;

        let questaoAtualParaExcluir = null;

        let questaoAtualParaEnviar = null;


        window.addEventListener('DOMContentLoaded', async () => {

            try {

                console.log('🔍 Verificando sessão...');

                const res = await fetch(
                    `${BASE_URL}app/routes/usuarios.php?acao=verificar_sessao`,
                    {
                        credentials: 'include'
                    }
                );

                if (!res.ok) {

                    window.location.href = './?page=login';
                    return;
                }

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

                carregarQuestoes();

                document.addEventListener('click', (e) => {

                    const dropdown =
                        document.getElementById('dropdownUsuario');

                    const btnUsuario =
                        document.querySelector('.btn-usuario');

                    if (
                        !dropdown.contains(e.target) &&
                        !btnUsuario.contains(e.target)
                    ) {
                        dropdown.classList.remove('ativo');
                    }

                });

            } catch (erro) {

                console.error(erro);

                window.location.href = './?page=login';
            }

        });


        async function carregarQuestoes() {

            if (carregandoQuestoes) return;

            carregandoQuestoes = true;

            try {

                const busca =
                    document.getElementById('campo_busca')
                    .value.trim();

                const genero =
                    document.getElementById('filtro_genero')
                    .value.trim();

                const subgenero =
                    document.getElementById('filtro_subgenero')
                    .value.trim();


                const params = new URLSearchParams();

                params.set('acao', 'listar');

                params.set('busca', busca);

                if (genero) {
                    params.set('genero', genero);
                }

                if (subgenero) {
                    params.set('subgenero', subgenero);
                }

                const url =
                    `${BASE_URL}app/routes/questoes.php?${params.toString()}`;

                const res = await fetch(url, {
                    credentials: 'include'
                });

                if (!res.ok) {

                    window.location.href = './?page=login';
                    return;
                }

                const resposta = await res.json();

                if (!resposta.ok) return;

                const questoes =
                    resposta.dados?.questoes || [];

                const lista =
                    document.getElementById('lista');


                if (!questoes.length) {

                    lista.innerHTML = `
                        <div class="vazio">
                            Nenhuma questão encontrada.
                            Adicione a primeira!
                        </div>
                    `;

                    return;
                }

                lista.innerHTML = questoes.map(q => `

                    <div class="questao-card">

                        <div 
                            class="questao-info"
                            onclick="window.location='./?page=questao_${q.tipo}&id=${encodeURIComponent(q.id)}'"
                        >

                            <span class="questao-titulo">
                                ${q.titulo || '(sem título)'}
                            </span>

                            <span class="tipo-badge">
                                ${q.tipo}
                            </span>

                            ${
                                q.status === 'rascunho'
                                ? '<span class="badge-rascunho">rascunho</span>'
                                : ''
                            }

                        </div>

                        <div class="genero-tags">

                            <button class="genero-btn">
                                ${q.genero || '-'}
                            </button>

                            ${
                                q.subgenero
                                ? `<span class="subgenero-text">${q.subgenero}</span>`
                                : ''
                            }

                        </div>

                        <div class="questao-acoes">

                            <button
                                class="btn btn-acoes enviar"
                                onclick="abrirModalEnvio(
                                    '${encodeURIComponent(q.id)}',
                                    '${q.titulo.replace(/'/g, "\\'")}'
                                )"
                            >
                                📤 Enviar
                            </button>

                            <button
                                class="btn btn-acoes excluir"
                                onclick="abrirModalConfirmacaoExcluir(
                                    '${encodeURIComponent(q.id)}'
                                )"
                            >
                                🗑️ Excluir
                            </button>

                        </div>

                    </div>

                `).join('');

            } catch (erro) {

                console.error(
                    'Erro ao carregar questões:',
                    erro
                );

            } finally {

                carregandoQuestoes = false;
            }
        }


        function toggleDropdownUsuario() {

            document
                .getElementById('dropdownUsuario')
                .classList.toggle('ativo');
        }


        function abrirModalTipo() {

            document
                .getElementById('modal_tipo')
                .classList.add('ativo');
        }


        function fecharModalTipo() {

            document
                .getElementById('modal_tipo')
                .classList.remove('ativo');
        }


        function abrirModalEnvio(questaoId, titulo) {

            questaoAtualParaEnviar = questaoId;

            document.getElementById('questao_envio').value =
                `[ID: ${decodeURIComponent(questaoId)}] ${decodeURIComponent(titulo)}`;

            document
                .getElementById('modal_envio')
                .classList.add('ativo');
        }


        function fecharModalEnvio() {

            document
                .getElementById('modal_envio')
                .classList.remove('ativo');

            document.getElementById('email_destinatario').value = '';

            document.getElementById('descricao_envio').value = '';

            document.getElementById('questao_envio').value = '';

            questaoAtualParaEnviar = null;
        }


        async function enviarQuestao() {

            const email =
                document.getElementById('email_destinatario')
                .value.trim();

            const descricao =
                document.getElementById('descricao_envio')
                .value.trim();


            if (!email) {

                alert('Digite um email válido');
                return;
            }

            if (!descricao) {

                alert('Digite uma descrição');
                return;
            }

            const aviso =
                document.getElementById('aviso');

            aviso.textContent =
                '✔ Questão enviada com sucesso para ' +
                email + '!';

            aviso.className = 'aviso sucesso';

            fecharModalEnvio();

            setTimeout(() => {

                aviso.className = 'aviso';

            }, 4000);

        }


        function abrirModalConfirmacaoExcluir(questaoId) {

            questaoAtualParaExcluir = questaoId;

            document
                .getElementById('modal_confirmacao_excluir')
                .classList.add('ativo');
        }


        function fecharModalConfirmacao() {

            document
                .getElementById('modal_confirmacao_excluir')
                .classList.remove('ativo');

            questaoAtualParaExcluir = null;
        }


        async function confirmarExcluir() {

            if (!questaoAtualParaExcluir) return;

            try {

                const res = await fetch(
                    `${BASE_URL}app/routes/questoes.php?acao=deletar`,
                    {
                        method: 'POST',

                        credentials: 'include',

                        headers: {
                            'Content-Type': 'application/json'
                        },

                        body: JSON.stringify({
                            id: questaoAtualParaExcluir
                        })
                    }
                );

                const resultado = await res.json();


                if (resultado.ok) {

                    const aviso =
                        document.getElementById('aviso');

                    aviso.textContent =
                        '🗑 Questão excluída com sucesso!';

                    aviso.className =
                        'aviso excluida';

                    fecharModalConfirmacao();

                    carregarQuestoes();

                    setTimeout(() => {

                        aviso.className = 'aviso';

                    }, 4000);

                } else {

                    alert(
                        'Erro ao excluir questão: ' +
                        (resultado.mensagem || 'Tente novamente')
                    );
                }

            } catch (erro) {

                console.error(erro);

                alert(
                    'Erro ao excluir questão.'
                );
            }
        }


        async function fazerLogout() {

            await fetch(
                `${BASE_URL}app/routes/logout.php`,
                {
                    credentials: 'include'
                }
            );

            window.location.href = './?page=login';
        }

    </script>

</body>
</html>