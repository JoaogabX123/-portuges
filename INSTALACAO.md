# Guia de Instalação - Portuguese Feature Database

Este projeto foi corrigido e agora utiliza um banco de dados MySQL/MariaDB simplificado com as tabelas necessárias apenas.

## Alterações Realizadas

### ✅ Schema SQL Corrigido
- Removidas tabelas desnecessárias: `aluno`, `professor`, `resposta`
- Mantidas apenas 3 tabelas essenciais:
  - `usuarios` - Autenticação de usuários
  - `questoes` - Armazenamento de questões
  - `alternativas_objetivas` - Alternativas A-E das questões múltipla escolha

### ✅ Backend PHP Refatorado
- Classe `BancoQuestoes` migrada de JSON para MySQLi
- `login.php` integrado com banco de dados (suporta `password_verify()`)
- Todos os endpoints funcionando corretamente com MySQL
- Autenticação verificada em operações críticas

### ✅ Frontend Mantido Intacto
- Nenhuma alteração no HTML, CSS ou JavaScript
- Interface permanece igual ao projeto original

## Passos de Instalação

### 1. Criar o Banco de Dados

```bash
# Via phpMyAdmin:
1. Acesse http://localhost/phpmyadmin
2. Clique em "Nova" para criar novo banco
3. Nome do banco: `mais_portugues`
4. Charset: utf8mb4_general_ci
5. Clique em "Criar"
```

### 2. Importar o Schema

```bash
# Via phpMyAdmin:
1. Selecione o banco `mais_portugues`
2. Vá para a aba "Importar"
3. Selecione o arquivo: database/mais_portugues_corrigido.sql
4. Clique em "Executar"
```

**OU via linha de comando:**

```bash
mysql -u root -p mais_portugues < database/mais_portugues_corrigido.sql
```

### 3. Verificar Configuração

O arquivo `database/config.php` está pré-configurado com:
```php
$servername = "localhost";
$usuario = "root";
$senha = "";  // Modifique se necessário
$banco = "mais_portugues";
```

Se suas credenciais MySQL forem diferentes, edite `database/config.php`.

## Credenciais Padrão

### Usuário Admin (já inserido no banco)
- **Email**: `admin@admin.com`
- **Senha**: `123`

Você pode adicionar mais usuários diretamente no banco ou criar um endpoint de registro.

## Estrutura de Dados

### Tabela: usuarios
```sql
id (INT) - PK, Auto Increment
email (VARCHAR) - UNIQUE
senha (VARCHAR) - Hash com password_hash()
nome (VARCHAR)
tipo (ENUM) - 'professor' ou 'admin'
status (TINYINT) - 0=inativo, 1=ativo
criado_em (TIMESTAMP)
ultimo_login (DATETIME)
```

### Tabela: questoes
```sql
id (INT) - PK, Auto Increment
titulo (VARCHAR)
tipo (ENUM) - 'objetiva' ou 'dissertativa'
status (ENUM) - 'rascunho' ou 'publicada'
genero (ENUM) - narrativo, argumentativo, descritivo, expositivo, instrucional
subgenero (VARCHAR)
especificacao (VARCHAR)
enunciado (LONGTEXT)
explicacao (LONGTEXT)
resposta_correta (CHAR) - A, B, C, D, E (NULL para dissertativas)
imagem (VARCHAR) - Caminho relativo da imagem
id_usuario_criador (INT) - FK para usuarios
criado_em (TIMESTAMP)
atualizado_em (TIMESTAMP)
```

### Tabela: alternativas_objetivas
```sql
id (INT) - PK, Auto Increment
id_questao (INT) - FK para questoes
alternativa (CHAR) - 'A', 'B', 'C', 'D' ou 'E'
texto (LONGTEXT) - Conteúdo da alternativa
criado_em (TIMESTAMP)
```

## Endpoints da API

### Autenticação
- `POST /beckend/login.php` - Login com email e senha
- `POST /beckend/logout.php` - Logout da sessão
- `POST /beckend/sessao.php` - Verificar sessão ativa

### Questões
- `POST /beckend/salvar_questao.php` - Criar/atualizar questão
- `GET /beckend/buscar_questao.php?id=1` - Buscar questão por ID
- `GET /beckend/listar_questoes.php` - Listar questões com filtros
- `POST /beckend/excluir_questao.php` - Deletar questão

### Filtros para Listagem
```
GET /beckend/listar_questoes.php?tipo=objetiva&status=publicada&genero=argumentativo&busca=termo
```

## Estrutura de Pastas

```
portuges-feature-databese/
├── beckend/
│   ├── config.php                 # Configurações globais
│   ├── helpers.php                # Classes (Resposta, BancoQuestoes, Upload)
│   ├── login.php                  # Autenticação
│   ├── logout.php                 # Encerrar sessão
│   ├── sessao.php                 # Verificar autenticação
│   ├── salvar_questao.php         # CRUD questões
│   ├── buscar_questao.php         # Busca por ID
│   ├── listar_questoes.php        # Listagem com filtros
│   ├── excluir_questao.php        # Deletar questão
│   ├── uploads/                   # Armazena imagens
│   └── questoes.json              # (LEGADO - não mais usado)
├── database/
│   ├── config.php                 # Configuração MySQLi
│   └── mais_portugues_corrigido.sql # Schema SQL corrigido
└── front/
    ├── *.html/php                 # Arquivos da interface
    ├── css/style.css              # Estilos
    └── js/api.js                  # Cliente JavaScript
```

## Migração de Dados (Opcional)

Se você quiser migrar dados do `questoes.json` original:

```php
<?php
require 'database/config.php';
require 'beckend/helpers.php';

$arquivo = 'beckend/questoes.json';
$conteudo = file_get_contents($arquivo);
$questoes = json_decode($conteudo, true);

foreach ($questoes as $questao) {
    try {
        // Modificar ID para NULL para auto_increment
        unset($questao['id']);
        BancoQuestoes::adicionar($questao);
    } catch (Exception $e) {
        echo "Erro ao migrar: " . $e->getMessage() . "\n";
    }
}
?>
```

## Troubleshooting

### Erro: "Falha na conexão com o banco de dados"
- Verifique se MySQL/MariaDB está rodando
- Confirme credenciais em `database/config.php`
- Verifique se o banco `mais_portugues` existe

### Erro: "Você não está autenticado"
- Realize login via `POST /beckend/login.php` com email/senha
- Verifique se as cookies/sessões estão habilitadas no navegador

### Erro: "Questão não encontrada"
- Confirme que o ID da questão existe no banco
- Verifique que o usuário tem permissão para acessar

## Suporte

Para dúvidas ou problemas, verifique:
1. O arquivo `database/config.php` com credenciais corretas
2. Se o schema SQL foi importado corretamente
3. Os logs do MySQL para erros de conexão

---

**Versão**: 1.0
**Última atualização**: 21/04/2026
