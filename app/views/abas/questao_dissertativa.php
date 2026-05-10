<?php
/**
 * VIEW: Aba Questão Dissertativa
 * GET /public/?page=questao_dissertativa&id=X
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
    <title>+Português - Questão</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>';</script>
</head>
<body>
    <header>
        <h1>+Português</h1>
    </header>

    <main>
        <div class="questao-aberta" id="conteudo">
            <p style="text-align:center;color:#888;">Carregando...</p>
        </div>
    </main>

    <button class="btn-add" onclick="window.location='./?page=home'">+ Adicionar questão</button>

    <script>
        const id = new URLSearchParams(window.location.search).get('id');

        window.addEventListener('DOMContentLoaded', async () => {
            if (!id) { 
                window.location.href = './?page=home';
                return;
            }

            try {
                const res = await fetch(`${BASE_URL}app/routes/questoes.php?acao=buscar&id=${encodeURIComponent(id)}`, {
                    credentials: 'include'
                });
                
                if (!res.ok) { 
                    window.location.href = './?page=home';
                    return;
                }

                const resposta = await res.json();
                const q = resposta.dados || resposta;
                
                if (q.tipo !== 'dissertativa') { 
                    window.location.href = `./?page=questao_objetiva&id=${id}`;
                    return;
                }

                const sub = [q.especificacao, q.subgenero].filter(Boolean).join(' / ');
                const imgTag = q.imagem
                    ? `<img src="${BASE_URL}public/${q.imagem}" alt="Imagem da questão" style="max-width:100%;margin:10px 0;">`
                    : '';

                document.getElementById('conteudo').innerHTML = `
                    <div class="topo-questao">
                        <div class="titulo">${q.titulo || '(sem título)'}</div>
                        <button class="genero-btn">${q.genero || '-'}</button>
                    </div>
                    <div class="subgenero">${sub}</div>
                    <div class="enunciado">${imgTag}${q.enunciado || ''}</div>
                    <div class="resposta">
                        <textarea placeholder="Digite sua resposta aqui..." readonly></textarea>
                    </div>
                    <div class="explicacao"><b>Explicação:</b><br><br>${q.explicacao || ''}</div>
                    <div class="botoes">
                        <button class="btn btn-voltar" onclick="window.location='./?page=home'">Voltar</button>
                        <button class="btn btn-editar" onclick="window.location='./?page=editar_questao&id=${encodeURIComponent(q.id)}'">Editar</button>
                        <button class="btn btn-copiar" onclick="copiar()">Copiar</button>
                    </div>
                `;
            } catch (e) {
                console.error('Erro:', e);
                window.location.href = './?page=home';
            }
        });

        function copiar() {
            navigator.clipboard.writeText(document.getElementById('conteudo').innerText)
                .then(() => alert('Questão copiada!'));
        }
    </script>
</body>
</html>
