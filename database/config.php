<?php
/**
 * Configuração do Banco de Dados MySQL
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'mais_portugues');  // Ajuste para o nome do seu banco
define('DB_USER', 'root');             // Seu usuário do MySQL
define('DB_PASS', '');                 // Sua senha do MySQL
define('DB_CHARSET', 'utf8mb4');

// Configuração de timezone
date_default_timezone_set('America/Sao_Paulo');

// Habilitar exibição de erros (apenas para desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Classe Database - Versão Simplificada e Robusta
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            die(json_encode([
                'success' => false,
                'mensagem' => 'Erro de conexão com o banco de dados: ' . $e->getMessage(),
                'detail' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()
            ]));
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
?>