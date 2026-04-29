<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>+Português</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; background-image: linear-gradient(30deg, cyan, rgb(35, 115, 235)); margin: 0; padding: 25px; min-height: 100vh; }
    .pagina { width: 1000px; margin: 0 auto; }
    header { background-color: rgba(0,0,0,0.8); color: rgb(239,249,255); padding: 20px; text-align: center; }
    header input { margin-top: 10px; outline: none; padding: 10px; width: 300px; }
    main { margin-top: 40px; padding-bottom: 60px; }
    .titulo_pagina { background-color: rgba(0,0,0,0.8); color: white; text-align: center; padding: 15px; border-radius: 8px; font-size: 20px; font-weight: bold; margin-bottom: 25px; }
    .formulario { background: rgb(250,253,255); padding: 25px; border-radius: 8px; }
    .tipo_questao { display: flex; gap: 10px; margin-bottom: 22px; }
    .tipo_questao button { flex: 1; padding: 10px; border: 2px solid #ccc; border-radius: 6px; background: white; font-size: 15px; cursor: pointer; font-family: Arial, Helvetica, sans-serif; transition: all 0.2s; }
    .tipo_questao button.ativo { border-color: #3182ce; background: #3182ce; color: white; font-weight: bold; }
    .campo { margin-bottom: 18px; }
    .campo label { display: block; font-weight: bold; margin-bottom: 6px; color: rgb(40,40,40); }
    .campo input[type="text"], .campo select, .campo textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; font-family: Arial, Helvetica, sans-serif; outline: none; }
    .campo input[type="text"]:focus, .campo select:focus, .campo textarea:focus { border-color: #3182ce; }
    .campo textarea { resize: none; }
    .linha_dupla { display: flex; gap: 20px; align-items: stretch; }
    .coluna_imagem, .coluna_direita { flex: 1; display: flex; flex-direction: column; }
    .area_imagem { border: 2px dashed #aaa; border-radius: 6px; flex: 1; min-height: 240px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; color: #777; background: rgb(240,245,250); position: relative; overflow: hidden; }
    .area_imagem:hover { border-color: #3182ce; color: #3182ce; }
    .area_imagem input[type="file"] { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .area_imagem img { max-width: 100%; max-height: 100%; border-radius: 4px; }
    .icone_upload { font-size: 36px; margin-bottom: 8px; }
    .caixa_alternativas { border: 2px solid #ccc; border-radius: 6px; padding: 15px; background: rgb(240,245,250); flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 10px; }
    .hint_radio { font-size: 12px; color: #888; margin: 0 0 2px 0; }
    .alternativa_item { display: flex; align-items: center; gap: 10px; }
    .alternativa_item input[type="radio"] { accent-color: #3182ce; width: 18px; height: 18px; cursor: pointer; flex-shrink: 0; }
    .alternativa_item input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; outline: none; }
    .alternativa_item input[type="text"]:focus { border-color: #3182ce; }
    .coluna_direita textarea { flex: 1; width: 100%; padding: 12px; border: 2px solid #ccc; border-radius: 6px; font-size: 15px; font-family: Arial, Helvetica, sans-serif; outline: none; resize: none; background: rgb(240,245,250); min-height: 240px; }
    .coluna_direita textarea:focus { border-color: #3182ce; }
    .secao_objetiva { display: block; }
    .secao_dissertativa { display: none; }
    .botoes { display: flex; gap: 10px; margin-top: 25px; justify-content: flex-end; }
    .botoes button { border: none; padding: 12px 22px; border-radius: 6px; cursor: pointer; font-size: 15px; font-family: Arial, Helvetica, sans-serif; }
    .excluir { background: rgb(200,50,50); color: white; margin-right: auto; }
    .excluir:hover { background: rgb(170,30,30); }
    .cancelar { background: rgb(60,60,60); color: white; }
    .cancelar:hover { background: rgb(40,40,40); }
    .salvar { background: #3182ce; color: white; }
    .salvar:hover { background: rgb(35,110,200); }
    .postar { background: rgb(60,141,248); color: white; }
    .postar:hover { background: rgb(40,120,220); }
</style>
</head>
<body>
<div class="pagina">
    <header>
        <h1>+Português</h1>
        <input type="text" placeholder="Pesquisar questão...">
    </header>

    <main>
        <div class="titulo_pagina">+ Editar questão</div>

        <div class="formulario" id="formulario">
            <p style="text-align:center;color:#888;">Carregando...</p>
        </div>
    </main>
</div>

<script>
    const BASE    = '../beckend';
    const id      = new URLSearchParams(window.location.search).get('id');
    let   questao = null;

    window.addEventListener('DOMContentLoaded', async () => {
        if (!id) { window.location.href = 'home_page.php'; return; }

        const res = await fetch(`${BASE}/buscar_questao.php?id=${encodeURIComponent(id)}`);
        if (!res.ok) { window.location.href = 'home_page.php'; return; }

        const resposta = await res.json();
        questao = resposta.dados || resposta;
        renderFormulario(questao);
    });

    function renderFormulario(q) {
        const alt  = q.alternativas || {};
        const tipo = q.tipo;

        const generoOpts = ['narrativo','argumentativo','descritivo','expositivo','instrucional']
            .map(g => `<option value="${g}" ${q.genero === g ? 'selected' : ''}>${g.charAt(0).toUpperCase()+g.slice(1)}</option>`)
            .join('');

        const imgSrc   = q.imagem ? `../beckend/${q.imagem}` : '';
        const imgStyle = q.imagem ? '' : 'display:none';
        const phStyle  = q.imagem ? 'display:none' : '';

        const secaoObj = `
            <div class="secao_objetiva" id="secao_objetiva">
                <div class="campo">
                    <label>Imagem e Alternativas</label>
                    <div class="linha_dupla">
                        <div class="coluna_imagem">
                            <div class="area_imagem">
                                <input type="file" accept="image/*" id="imagem"
                                       onchange="previewImagem(event,'preview_obj','placeholder_obj')">
                                <div id="placeholder_obj" style="${phStyle}">
                                    <div class="icone_upload">🖼️</div>
                                    <span>Clique para adicionar imagem</span>
                                </div>
                                <img id="preview_obj" src="${imgSrc}" alt="Preview" style="${imgStyle}">
                            </div>
                        </div>
                        <div class="coluna_direita">
                            <div class="caixa_alternativas">
                                <p class="hint_radio">Marque o radio da alternativa correta:</p>
                                ${['A','B','C','D','E'].map(l => `
                                <div class="alternativa_item">
                                    <input type="radio" name="correta" value="${l}" ${q.correta === l ? 'checked' : ''}>
                                    <input type="text" id="alt_${l}" value="${alt[l] || ''}" placeholder="${l}) Alternativa...">
                                </div>`).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

        const secaoDis = `
            <div class="secao_dissertativa" id="secao_dissertativa">
                <div class="campo">
                    <label>Imagem e Enunciado</label>
                    <div class="linha_dupla">
                        <div class="coluna_imagem">
                            <div class="area_imagem">
                                <input type="file" accept="image/*" id="imagem"
                                       onchange="previewImagem(event,'preview_dis','placeholder_dis')">
                                <div id="placeholder_dis" style="${phStyle}">
                                    <div class="icone_upload">🖼️</div>
                                    <span>Clique para adicionar imagem</span>
                                </div>
                                <img id="preview_dis" src="${imgSrc}" alt="Preview" style="${imgStyle}">
                            </div>
                        </div>
                        <div class="coluna_direita">
                            <textarea id="enunciado" placeholder="Digite o enunciado...">${q.enunciado || ''}</textarea>
                        </div>
                    </div>
                </div>
            </div>`;

        document.getElementById('formulario').innerHTML = `
            <div class="tipo_questao">
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
                <select id="genero">
                    <option value="" disabled>Selecione o gênero...</option>
                    ${generoOpts}
                </select>
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
                <button class="excluir" onclick="excluir()">Excluir</button>
                <button class="cancelar" onclick="history.back()">Cancelar</button>
                <button class="salvar" onclick="enviar('salvar')">Salvar</button>
                <button class="postar"  onclick="enviar('postar')">Postar</button>
            </div>
        `;

        // Aplica visibilidade correta das seções
        alternarTipo(tipo);
    }

    function alternarTipo(tipo) {
        const secaoObj = document.getElementById('secao_objetiva');
        const secaoDis = document.getElementById('secao_dissertativa');
        const btnObj   = document.getElementById('btn_objetiva');
        const btnDis   = document.getElementById('btn_dissertativa');
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
        const fd   = new FormData();

        fd.append('id',            id);
        fd.append('tipo',          tipo);
        fd.append('acao',          acao);
        fd.append('titulo',        document.getElementById('titulo').value);
        fd.append('genero',        document.getElementById('genero').value);
        fd.append('explicacao',    document.getElementById('explicacao').value);
        fd.append('especificacao', document.getElementById('especificacao').value);
        fd.append('subgenero',     document.getElementById('subgenero').value);
        fd.append('imagem_atual',  questao.imagem || '');

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

        const arquivo = document.getElementById('imagem')?.files[0];
        if (arquivo) fd.append('imagem', arquivo);

        const res  = await fetch(`${BASE}/salvar_questao.php`, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.ok) {
            window.location.href = 'home_page.php?msg=sucesso';
        } else {
            alert('Erro ao salvar. Tente novamente.');
        }
    }

    async function excluir() {
        if (!confirm('Tem certeza que deseja excluir esta questão?')) return;
        const res  = await fetch(`${BASE}/excluir_questao.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (data.ok) {
            window.location.href = 'home_page.php?msg=excluida';
        } else {
            alert('Erro ao excluir.');
        }
    }
</script>
</body>
</html>