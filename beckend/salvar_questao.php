<?php
header('Content-Type: application/json');

$arquivoJson = 'questoes.json';
$questoes = file_exists($arquivoJson) ? json_decode(file_get_contents($arquivoJson), true) : [];

$id = $_POST['id'] ?? uniqid();
$tipo = $_POST['tipo'] ?? 'objetiva';
$acao = $_POST['acao'] ?? 'salvar'; // 'salvar' ou 'postar'

// Lógica de Upload de Imagem
$caminhoImagem = $_POST['imagem_atual'] ?? '';
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    if (!is_dir('uploads')) mkdir('uploads');
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeImg = uniqid() . "." . $ext;
    move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $nomeImg);
    $caminhoImagem = 'uploads/' . $nomeImg;
}

$novaQuestao = [
    'id' => $id,
    'tipo' => $tipo,
    'status' => ($acao === 'postar' ? 'publicada' : 'rascunho'),
    'titulo' => $_POST['titulo'] ?? '',
    'genero' => $_POST['genero'] ?? '',
    'enunciado' => $_POST['enunciado'] ?? '',
    'explicacao' => $_POST['explicacao'] ?? '',
    'especificacao' => $_POST['especificacao'] ?? '',
    'subgenero' => $_POST['subgenero'] ?? '',
    'imagem' => $caminhoImagem
];

if ($tipo === 'objetiva') {
    $novaQuestao['correta'] = $_POST['correta'] ?? '';
    $novaQuestao['alternativas'] = [
        'A' => $_POST['alt_A'] ?? '',
        'B' => $_POST['alt_B'] ?? '',
        'C' => $_POST['alt_C'] ?? '',
        'D' => $_POST['alt_D'] ?? '',
        'E' => $_POST['alt_E'] ?? '',
    ];
}

// Atualizar se existir, senão adicionar
$index = -1;
foreach ($questoes as $i => $q) {
    if ($q['id'] == $id) { $index = $i; break; }
}

if ($index !== -1) {
    $questoes[$index] = $novaQuestao;
} else {
    $questoes[] = $novaQuestao;
}

file_put_contents($arquivoJson, json_encode($questoes, JSON_PRETTY_PRINT));
echo json_encode(['ok' => true]);