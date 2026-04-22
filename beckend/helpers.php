<?php
/**
 * Funções Auxiliares e Classes Reutilizáveis
 * Integrado com MySQLi
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../database/config.php';

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
// Integrada com MySQLi
// ============================================
class BancoQuestoes {
    private static $conexao = null;
    
    /**
     * Obter conexão com banco de dados
     */
    public static function getConexao() {
        global $conexao;
        if ($conexao->connect_error) {
            throw new Exception('Erro de conexão com banco de dados: ' . $conexao->connect_error);
        }
        return $conexao;
    }

    /**
     * Encontrar questão por ID
     */
    public static function encontrarPorId($id) {
        global $conexao;
        
        $stmt = $conexao->prepare("SELECT * FROM questoes WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return null;
        }
        
        $questao = $result->fetch_assoc();
        $stmt->close();
        
        // Carregar alternativas se for questão objetiva
        if ($questao['tipo'] === 'objetiva') {
            $questao['alternativas'] = self::obterAlternativas($questao['id']);
            $questao['correta'] = $questao['resposta_correta'];
        }
        
        return $questao;
    }

    /**
     * Obter alternativas de uma questão objetiva
     */
    private static function obterAlternativas($idQuestao) {
        global $conexao;
        
        $alternativas = [];
        
        $stmt = $conexao->prepare("SELECT alternativa, texto FROM alternativas_objetivas WHERE id_questao = ? ORDER BY alternativa ASC");
        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $idQuestao);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $alternativas[$row['alternativa']] = $row['texto'];
        }
        
        $stmt->close();
        
        return $alternativas;
    }

    /**
     * Adicionar nova questão
     */
    public static function adicionar($questao) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            $tipo = $questao['tipo'];
            $status = $questao['status'];
            $titulo = $questao['titulo'];
            $genero = $questao['genero'];
            $subgenero = $questao['subgenero'] ?? null;
            $especificacao = $questao['especificacao'] ?? null;
            $enunciado = $questao['enunciado'];
            $explicacao = $questao['explicacao'] ?? null;
            $resposta_correta = isset($questao['correta']) ? $questao['correta'] : null;
            $imagem = $questao['imagem'] ?? null;
            $id_usuario = verificarAutenticacao(); // Função que obtém ID do usuário logado
            
            // Inserir questão
            $stmt = $conexao->prepare(
                "INSERT INTO questoes 
                (titulo, tipo, status, genero, subgenero, especificacao, enunciado, explicacao, resposta_correta, imagem, id_usuario_criador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            
            if (!$stmt) {
                throw new Exception('Erro na preparação da query: ' . $conexao->error);
            }
            
            $stmt->bind_param(
                "ssssssssssi",
                $titulo, $tipo, $status, $genero, $subgenero, $especificacao,
                $enunciado, $explicacao, $resposta_correta, $imagem, $id_usuario
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Erro ao inserir questão: ' . $stmt->error);
            }
            
            $idQuestao = $stmt->insert_id;
            $stmt->close();
            
            // Inserir alternativas se for questão objetiva
            if ($tipo === 'objetiva' && isset($questao['alternativas']) && is_array($questao['alternativas'])) {
                foreach ($questao['alternativas'] as $alt => $texto) {
                    $stmt = $conexao->prepare(
                        "INSERT INTO alternativas_objetivas (id_questao, alternativa, texto) VALUES (?, ?, ?)"
                    );
                    
                    if (!$stmt) {
                        throw new Exception('Erro ao inserir alternativa: ' . $conexao->error);
                    }
                    
                    $stmt->bind_param("iss", $idQuestao, $alt, $texto);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Erro ao inserir alternativa: ' . $stmt->error);
                    }
                    
                    $stmt->close();
                }
            }
            
            $conexao->commit();
            
            // Retornar questão criada
            $questao['id'] = $idQuestao;
            return $questao;
            
        } catch (Exception $e) {
            $conexao->rollback();
            throw $e;
        }
    }

    /**
     * Atualizar questão existente
     */
    public static function atualizar($id, $dados) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            $tipo = $dados['tipo'];
            $status = $dados['status'];
            $titulo = $dados['titulo'];
            $genero = $dados['genero'];
            $subgenero = $dados['subgenero'] ?? null;
            $especificacao = $dados['especificacao'] ?? null;
            $enunciado = $dados['enunciado'];
            $explicacao = $dados['explicacao'] ?? null;
            $resposta_correta = isset($dados['correta']) ? $dados['correta'] : null;
            $imagem = $dados['imagem'] ?? null;
            
            // Atualizar questão
            $stmt = $conexao->prepare(
                "UPDATE questoes SET 
                titulo = ?, tipo = ?, status = ?, genero = ?, subgenero = ?, especificacao = ?, 
                enunciado = ?, explicacao = ?, resposta_correta = ?, imagem = ? 
                WHERE id = ?"
            );
            
            if (!$stmt) {
                throw new Exception('Erro na preparação da query: ' . $conexao->error);
            }
            
            $stmt->bind_param(
                "ssssssssssi",
                $titulo, $tipo, $status, $genero, $subgenero, $especificacao,
                $enunciado, $explicacao, $resposta_correta, $imagem, $id
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Erro ao atualizar questão: ' . $stmt->error);
            }
            
            $stmt->close();
            
            // Atualizar alternativas se for questão objetiva
            if ($tipo === 'objetiva') {
                // Deletar alternativas antigas
                $stmt = $conexao->prepare("DELETE FROM alternativas_objetivas WHERE id_questao = ?");
                if (!$stmt) {
                    throw new Exception('Erro ao deletar alternativas antigas: ' . $conexao->error);
                }
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                
                // Inserir novas alternativas
                if (isset($dados['alternativas']) && is_array($dados['alternativas'])) {
                    foreach ($dados['alternativas'] as $alt => $texto) {
                        $stmt = $conexao->prepare(
                            "INSERT INTO alternativas_objetivas (id_questao, alternativa, texto) VALUES (?, ?, ?)"
                        );
                        
                        if (!$stmt) {
                            throw new Exception('Erro ao inserir alternativa: ' . $conexao->error);
                        }
                        
                        $stmt->bind_param("iss", $id, $alt, $texto);
                        
                        if (!$stmt->execute()) {
                            throw new Exception('Erro ao inserir alternativa: ' . $stmt->error);
                        }
                        
                        $stmt->close();
                    }
                }
            }
            
            $conexao->commit();
            
            return self::encontrarPorId($id);
            
        } catch (Exception $e) {
            $conexao->rollback();
            throw $e;
        }
    }

    /**
     * Deletar questão por ID
     */
    public static function deletar($id) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            // Deletar alternativas (cascade não funcionou, deletar manualmente)
            $stmt = $conexao->prepare("DELETE FROM alternativas_objetivas WHERE id_questao = ?");
            if (!$stmt) {
                throw new Exception('Erro ao deletar alternativas: ' . $conexao->error);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            // Deletar questão
            $stmt = $conexao->prepare("DELETE FROM questoes WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Erro na preparação da query: ' . $conexao->error);
            }
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Erro ao deletar questão: ' . $stmt->error);
            }
            
            $stmt->close();
            
            $conexao->commit();
            
            return true;
            
        } catch (Exception $e) {
            $conexao->rollback();
            throw $e;
        }
    }

    /**
     * Listar questões com filtros opcionais
     */
    public static function listar($filtros = []) {
        global $conexao;
        
        $query = "SELECT * FROM questoes WHERE 1=1";
        $tipos = "";
        $params = [];
        
        // Filtro por tipo
        if (!empty($filtros['tipo'])) {
            $query .= " AND tipo = ?";
            $tipos .= "s";
            $params[] = $filtros['tipo'];
        }
        
        // Filtro por status
        if (!empty($filtros['status'])) {
            $query .= " AND status = ?";
            $tipos .= "s";
            $params[] = $filtros['status'];
        }
        
        // Filtro por gênero
        if (!empty($filtros['genero'])) {
            $query .= " AND genero = ?";
            $tipos .= "s";
            $params[] = $filtros['genero'];
        }
        
        $query .= " ORDER BY criado_em DESC";
        
        $stmt = $conexao->prepare($query);
        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conexao->error);
        }
        
        // Bind params se houver
        if (!empty($params)) {
            $stmt->bind_param($tipos, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $questoes = [];
        while ($row = $result->fetch_assoc()) {
            // Carregar alternativas se for objetiva
            if ($row['tipo'] === 'objetiva') {
                $row['alternativas'] = self::obterAlternativas($row['id']);
                $row['correta'] = $row['resposta_correta'];
            }
            $questoes[] = $row;
        }
        
        $stmt->close();
        
        return $questoes;
    }

    /**
     * Buscar questões por termo
     */
    public static function buscar($termo) {
        global $conexao;
        
        $termo = "%{$termo}%";
        
        $stmt = $conexao->prepare(
            "SELECT * FROM questoes WHERE titulo LIKE ? OR enunciado LIKE ? ORDER BY criado_em DESC"
        );
        
        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conexao->error);
        }
        
        $stmt->bind_param("ss", $termo, $termo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $questoes = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['tipo'] === 'objetiva') {
                $row['alternativas'] = self::obterAlternativas($row['id']);
                $row['correta'] = $row['resposta_correta'];
            }
            $questoes[] = $row;
        }
        
        $stmt->close();
        
        return $questoes;
    }

    /**
     * Obter estatísticas
     */
    public static function estatisticas() {
        global $conexao;
        
        $stats = [
            'total' => 0,
            'objetivas' => 0,
            'dissertativas' => 0,
            'publicadas' => 0,
            'rascunhos' => 0
        ];
        
        // Total
        $result = $conexao->query("SELECT COUNT(*) as count FROM questoes");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total'] = $row['count'];
        }
        
        // Objetivas
        $result = $conexao->query("SELECT COUNT(*) as count FROM questoes WHERE tipo = 'objetiva'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['objetivas'] = $row['count'];
        }
        
        // Dissertativas
        $result = $conexao->query("SELECT COUNT(*) as count FROM questoes WHERE tipo = 'dissertativa'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['dissertativas'] = $row['count'];
        }
        
        // Publicadas
        $result = $conexao->query("SELECT COUNT(*) as count FROM questoes WHERE status = 'publicada'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['publicadas'] = $row['count'];
        }
        
        // Rascunhos
        $result = $conexao->query("SELECT COUNT(*) as count FROM questoes WHERE status = 'rascunho'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['rascunhos'] = $row['count'];
        }
        
        return $stats;
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
