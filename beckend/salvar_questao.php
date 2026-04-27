<?php
/**
 * SALVAR_QUESTAO.PHP
 * Cria ou atualiza questão com validação completa
 */

require 'config.php';
require 'helpers.php';

try {
    // Verificar autenticação
    verificarAutenticacao();
    
    $id = $_POST['id'] ?? '';
    $tipo = $_POST['tipo'] ?? 'objetiva';
    $acao = $_POST['acao'] ?? 'salvar';
    
    $erros = validarQuestao($_POST);
    if (!empty($erros)) {
        Resposta::validacao($erros);
    }
    
    $caminhoImagem = $_POST['imagem_atual'] ?? '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultado = Upload::validarESalvarImagem($_FILES['imagem']);
        
        if (isset($resultado['erro'])) {
            Resposta::erro($resultado['erro'], 400);
        }
        
        $caminhoImagem = $resultado['caminho'];
        
        if (!empty($_POST['imagem_atual']) && $_POST['imagem_atual'] !== $caminhoImagem) {
            Upload::deletarImagem($_POST['imagem_atual']);
        }
    }
    
    $status = ($acao === 'postar') ? 'publicada' : 'rascunho';
    
    $dadosQuestao = [
        'tipo' => $tipo,
        'status' => $status,
        'titulo' => $_POST['titulo'],
        'genero' => $_POST['genero'],
        'enunciado' => $_POST['enunciado'],
        'explicacao' => $_POST['explicacao'] ?? '',
        'especificacao' => $_POST['especificacao'] ?? '',
        'subgenero' => $_POST['subgenero'] ?? '',
        'imagem' => $caminhoImagem
    ];
    
    if ($tipo === 'objetiva') {
        $dadosQuestao['correta'] = $_POST['correta'];
        $dadosQuestao['alternativas'] = [
            'A' => $_POST['alt_A'],
            'B' => $_POST['alt_B'],
            'C' => $_POST['alt_C'],
            'D' => $_POST['alt_D'],
            'E' => $_POST['alt_E']
        ];
    }
    
    foreach ($dadosQuestao as $chave => &$valor) {
        if (is_string($valor)) {
            $valor = sanitizarTexto($valor);
        } elseif (is_array($valor) && $chave === 'alternativas') {
            foreach ($valor as &$alt) {
                $alt = sanitizarTexto($alt);
            }
        }
    }
    
    if (!empty($id)) {
        $questaoExistente = BancoQuestoes::encontrarPorId($id);
        if ($questaoExistente) {
            if (empty($caminhoImagem) && !empty($questaoExistente['imagem'])) {
                $dadosQuestao['imagem'] = $questaoExistente['imagem'];
            }
            $questaoAtualizada = BancoQuestoes::atualizar($id, $dadosQuestao);
            Resposta::sucesso(['id' => $questaoAtualizada['id']], 'Questão atualizada com sucesso');
        } else {
            $questaoNova = BancoQuestoes::adicionar($dadosQuestao);
            Resposta::sucesso(['id' => $questaoNova['id']], 'Questão salva com sucesso');
        }
    } else {
        $questaoNova = BancoQuestoes::adicionar($dadosQuestao);
        Resposta::sucesso(['id' => $questaoNova['id']], 'Questão criada com sucesso');
    }
} catch (Exception $e) {
    Resposta::erro('Erro ao salvar questão: ' . $e->getMessage(), 500);
}
?>