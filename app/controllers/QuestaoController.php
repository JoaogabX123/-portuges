<?php
/**
 * CONTROLLER: QuestaoController
 * Gerencia CRUD de questões
 */

class QuestaoController {
    /**
     * Listar questões do usuário
     * GET /routes/questoes.php?acao=listar&busca=...&tipo=...&status=...&genero=...
     */
    public static function listar() {
        try {
            $id_usuario = verificarAutenticacao();
            
            $busca = trim($_GET['busca'] ?? '');
            $tipo = trim($_GET['tipo'] ?? '');
            $status = trim($_GET['status'] ?? '');
            $genero = trim($_GET['genero'] ?? '');
            $subgenero = trim($_GET['subgenero'] ?? '');
            
            $filtros = [];
            $filtros['id_usuario_criador'] = $id_usuario;
            if (!empty($tipo)) $filtros['tipo'] = $tipo;
            if (!empty($status)) $filtros['status'] = $status;
            if (!empty($genero)) $filtros['genero'] = $genero;
            if (!empty($subgenero)) $filtros['subgenero'] = $subgenero;
            
            $questoes = Questao::listar($filtros);
            
            // Aplicar busca em memória
            if (!empty($busca)) {
                $questoes = array_filter($questoes, function($q) use ($busca) {
                    $busca_lower = strtolower($busca);
                    return stripos(strtolower($q['titulo'] ?? ''), $busca_lower) !== false ||
                           stripos(strtolower($q['enunciado'] ?? ''), $busca_lower) !== false;
                });
                $questoes = array_values($questoes);
            }
            
            resposta_sucesso([
                'total' => count($questoes),
                'questoes' => $questoes
            ]);
            
        } catch (Exception $e) {
            resposta_erro('Erro ao listar questões: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Buscar questão por ID
     * GET /routes/questoes.php?acao=buscar&id=X
     * POST /routes/questoes.php?acao=buscar com JSON {id: X}
     */
    public static function buscar() {
        try {
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                $dados = obterDadosJSON();
                $id = $dados['id'] ?? '';
            }
            
            if (empty($id)) {
                resposta_erro('ID da questão é obrigatório', 400);
            }
            
            $id = intval($id);
            $questao = Questao::buscarPorId($id);
            
            if (!$questao) {
                resposta_erro('Questão não encontrada', 404);
            }
            
            resposta_sucesso($questao);
            
        } catch (Exception $e) {
            resposta_erro('Erro ao buscar questão: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Criar ou atualizar questão
     * POST /routes/questoes.php?acao=salvar com FormData
     */
    public static function salvar() {
        try {
            $usuario_id = verificarAutenticacao();
            
            $id = $_POST['id'] ?? '';
            $tipo = $_POST['tipo'] ?? 'objetiva';
            $acao = $_POST['acao'] ?? 'salvar';
            $status = ($acao === 'postar') ? 'publicada' : 'rascunho';
            
            // Validar campos obrigatórios
            if (empty($_POST['titulo'])) {
                resposta_validacao('Título é obrigatório');
            }
            if (empty($_POST['genero'])) {
                resposta_validacao('Gênero é obrigatório');
            }
            if (empty($_POST['enunciado'])) {
                resposta_validacao('Enunciado é obrigatório');
            }
            
            // Processar upload de imagem
            $caminhoImagem = $_POST['imagem_atual'] ?? '';
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
                $resultado = self::salvar_imagem($_FILES['imagem']);
                
                if (isset($resultado['erro'])) {
                    resposta_erro($resultado['erro'], 400);
                }
                
                $caminhoImagem = $resultado['caminho'];
                
                // Deletar imagem antiga se houver
                if (!empty($_POST['imagem_atual']) && $_POST['imagem_atual'] !== $caminhoImagem) {
                    self::deletar_imagem($_POST['imagem_atual']);
                }
            }
            
            $dadosQuestao = [
                'tipo' => $tipo,
                'status' => $status,
                'titulo' => $_POST['titulo'],
                'genero' => $_POST['genero'],
                'enunciado' => $_POST['enunciado'],
                'explicacao' => $_POST['explicacao'] ?? '',
                'especificacao' => $_POST['especificacao'] ?? '',
                'subgenero' => $_POST['subgenero'] ?? '',
                'imagem' => $caminhoImagem,
                'id_usuario_criador' => $usuario_id
            ];
            
            if ($tipo === 'objetiva') {
                if (empty($_POST['correta'])) {
                    resposta_validacao('Resposta correta é obrigatória');
                }
                if (empty($_POST['alt_A']) || empty($_POST['alt_B']) || empty($_POST['alt_C']) || 
                    empty($_POST['alt_D']) || empty($_POST['alt_E'])) {
                    resposta_validacao('Todas as 5 alternativas são obrigatórias');
                }
                
                $dadosQuestao['correta'] = $_POST['correta'];
                $dadosQuestao['alternativas'] = [
                    'A' => $_POST['alt_A'],
                    'B' => $_POST['alt_B'],
                    'C' => $_POST['alt_C'],
                    'D' => $_POST['alt_D'],
                    'E' => $_POST['alt_E']
                ];
            }
            
            if (!empty($id)) {
                // Atualizar
                $questaoExistente = Questao::buscarPorId($id);
                if ($questaoExistente) {
                    if (empty($caminhoImagem) && !empty($questaoExistente['imagem'])) {
                        $dadosQuestao['imagem'] = $questaoExistente['imagem'];
                    }
                    $questaoAtualizada = Questao::atualizar($id, $dadosQuestao);
                    resposta_sucesso(['id' => $questaoAtualizada['id']], 'Questão atualizada com sucesso');
                } else {
                    $questaoNova = Questao::criar($dadosQuestao);
                    resposta_sucesso(['id' => $questaoNova['id']], 'Questão salva com sucesso');
                }
            } else {
                // Criar nova
                $questaoNova = Questao::criar($dadosQuestao);
                resposta_sucesso(['id' => $questaoNova['id']], 'Questão criada com sucesso');
            }
            
        } catch (Exception $e) {
            resposta_erro('Erro ao salvar questão: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Deletar questão
     * POST /routes/questoes.php?acao=deletar com JSON {id: X}
     */
    public static function deletar() {
        try {
            verificarAutenticacao();
            
            $dados = obterDadosJSON();
            $id = $dados['id'] ?? '';
            
            if (empty($id)) {
                resposta_erro('ID da questão é obrigatório', 400);
            }
            
            $id = intval($id);
            $questao = Questao::buscarPorId($id);
            
            if (!$questao) {
                resposta_erro('Questão não encontrada', 404);
            }
            
            // Deletar imagem se houver
            if (!empty($questao['imagem'])) {
                self::deletar_imagem($questao['imagem']);
            }
            
            Questao::deletar($id);
            
            resposta_sucesso(null, 'Questão deletada com sucesso');
            
        } catch (Exception $e) {
            resposta_erro('Erro ao excluir questão: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Salvar imagem
     */
    private static function salvar_imagem($arquivo) {
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Erro ao fazer upload da imagem'];
        }
        
        $mime_type = mime_content_type($arquivo['tmp_name']);
        if (!in_array($mime_type, TIPOS_PERMITIDOS)) {
            return ['erro' => 'Tipo de arquivo não permitido'];
        }
        
        if ($arquivo['size'] > TAMANHO_MAXIMO_UPLOAD) {
            return ['erro' => 'Arquivo muito grande (máximo 5MB)'];
        }
        
        $extensoes_validas = EXTENSOES_PERMITIDAS;
        $nome_original = $arquivo['name'];
        $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
        
        if (!in_array($extensao, $extensoes_validas)) {
            return ['erro' => 'Extensão de arquivo não permitida'];
        }
        
        $nome_arquivo = uniqid('img_') . '.' . $extensao;
        $caminho_completo = UPLOADS_PATH . '/' . $nome_arquivo;
        
        if (!file_exists(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }
        
        if (!move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
            return ['erro' => 'Erro ao salvar arquivo no servidor'];
        }
        
        return ['caminho' => 'uploads/' . $nome_arquivo];
    }
    
    /**
     * Deletar imagem
     */
    private static function deletar_imagem($caminho) {
        $caminho_completo = PUBLIC_PATH . '/' . $caminho;
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
    }
}
?>
