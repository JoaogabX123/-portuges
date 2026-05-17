<?php
/**
 * VIEW: Editar Questão
 * GET /app/views/editar_questao.php?id=X
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
    <title>+ Editar questão</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>'; const API_URL = '<?php echo API_URL; ?>'; const UPLOAD_URL = '<?php echo UPLOAD_URL; ?>';</script>
</head>
<body>
    <div class="container-form">
        <header>
            <h1>+Português</h1>
        </header>

        <main>
            <div class="titulo-pagina">+ Editar questão</div>

            <div class="formulario" id="formulario">
                <p style="text-align:center;color:#888;">Carregando...</p>
            </div>
        </main>
    </div>

    <script>
        const id = new URLSearchParams(window.location.search).get('id');
        let questao = null;

        window.addEventListener('DOMContentLoaded', async () => {
            if (!id) { 
                window.location.href = './?page=home';
                return;
            }

            try {
                const res = await fetch(`${API_URL}questoes&acao=buscar&id=${encodeURIComponent(id)}`, {
                    credentials: 'include'
                });
                
                if (!res.ok) { 
                    window.location.href = './?page=home'; 
                    return;
                }

                const resposta = await res.json();
                questao = resposta.dados || resposta;
                renderFormulario(questao);
            } catch (e) {
                console.error('Erro:', e);
                window.location.href = './?page=home';
            }
        });

        function renderFormulario(q) {
            const alt = q.alternativas || {};
            const tipo = q.tipo;

            const generoValor = q.genero || '';
            const imgSrc = q.imagem ? `${UPLOAD_URL}${q.imagem.replace(/^uploads\//, '')}` : '';
            const imgStyle = q.imagem ? '' : 'display:none';
            const phStyle = q.imagem ? 'display:none' : '';

            const secaoObj = `
                <div class="secao-objetiva" id="secao_objetiva">
                    <div class="campo">
                        <label>Imagem e Alternativas</label>
                        <div class="linha-dupla">
                            <div class="coluna-imagem">
                                <div class="area-imagem">
                                    <input type="file" accept="image/*" id="imagem_objetiva"
                                           onchange="previewImagem(event,'preview_obj','placeholder_obj')">
                                    <div id="placeholder_obj" style="${phStyle}">
                                        <div class="icone-upload">🖼️</div>
                                        <span>Clique para adicionar imagem</span>
                                    </div>
                                    <img id="preview_obj" src="${imgSrc}" alt="Preview" style="${imgStyle}">
                                </div>
                            </div>
                            <div class="coluna-direita">
                                <div class="caixa-alternativas">
                                    <p class="hint-radio">Marque o radio da alternativa correta:</p>
                                    ${['A','B','C','D','E'].map(l => `
                                    <div class="alternativa-item">
                                        <input type="radio" name="correta" value="${l}" ${q.correta === l ? 'checked' : ''}>
                                        <input type="text" id="alt_${l}" value="${alt[l] || ''}" placeholder="${l}) Alternativa...">
                                    </div>`).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            const secaoDis = `
                <div class="secao-dissertativa" id="secao_dissertativa">
                    <div class="campo">
                        <label>Imagem e Enunciado</label>
                        <div class="linha-dupla">
                            <div class="coluna-imagem">
                                <div class="area-imagem">
                                    <input type="file" accept="image/*" id="imagem_dissertativa"
                                           onchange="previewImagem(event,'preview_dis','placeholder_dis')">
                                    <div id="placeholder_dis" style="${phStyle}">
                                        <div class="icone-upload">🖼️</div>
                                        <span>Clique para adicionar imagem</span>
                                    </div>
                                    <img id="preview_dis" src="${imgSrc}" alt="Preview" style="${imgStyle}">
                                </div>
                            </div>
                            <div class="coluna-direita">
                                <textarea id="enunciado" placeholder="Digite o enunciado...">${q.enunciado || ''}</textarea>
                            </div>
                        </div>
                    </div>
                </div>`;

            document.getElementById('formulario').innerHTML = `
                <div class="tipo-questao">
                    <button class="${tipo === 'objetiva' ? 'ativo' : ''}" id="btn_objetiva"
                            onclick="alternarTipo('objetiva')">Objetiva</button>
                    <button class="${tipo === 'dissertativa' ? 'ativo' : ''}" id="btn_dissertativa"
                            onclick="alternarTipo('dissertativa')">Dissertativa</button>
                </div>

                <div class="campo">
                    <label>Título</label>
                    <input type="text" id="titulo" value="${q.titulo || ''}" placeholder="Digite o título da questão...">
                </div>

                <div class="campo">
                    <label>Gênero</label>
                    <input type="text" id="genero" value="${generoValor}" placeholder="Digite o gênero da questão...">
                </div>

                ${secaoObj}
                ${secaoDis}

                <div class="campo">
                    <label>Enunciado (objetiva)</label>
                    <textarea id="enunciado_obj" rows="4"
                              placeholder="Enunciado...">${tipo === 'objetiva' ? (q.enunciado || '') : ''}</textarea>
                </div>

                <div class="campo">
                    <label>Explicação</label>
                    <textarea id="explicacao" rows="4"
                              placeholder="Explicação...">${q.explicacao || ''}</textarea>
                </div>

                <div class="campo">
                    <label>Especificação</label>
                    <input type="text" id="especificacao" value="${q.especificacao || ''}" placeholder="Especificação...">
                </div>

                <div class="campo">
                    <label>Subgênero</label>
                    <input type="text" id="subgenero" value="${q.subgenero || ''}" placeholder="Subgênero...">
                </div>

                <div class="botoes">
                    <button class="btn btn-excluir" onclick="abrirModalConfirmacaoExcluir()">Excluir</button>
                    <button class="btn btn-cancelar" onclick="history.back()">Cancelar</button>
                    <button class="btn btn-salvar" onclick="enviar('salvar')">Salvar</button>
                    <button class="btn btn-postar"  onclick="enviar('postar')">Postar</button>
                </div>
            `;

            alternarTipo(tipo);
        }

        function alternarTipo(tipo) {
            const secaoObj = document.getElementById('secao_objetiva');
            const secaoDis = document.getElementById('secao_dissertativa');
            const btnObj = document.getElementById('btn_objetiva');
            const btnDis = document.getElementById('btn_dissertativa');
            
            if (!secaoObj) return;

            if (tipo === 'objetiva') {
                secaoObj.style.display = 'block';
                secaoDis.style.display = 'none';
                btnObj.classList.add('ativo');
                btnDis.classList.remove('ativo');
                questao.tipo = 'objetiva';
            } else {
                secaoObj.style.display = 'none';
                secaoDis.style.display = 'block';
                btnDis.classList.add('ativo');
                btnObj.classList.remove('ativo');
                questao.tipo = 'dissertativa';
            }
        }

        function previewImagem(event, previewId, placeholderId) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                    document.getElementById(placeholderId).style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        async function enviar(acao) {
            const tipo = questao.tipo;
            const fd = new FormData();

            fd.append('id', id);
            fd.append('tipo', tipo);
            fd.append('acao', acao);
            fd.append('titulo', document.getElementById('titulo').value);
            fd.append('genero', document.getElementById('genero').value);
            fd.append('explicacao', document.getElementById('explicacao').value);
            fd.append('especificacao', document.getElementById('especificacao').value);
            fd.append('subgenero', document.getElementById('subgenero').value);
            fd.append('imagem_atual', questao.imagem || '');

            if (tipo === 'objetiva') {
                fd.append('enunciado', document.getElementById('enunciado_obj')?.value || '');
                const correta = document.querySelector('input[name="correta"]:checked');
                fd.append('correta', correta ? correta.value : '');
                ['A','B','C','D','E'].forEach(l => {
                    fd.append('alt_' + l, document.getElementById('alt_' + l)?.value || '');
                });
            } else {
                fd.append('enunciado', document.getElementById('enunciado')?.value || '');
            }

            // Selecionar input correto baseado no tipo
            const idImagem = tipo === 'objetiva' ? 'imagem_objetiva' : 'imagem_dissertativa';
            const arquivo = document.getElementById(idImagem)?.files[0];
            if (arquivo) {
                console.log('Arquivo selecionado:', arquivo.name);
                fd.append('imagem', arquivo);
            }

            try {
                const res = await fetch(`${API_URL}questoes&acao=salvar`, {
                    method: 'POST',
                    credentials: 'include',
                    body: fd
                });
                
                const data = await res.json();

                if (data.ok) {
                    window.location.href = './?page=home&msg=sucesso';
                } else {
                    alert('Erro ao salvar. Tente novamente.');
                }
            } catch (e) {
                alert('Erro de conexão: ' + e.message);
            }
        }

        function abrirModalConfirmacaoExcluir() {
            document.getElementById('modal_confirmacao_editar').classList.add('ativo');
        }

        function fecharModalConfirmacaoEditar() {
            document.getElementById('modal_confirmacao_editar').classList.remove('ativo');
        }

        async function confirmarExcluirEditar() {
            try {
                const res = await fetch(`${API_URL}questoes&acao=deletar`, {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const data = await res.json();

                if (data.ok) {
                    window.location.href = './?page=home&msg=excluida';
                } else {
                    alert('Erro ao excluir.');
                }
            } catch (e) {
                alert('Erro de conexão: ' + e.message);
            }
        }
    </script>

    <div class="modal-overlay" id="modal_confirmacao_editar">
        <div class="modal">
            <h2>Confirmar Exclusão</h2>
            <p style="margin-bottom: 20px; color: #666;">Tem certeza que deseja excluir esta questão? Esta ação não pode ser desfeita.</p>
            <div class="modal-botoes" style="gap: 10px;">
                <button class="btn excluir" onclick="confirmarExcluirEditar()" style="flex: 1;">Excluir</button>
                <button class="btn cancelar" onclick="fecharModalConfirmacaoEditar()" style="flex: 1;">Cancelar</button>
            </div>
        </div>
    </div>
</body>
</html>
