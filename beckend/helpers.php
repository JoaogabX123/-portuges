<?php
/**
 * Funções Auxiliares e Classes Reutilizáveis
 */

require_once __DIR__ . '/config.php';

// ============================================
// CLASSE: Resposta
// Padroniza todas as respostas da API
// ============================================
class Resposta {
    /**
     * Retorna resposta de sucesso em JSON
     */
    public static function sucesso($dados = null, $mensagem = '') {
        header(HEADER_JSON);
        http_response_code(200);
        
        $resposta = ['ok' => true];
        if (!empty($mensagem)) {
            $resposta['mensagem'] = $mensagem;
        }
        if ($dados !== null) {
            $resposta['dados'] = $dados;
        }
        
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Retorna resposta de erro em JSON
     */
    public static function erro($mensagem, $codigo = 400) {
        header(HEADER_JSON);
        http_response_code($codigo);
        
        $resposta = [
            'ok' => false,
            'erro' => $mensagem
        ];
        
        if (DEBUG_MODE) {
            $resposta['debug'] = [
                'codigo_http' => $codigo,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
        
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Retorna erro de validação
     */
    public static function validacao($erros) {
        header(HEADER_JSON);
        http_response_code(422);
        
        echo json_encode([
            'ok' => false,
            'erro' => 'Erro de validação',
            'erros' => is_array($erros) ? $erros : [$erros]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

// ============================================
// CLASSE: BancoQuestoes
// Gerencia todas as operações com questões
// ============================================
class BancoQuestoes {
    /**
     * Obter todas as questões do arquivo JSON
     */
    public static function obter() {
        $arquivo = BANCO_ARQUIVO;
        
        if (!file_exists($arquivo)) {
            return [];
        }
        
        $conteudo = @file_get_contents($arquivo);
        if ($conteudo === false) {
            throw new Exception('Erro ao ler arquivo de questões');
        }
        
        $questoes = json_decode($conteudo, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
        }
        
        return $questoes ?? [];
    }

    /**
     * Salvar questões no arquivo JSON
     */
    public static function salvar($questoes) {
        $arquivo = BANCO_ARQUIVO;
        $dirPai = dirname($arquivo);
        
        if (!is_dir($dirPai)) {
            if (!mkdir($dirPai, 0755, true)) {
                throw new Exception('Erro ao criar diretório de questões');
            }
        }
        
        $json = json_encode($questoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($json === false) {
            throw new Exception('Erro ao codificar JSON');
        }
        
        if (@file_put_contents($arquivo, $json) === false) {
            throw new Exception('Erro ao salvar questões');
        }
    }

    /**
     * Encontrar questão por ID
     */
    public static function encontrarPorId($id) {
        $questoes = self::obter();
        
        foreach ($questoes as $questao) {
            if ($questao['id'] === $id) {
                return $questao;
            }
        }
        
        return null;
    }

    /**
     * Adicionar nova questão
     */
    public static function adicionar($questao) {
        $questoes = self::obter();
        $questoes[] = $questao;
        self::salvar($questoes);
        
        return $questao;
    }

    /**
     * Atualizar questão existente
     */
    public static function atualizar($id, $dados) {
        $questoes = self::obter();
        
        foreach ($questoes as $indice => $questao) {
            if ($questao['id'] === $id) {
                // Mesclar dados mantendo campos não enviados
                $questoes[$indice] = array_merge($questao, $dados);
                self::salvar($questoes);
                return $questoes[$indice];
            }
        }
        
        return null;
    }

    /**
     * Deletar questão por ID
     */
    public static function deletar($id) {
        $questoes = self::obter();
        $questoes = array_filter($questoes, function($q) use ($id) {
            return $q['id'] !== $id;
        });
        
        self::salvar(array_values($questoes));
        return true;
    }

    /**
     * Buscar questões por termo (título ou enunciado)
     */
    public static function buscar($termo) {
        $questoes = self::obter();
        
        return array_filter($questoes, function($q) use ($termo) {
            $titulo = strtolower($q['titulo'] ?? '');
            $enunciado = strtolower($q['enunciado'] ?? '');
            $termo = strtolower($termo);
            
            return stripos($titulo, $termo) !== false || stripos($enunciado, $termo) !== false;
        });
    }

    /**
     * Listar questões com paginação opcional
     */
    public static function listar($filtros = []) {
        $questoes = self::obter();
        
        // Filtrar por tipo
        if (!empty($filtros['tipo'])) {
            $questoes = array_filter($questoes, function($q) use ($filtros) {
                return $q['tipo'] === $filtros['tipo'];
            });
        }
        
        // Filtrar por status
        if (!empty($filtros['status'])) {
            $questoes = array_filter($questoes, function($q) use ($filtros) {
                return $q['status'] === $filtros['status'];
            });
        }
        
        // Filtrar por gênero
        if (!empty($filtros['genero'])) {
            $questoes = array_filter($questoes, function($q) use ($filtros) {
                return $q['genero'] === $filtros['genero'];
            });
        }
        
        return array_values($questoes);
    }

    /**
     * Obter estatísticas
     */
    public static function estatisticas() {
        $questoes = self::obter();
        
        return [
            'total' => count($questoes),
            'objetivas' => count(array_filter($questoes, fn($q) => $q['tipo'] === 'objetiva')),
            'dissertativas' => count(array_filter($questoes, fn($q) => $q['tipo'] === 'dissertativa')),
            'publicadas' => count(array_filter($questoes, fn($q) => $q['status'] === 'publicada')),
            'rascunhos' => count(array_filter($questoes, fn($q) => $q['status'] === 'rascunho'))
        ];
    }
}

// ============================================
// CLASSE: Upload
// Gerencia upload de arquivos
// ============================================
class Upload {
    /**
     * Validar e salvar arquivo de imagem
     */
    public static function validarESalvarImagem($arquivo) {
        // Verificar se arquivo foi enviado
        if (empty($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Nenhuma imagem foi enviada ou ocorreu erro no upload'];
        }
        
        // Verificar tamanho
        if ($arquivo['size'] > TAMANHO_MAXIMO_UPLOAD) {
            return ['erro' => 'Arquivo muito grande. Máximo: 5MB'];
        }
        
        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipoMime, TIPOS_PERMITIDOS)) {
            return ['erro' => 'Tipo de arquivo não permitido. Use: JPG, PNG, WebP, GIF'];
        }
        
        // Criar diretório se não existir
        if (!is_dir(PASTA_UPLOADS)) {
            if (!@mkdir(PASTA_UPLOADS, 0755, true)) {
                return ['erro' => 'Erro ao criar diretório de uploads'];
            }
        }
        
        // Gerar nome único para arquivo
        $extensao = self::obterExtensaoSegura($tipoMime);
        $nomeArquivo = uniqid('img_') . '.' . $extensao;
        $caminhoCompleto = PASTA_UPLOADS . '/' . $nomeArquivo;
        
        // Mover arquivo
        if (!@move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
            return ['erro' => 'Erro ao salvar imagem no servidor'];
        }
        
        return [
            'sucesso' => true,
            'caminho' => 'uploads/' . $nomeArquivo,
            'nomeOriginal' => $arquivo['name']
        ];
    }

    /**
     * Obter extensão segura baseada no MIME type
     */
    private static function obterExtensaoSegura($tipoMime) {
        $extensoes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];
        
        return $extensoes[$tipoMime] ?? 'jpg';
    }

    /**
     * Deletar arquivo de imagem
     */
    public static function deletarImagem($caminho) {
        if (empty($caminho)) {
            return false;
        }
        
        $caminhoCompleto = __DIR__ . '/' . $caminho;
        
        if (file_exists($caminhoCompleto)) {
            return @unlink($caminhoCompleto);
        }
        
        return false;
    }
}

// ============================================
// FUNÇÕES: Validação e Sanitização
// ============================================

/**
 * Sanitizar texto (remover tags HTML, espaços extras)
 */
function sanitizarTexto($texto) {
    if (!is_string($texto)) {
        return '';
    }
    
    $texto = trim($texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    
    return $texto;
}

/**
 * Validar estrutura de questão
 */
function validarQuestao($dados) {
    $erros = [];
    
    // Validações obrigatórias
    if (empty(trim($dados['titulo'] ?? ''))) {
        $erros[] = 'Título é obrigatório';
    }
    
    if (empty(trim($dados['enunciado'] ?? ''))) {
        $erros[] = 'Enunciado é obrigatório';
    }
    
    $tipo = $dados['tipo'] ?? '';
    if (!in_array($tipo, array_keys(TIPOS_QUESTAO))) {
        $erros[] = 'Tipo de questão inválido';
    }
    
    if (!in_array($dados['status'] ?? 'rascunho', array_keys(STATUS_QUESTAO))) {
        $erros[] = 'Status inválido';
    }
    
    if (!in_array($dados['genero'] ?? '', array_keys(GENEROS_TEXTO))) {
        $erros[] = 'Gênero de texto inválido';
    }
    
    // Validações específicas por tipo
    if ($tipo === 'objetiva') {
        if (empty($dados['correta'] ?? '')) {
            $erros[] = 'Alternativa correta é obrigatória para questões objetivas';
        } elseif (!in_array($dados['correta'], ALTERNATIVAS)) {
            $erros[] = 'Alternativa correta inválida';
        }
        
        foreach (ALTERNATIVAS as $alt) {
            $chave = 'alt_' . $alt;
            if (empty(trim($dados[$chave] ?? ''))) {
                $erros[] = "Alternativa $alt é obrigatória";
            }
        }
    }
    
    return $erros;
}

/**
 * Verificar autenticação do usuário
 */
function verificarAutenticacao() {
    session_start();
    
    if (empty($_SESSION['usuario_id'] ?? null)) {
        Resposta::erro('Você não está autenticado', 401);
    }
    
    return $_SESSION['usuario_id'];
}

/**
 * Obter dados JSON da requisição POST
 */
function obterDadosJSON() {
    $json = file_get_contents('php://input');
    $dados = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        Resposta::erro('JSON inválido: ' . json_last_error_msg(), 400);
    }
    
    return $dados ?? [];
}

/**
 * Gerar ID único para questão
 */
function gerarId() {
    return uniqid('q_', true);
}

/**
 * Construir objeto questão
 */
function construirQuestao($dados) {
    $questao = [
        'id' => $dados['id'] ?? gerarId(),
        'tipo' => sanitizarTexto($dados['tipo']),
        'status' => sanitizarTexto($dados['status'] ?? 'rascunho'),
        'titulo' => sanitizarTexto($dados['titulo']),
        'genero' => sanitizarTexto($dados['genero']),
        'enunciado' => sanitizarTexto($dados['enunciado']),
        'explicacao' => sanitizarTexto($dados['explicacao'] ?? ''),
        'especificacao' => sanitizarTexto($dados['especificacao'] ?? ''),
        'subgenero' => sanitizarTexto($dados['subgenero'] ?? ''),
        'imagem' => $dados['imagem'] ?? ''
    ];
    
    if ($dados['tipo'] === 'objetiva') {
        $questao['correta'] = sanitizarTexto($dados['correta']);
        $questao['alternativas'] = [
            'A' => sanitizarTexto($dados['alt_A']),
            'B' => sanitizarTexto($dados['alt_B']),
            'C' => sanitizarTexto($dados['alt_C']),
            'D' => sanitizarTexto($dados['alt_D']),
            'E' => sanitizarTexto($dados['alt_E'])
        ];
    }
    
    return $questao;
}
?>
