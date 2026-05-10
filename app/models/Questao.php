<?php
/**
 * MODEL: Questao
 * Encapsula a lógica de acesso à tabela questoes
 */

class Questao {
    /**
     * Encontrar questão por ID
     */
    public static function buscarPorId($id) {
        global $conexao;
        
        $stmt = $conexao->prepare("SELECT * FROM questoes WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            $stmt->close();
            return null;
        }
        
        $questao = $resultado->fetch_assoc();
        $stmt->close();
        
        // Carregar alternativas se for objetiva
        if ($questao['tipo'] === 'objetiva') {
            $questao['alternativas'] = Alternativa::obterPorQuestao($questao['id']);
            $questao['correta'] = $questao['resposta_correta'];
        }
        
        return $questao;
    }
    
    /**
     * Listar questões com filtros
     */
    public static function listar($filtros = []) {
        global $conexao;
        
        $query = "SELECT * FROM questoes WHERE 1=1";
        $tipos = "";
        $params = [];
        
        // Filtro OBRIGATÓRIO: apenas questões do usuário logado
        if (!empty($filtros['id_usuario_criador'])) {
            $query .= " AND id_usuario_criador = ?";
            $tipos .= "i";
            $params[] = $filtros['id_usuario_criador'];
        }
        
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
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        // Bind params
        if (!empty($params)) {
            $stmt->bind_param($tipos, ...$params);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $questoes = [];
        while ($row = $resultado->fetch_assoc()) {
            if ($row['tipo'] === 'objetiva') {
                $row['alternativas'] = Alternativa::obterPorQuestao($row['id']);
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
    public static function buscar($termo, $idUsuario) {
        global $conexao;
        
        $termo_search = "%{$termo}%";
        
        $stmt = $conexao->prepare(
            "SELECT * FROM questoes 
             WHERE id_usuario_criador = ? AND (titulo LIKE ? OR enunciado LIKE ?) 
             ORDER BY criado_em DESC"
        );
        
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("iss", $idUsuario, $termo_search, $termo_search);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $questoes = [];
        while ($row = $resultado->fetch_assoc()) {
            if ($row['tipo'] === 'objetiva') {
                $row['alternativas'] = Alternativa::obterPorQuestao($row['id']);
                $row['correta'] = $row['resposta_correta'];
            }
            $questoes[] = $row;
        }
        
        $stmt->close();
        
        return $questoes;
    }
    
    /**
     * Criar nova questão
     */
    public static function criar($questao) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            $tipo = $questao['tipo'];
            $status = $questao['status'];
            $titulo = sanitizarTexto($questao['titulo']);
            $genero = sanitizarTexto($questao['genero']);
            $subgenero = isset($questao['subgenero']) ? sanitizarTexto($questao['subgenero']) : null;
            $especificacao = isset($questao['especificacao']) ? sanitizarTexto($questao['especificacao']) : null;
            $enunciado = sanitizarTexto($questao['enunciado']);
            $explicacao = isset($questao['explicacao']) ? sanitizarTexto($questao['explicacao']) : null;
            $resposta_correta = isset($questao['correta']) ? $questao['correta'] : null;
            $imagem = $questao['imagem'] ?? null;
            $id_usuario = $questao['id_usuario_criador'];
            
            // Inserir questão
            $stmt = $conexao->prepare(
                "INSERT INTO questoes 
                (titulo, tipo, status, genero, subgenero, especificacao, enunciado, explicacao, resposta_correta, imagem, id_usuario_criador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            
            if (!$stmt) {
                throw new Exception('Erro ao preparar query: ' . $conexao->error);
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
            
            // Inserir alternativas se for objetiva
            if ($tipo === 'objetiva' && isset($questao['alternativas'])) {
                Alternativa::inserirMultiplas($idQuestao, $questao['alternativas']);
            }
            
            $conexao->commit();
            
            $questao['id'] = $idQuestao;
            return $questao;
            
        } catch (Exception $e) {
            $conexao->rollback();
            throw $e;
        }
    }
    
    /**
     * Atualizar questão
     */
    public static function atualizar($id, $dados) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            $tipo = $dados['tipo'];
            $status = $dados['status'];
            $titulo = sanitizarTexto($dados['titulo']);
            $genero = sanitizarTexto($dados['genero']);
            $subgenero = isset($dados['subgenero']) ? sanitizarTexto($dados['subgenero']) : null;
            $especificacao = isset($dados['especificacao']) ? sanitizarTexto($dados['especificacao']) : null;
            $enunciado = sanitizarTexto($dados['enunciado']);
            $explicacao = isset($dados['explicacao']) ? sanitizarTexto($dados['explicacao']) : null;
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
                throw new Exception('Erro ao preparar query: ' . $conexao->error);
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
            
            // Atualizar alternativas se for objetiva
            if ($tipo === 'objetiva') {
                Alternativa::deletarPorQuestao($id);
                
                if (isset($dados['alternativas'])) {
                    Alternativa::inserirMultiplas($id, $dados['alternativas']);
                }
            }
            
            $conexao->commit();
            
            return self::buscarPorId($id);
            
        } catch (Exception $e) {
            $conexao->rollback();
            throw $e;
        }
    }
    
    /**
     * Deletar questão
     */
    public static function deletar($id) {
        global $conexao;
        
        $conexao->begin_transaction();
        
        try {
            // Deletar alternativas
            Alternativa::deletarPorQuestao($id);
            
            // Deletar questão
            $stmt = $conexao->prepare("DELETE FROM questoes WHERE id = ?");
            
            if (!$stmt) {
                throw new Exception('Erro ao preparar query: ' . $conexao->error);
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
     * Obter estatísticas
     */
    public static function estatisticas($idUsuario) {
        global $conexao;
        
        $stats = [];
        
        // Total
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM questoes WHERE id_usuario_criador = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        // Objetivas
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM questoes WHERE id_usuario_criador = ? AND tipo = 'objetiva'");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['objetivas'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        // Dissertativas
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM questoes WHERE id_usuario_criador = ? AND tipo = 'dissertativa'");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['dissertativas'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        // Publicadas
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM questoes WHERE id_usuario_criador = ? AND status = 'publicada'");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['publicadas'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        // Rascunhos
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM questoes WHERE id_usuario_criador = ? AND status = 'rascunho'");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['rascunhos'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        return $stats;
    }
}
?>
