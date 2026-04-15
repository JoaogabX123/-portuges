<?php
/**
 * EXCLUIR_QUESTAO.PHP
 * Deleta questão por ID
 */

require 'config.php';
require 'helpers.php';

try {
    $dados = obterDadosJSON();
    $id = $dados['id'] ?? '';
    
    if (empty($id)) {
        Resposta::erro('ID da questão é obrigatório', 400);
    }
    
    $questao = BancoQuestoes::encontrarPorId($id);
    
    if (!$questao) {
        Resposta::erro('Questão não encontrada', 404);
    }
    
    if (!empty($questao['imagem'])) {
        Upload::deletarImagem($questao['imagem']);
    }
    
    BancoQuestoes::deletar($id);
    
    Resposta::sucesso(null, 'Questão deletada com sucesso');
} catch (Exception $e) {
    Resposta::erro('Erro ao excluir questão: ' . $e->getMessage(), 500);
}
?>