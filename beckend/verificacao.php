<?php
/**
 * VERIFICACAO.PHP
 * Script para verificar se tudo está configurado corretamente
 */

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação da Instalação - Portuguese Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #2373eb;
            padding-bottom: 10px;
        }
        .status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }
        .status.ok {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }
        .status.erro {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .status.aviso {
            background: #fff3e0;
            border-left-color: #ff9800;
        }
        .icon {
            font-weight: bold;
            margin-right: 10px;
        }
        .ok .icon { color: #4caf50; }
        .erro .icon { color: #f44336; }
        .aviso .icon { color: #ff9800; }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>✓ Verificação da Instalação - Portuguese Database</h1>

    <?php
    $erros = [];
    $avisos = [];
    $ok = [];

    // 1. Verificar PHP
    if (version_compare(PHP_VERSION, '7.4', '>=')) {
        $ok[] = "PHP " . PHP_VERSION . " (suportado)";
    } else {
        $erros[] = "PHP " . PHP_VERSION . " - Versão mínima: 7.4";
    }

    // 2. Verificar extensões necessárias
    $extensoes_necessarias = ['mysqli', 'json', 'fileinfo'];
    foreach ($extensoes_necessarias as $ext) {
        if (extension_loaded($ext)) {
            $ok[] = "Extensão PHP: <code>$ext</code>";
        } else {
            $erros[] = "Extensão PHP <code>$ext</code> não está instalada";
        }
    }

    // 3. Verificar arquivo database/config.php
    $config_file = __DIR__ . '/../database/config.php';
    if (file_exists($config_file)) {
        $ok[] = "Arquivo de configuração encontrado";
        
        // Tentar conectar
        require_once $config_file;
        
        if (isset($conexao) && $conexao instanceof mysqli) {
            if ($conexao->connect_error) {
                $erros[] = "Erro ao conectar com banco: " . $conexao->connect_error;
            } else {
                $ok[] = "Conexão MySQL estabelecida com sucesso";
                
                // Verificar tabelas
                $tabelas_necessarias = ['usuarios', 'questoes', 'alternativas_objetivas'];
                $tabelas_encontradas = [];
                
                $resultado = $conexao->query("SHOW TABLES");
                while ($row = $resultado->fetch_row()) {
                    $tabelas_encontradas[] = $row[0];
                }
                
                foreach ($tabelas_necessarias as $tabela) {
                    if (in_array($tabela, $tabelas_encontradas)) {
                        $ok[] = "Tabela <code>$tabela</code> encontrada";
                    } else {
                        $erros[] = "Tabela <code>$tabela</code> não encontrada. Importe o arquivo SQL.";
                    }
                }
                
                // Verificar usuário admin
                $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM usuarios WHERE email = 'admin@admin.com'");
                if ($stmt) {
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $row = $resultado->fetch_assoc();
                    
                    if ($row['count'] > 0) {
                        $ok[] = "Usuário admin@admin.com já cadastrado";
                    } else {
                        $avisos[] = "Usuário admin@admin.com não encontrado. Importe o SQL novamente.";
                    }
                    $stmt->close();
                } else {
                    $erros[] = "Erro ao consultar usuários: " . $conexao->error;
                }
            }
        } else {
            $erros[] = "Arquivo config.php não retorna conexão mysqli válida";
        }
    } else {
        $erros[] = "Arquivo <code>database/config.php</code> não encontrado";
    }

    // 4. Verificar arquivos PHP necessários
    $arquivos_necessarios = [
        'helpers.php' => 'Classes e funções auxiliares',
        'config.php' => 'Configuração global',
        'login.php' => 'Autenticação',
        'salvar_questao.php' => 'CRUD questões',
        'listar_questoes.php' => 'Listagem de questões',
        'buscar_questao.php' => 'Busca por ID',
        'excluir_questao.php' => 'Deletar questão',
        'sessao.php' => 'Verificar sessão',
        'logout.php' => 'Logout'
    ];
    
    $diretorio_backend = __DIR__;
    foreach ($arquivos_necessarios as $arquivo => $descricao) {
        if (file_exists($diretorio_backend . '/' . $arquivo)) {
            $ok[] = "<code>$arquivo</code> - $descricao";
        } else {
            $erros[] = "<code>$arquivo</code> não encontrado";
        }
    }

    // 5. Verificar diretório uploads
    $dir_uploads = __DIR__ . '/uploads';
    if (is_dir($dir_uploads)) {
        if (is_writable($dir_uploads)) {
            $ok[] = "Diretório <code>uploads/</code> existe e é gravável";
        } else {
            $avisos[] = "Diretório <code>uploads/</code> não é gravável. Execute: <code>chmod 755 uploads/</code>";
        }
    } else {
        $avisos[] = "Diretório <code>uploads/</code> não existe. Será criado automaticamente.";
    }

    // Exibir resultados
    echo "<h2>Resultados da Verificação</h2>";

    if (!empty($ok)) {
        echo "<h3 style='color: #4caf50;'>✓ Verificações OK</h3>";
        foreach ($ok as $msg) {
            echo "<div class='status ok'><span class='icon'>✓</span>$msg</div>";
        }
    }

    if (!empty($avisos)) {
        echo "<h3 style='color: #ff9800;'>⚠ Avisos</h3>";
        foreach ($avisos as $msg) {
            echo "<div class='status aviso'><span class='icon'>⚠</span>$msg</div>";
        }
    }

    if (!empty($erros)) {
        echo "<h3 style='color: #f44336;'>✗ Erros</h3>";
        foreach ($erros as $msg) {
            echo "<div class='status erro'><span class='icon'>✗</span>$msg</div>";
        }
    }

    // Resumo
    echo "<h2>Resumo</h2>";
    echo "<table class='table'>";
    echo "<tr><th>Categoria</th><th>Total</th><th>Status</th></tr>";
    echo "<tr><td>Verificações OK</td><td>" . count($ok) . "</td><td style='color: #4caf50;'>✓</td></tr>";
    echo "<tr><td>Avisos</td><td>" . count($avisos) . "</td><td style='color: #ff9800;'>⚠</td></tr>";
    echo "<tr><td>Erros</td><td>" . count($erros) . "</td><td style='color: #f44336;'>✗</td></tr>";
    echo "</table>";

    if (empty($erros)) {
        echo "<div class='status ok' style='margin-top: 20px;'>";
        echo "<span class='icon'>✓</span>";
        echo "<strong>Tudo está configurado corretamente!</strong><br>";
        echo "Você pode começar a usar a aplicação.";
        echo "</div>";
    } else {
        echo "<div class='status erro' style='margin-top: 20px;'>";
        echo "<span class='icon'>✗</span>";
        echo "<strong>Há erros que precisam ser corrigidos.</strong><br>";
        echo "Verifique os itens acima e corrija antes de usar a aplicação.";
        echo "</div>";
    }

    // Próximos passos
    echo "<h2>Próximos Passos</h2>";
    echo "<ol>";
    echo "<li>Se todos os erros foram corrigidos, acesse a interface em <code>/front/tela_de_login.php</code></li>";
    echo "<li>Login com <code>admin@admin.com</code> e senha <code>123</code></li>";
    echo "<li>Comece a criar e gerenciar questões</li>";
    echo "</ol>";

    ?>

</body>
</html>
