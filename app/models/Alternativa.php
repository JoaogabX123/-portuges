<?php
/**
 * MODEL: Alternativa
 * Encapsula a lógica de acesso à tabela alternativas_objetivas
 */

class Alternativa {
    /**
     * Obter alternativas de uma questão
     */
    public static function obterPorQuestao($idQuestao) {
        global $conexao;
        
        $alternativas = [];
        
        $stmt = $conexao->prepare(
            "SELECT alternativa, texto FROM alternativas_objetivas WHERE id_questao = ? ORDER BY alternativa ASC"
        );
        
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $idQuestao);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        while ($row = $resultado->fetch_assoc()) {
            $alternativas[$row['alternativa']] = $row['texto'];
        }
        
        $stmt->close();
        
        return $alternativas;
    }
    
    /**
     * Inserir alternativa
     */
    public static function inserir($idQuestao, $alternativa, $texto) {
        global $conexao;
        
        $stmt = $conexao->prepare(
            "INSERT INTO alternativas_objetivas (id_questao, alternativa, texto) VALUES (?, ?, ?)"
        );
        
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("iss", $idQuestao, $alternativa, $texto);
        
        if (!$stmt->execute()) {
            throw new Exception('Erro ao inserir alternativa: ' . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /**
     * Deletar alternativas de uma questão
     */
    public static function deletarPorQuestao($idQuestao) {
        global $conexao;
        
        $stmt = $conexao->prepare("DELETE FROM alternativas_objetivas WHERE id_questao = ?");
        
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $idQuestao);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Inserir múltiplas alternativas
     */
    public static function inserirMultiplas($idQuestao, $alternativas) {
        foreach ($alternativas as $alt => $texto) {
            self::inserir($idQuestao, $alt, $texto);
        }
    }
}
?>
