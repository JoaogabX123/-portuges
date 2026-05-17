<?php
/**
 * VIEW: Criar Questão Dissertativa
 * GET /app/views/criacao_dissertativa.php
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
    <title>+ Adicionar Questão Dissertativa</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>'; const API_URL = '<?php echo API_URL; ?>'; const UPLOAD_URL = '<?php echo UPLOAD_URL; ?>';</script>
</head>
<body>
    <div class="container-form">
        <header>
            <h1>+Português</h1>
        </header>

        <main>
            <div class="titulo-pagina">+ Adicionar questão dissertativa</div>
            <div class="formulario">
                <div class="campo">
                    <label>Título</label>
                    <input type="text" id="titulo" placeholder="Digite o título da questão...">
                </div>

                <div class="campo">
                    <label>Gênero</label>
                    <input type="text" id="genero" placeholder="Digite o gênero da questão...">
                </div>

                <div class="campo">
                    <label>Imagem e Enunciado</label>
                    <div class="linha-dupla">
                        <div class="coluna-imagem">
                            <div class="area-imagem">
                                <input type="file" id="imagem" accept="image/*" onchange="previewImagem(event)">
                                <div id="placeholder-upload">
                                    <div class="icone-upload">🖼️</div>
                                    <span>Clique para adicionar imagem</span>
                                </div>
                                <img id="preview-img" alt="Preview" style="display: none;">
                            </div>
                        </div>
                        <div class="coluna-enunciado">
                            <textarea id="enunciado" placeholder="Digite o enunciado da questão dissertativa..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="campo">
                    <label>Explicação</label>
                    <textarea id="explicacao" rows="4" placeholder="Digite a explicação ou gabarito esperado..."></textarea>
                </div>

                <div class="campo">
                    <label>Especificação</label>
                    <input type="text" id="especificacao" placeholder="Digite a especificação...">
                </div>

                <div class="campo">
                    <label>Subgênero</label>
                    <input type="text" id="subgenero" placeholder="Digite o subgênero...">
                </div>

                <div class="botoes">
                    <button class="btn btn-cancelar" onclick="history.back()">Cancelar</button>
                    <button class="btn btn-salvar" onclick="enviar('salvar')">Salvar</button>
                    <button class="btn btn-postar" onclick="enviar('postar')">Postar</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        function previewImagem(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('preview-img');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    document.getElementById('placeholder-upload').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        async function enviar(acao) {
            const fd = new FormData();
            fd.append('tipo', 'dissertativa');
            fd.append('acao', acao);
            fd.append('titulo', document.getElementById('titulo').value);
            fd.append('genero', document.getElementById('genero').value);
            fd.append('enunciado', document.getElementById('enunciado').value);
            fd.append('explicacao', document.getElementById('explicacao').value);
            fd.append('especificacao', document.getElementById('especificacao').value);
            fd.append('subgenero', document.getElementById('subgenero').value);

            const arquivo = document.getElementById('imagem').files[0];
            if (arquivo) fd.append('imagem', arquivo);

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
                    let mensagem = data.erro || 'Erro desconhecido';
                    if (data.erros && Array.isArray(data.erros)) {
                        mensagem = 'Erros de validação:\n' + data.erros.join('\n');
                    }
                    alert(mensagem);
                }
            } catch (e) {
                alert('Erro de conexão: ' + e.message);
            }
        }
    </script>
</body>
</html>
