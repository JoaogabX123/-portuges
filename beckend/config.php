<?php
/**
 * Arquivo de Configuração Global
 * Constantes e configurações do projeto
 */

// Ambiente
define('AMBIENTE', 'desenvolvimento'); // 'desenvolvimento' ou 'producao'
define('DEBUG_MODE', AMBIENTE === 'desenvolvimento');

// Caminho dos arquivos
define('BANCO_ARQUIVO', __DIR__ . '/questoes.json');
define('PASTA_UPLOADS', __DIR__ . '/uploads');

// Configurações de Upload
define('TAMANHO_MAXIMO_UPLOAD', 5 * 1024 * 1024); // 5MB
define('TIPOS_PERMITIDOS', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('EXTENSOES_PERMITIDAS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// Configurações de Sessão
define('TEMPO_SESSAO', 3600); // 1 hora em segundos

// Configurações de Resposta
define('HEADER_JSON', 'Content-Type: application/json; charset=utf-8');

// Gêneros de Texto (para categorização)
define('GENEROS_TEXTO', [
    'narrativo' => 'Narrativo',
    'argumentativo' => 'Argumentativo',
    'descritivo' => 'Descritivo',
    'expositivo' => 'Expositivo',
    'instrucional' => 'Instrucional'
]);

// Status de Questão
define('STATUS_QUESTAO', [
    'rascunho' => 'Rascunho',
    'publicada' => 'Publicada'
]);

// Tipos de Questão
define('TIPOS_QUESTAO', [
    'objetiva' => 'Objetiva (Múltipla Escolha)',
    'dissertativa' => 'Dissertativa'
]);

// Alternativas para questões objetivas
define('ALTERNATIVAS', ['A', 'B', 'C', 'D', 'E']);
?>
