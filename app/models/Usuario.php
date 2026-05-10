<?php
/**
 * MODEL: Usuario
 * Encapsula a lógica de acesso à tabela usuarios
 */

class Usuario {
    private static $conexao = null;
    
    public static function setConexao($conn) {
        self::$conexao = $conn;
    }
    
    /**
     * Buscar usuário por email
     */
    public static function buscarPorEmail($email) {
        global $conexao;
        
        $stmt = $conexao->prepare("SELECT id, email, senha, nome, tipo FROM usuarios WHERE email = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            $stmt->close();
            return null;
        }
        
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        return $usuario;
    }
    
    /**
     * Buscar usuário por ID
     */
    public static function buscarPorId($id) {
        global $conexao;
        
        $stmt = $conexao->prepare("SELECT id, email, nome, tipo, status FROM usuarios WHERE id = ? LIMIT 1");
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
        
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        return $usuario;
    }
    
    /**
     * Verificar se email já existe
     */
    public static function emailExiste($email) {
        global $conexao;
        
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $existe = $resultado->num_rows > 0;
        $stmt->close();
        
        return $existe;
    }
    
    /**
     * Criar novo usuário
     */
    public static function criar($dados) {
        global $conexao;
        
        // Validar dados
        $erros = [];
        
        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório';
        }
        
        if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Email válido é obrigatório';
        }
        
        if (empty($dados['senha'])) {
            $erros[] = 'Senha é obrigatória';
        } elseif (strlen($dados['senha']) < 8 || !preg_match('/[A-Z]/', $dados['senha']) || 
                  !preg_match('/[a-z]/', $dados['senha']) || !preg_match('/\d/', $dados['senha'])) {
            $erros[] = 'Senha deve ter mínimo 8 caracteres, uma maiúscula, uma minúscula e um número';
        }
        
        if (!empty($erros)) {
            return ['sucesso' => false, 'erros' => $erros];
        }
        
        // Verificar se email já existe
        if (self::emailExiste($dados['email'])) {
            return ['sucesso' => false, 'erro' => 'Email já cadastrado'];
        }
        
        // Hash da senha
        $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        $nome = sanitizarTexto($dados['nome']);
        $email = sanitizarTexto($dados['email']);
        $tipo = $dados['tipo'] ?? 'professor';
        
        // Inserir usuário
        $stmt = $conexao->prepare(
            "INSERT INTO usuarios (email, senha, nome, tipo, status, criado_em) VALUES (?, ?, ?, ?, 1, NOW())"
        );
        
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("ssss", $email, $senha_hash, $nome, $tipo);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            $stmt->close();
            return ['sucesso' => false, 'erro' => 'Erro ao inserir usuário'];
        }
        
        $novo_id = $conexao->insert_id;
        $stmt->close();
        
        return ['sucesso' => true, 'id' => $novo_id, 'email' => $email, 'nome' => $nome];
    }
    
    /**
     * Atualizar último login
     */
    public static function atualizarUltimoLogin($id) {
        global $conexao;
        
        $stmt = $conexao->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Erro ao preparar query: ' . $conexao->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
?>
