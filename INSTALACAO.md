# Guia de Instalação - Projeto +Portugues

Este projeto é uma plataforma completa de gerenciamento de questões com isolamento de dados por usuário, autenticação PHP com sessões, e banco de dados MySQL.

## ✅ Status Atual

- ✅ **Banco de dados MySQL** funcionando
- ✅ **Backend PHP** com endpoints seguros
- ✅ **Isolamento de dados por usuário** implementado e testado
- ✅ **Autenticação com sessões** PHP
- ✅ **Frontend responsivo** funcionando
- ✅ **Suporte a cookies** via `credentials: 'include'`

---

## 📋 Pré-requisitos

- **XAMPP** ou **LAMP/LEMP** com PHP 7.4+
- **MySQL 5.7+** ou **MariaDB**
- **Navegador moderno** (Chrome, Firefox, Safari, Edge)

---

## 🚀 Passos de Instalação

### 1. Criar o Banco de Dados

**Via phpMyAdmin** (recomendado):
```
1. Acesse http://localhost/phpmyadmin
2. Clique em "Novo" para criar novo banco
3. Nome do banco: `mais_portugues`
4. Charset: utf8mb4_general_ci
5. Clique em "Criar"
```

**Via linha de comando**:
```bash
mysql -u root -p -e "CREATE DATABASE mais_portugues CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

### 2. Importar o Schema

**Via phpMyAdmin**:
```
1. Selecione o banco `mais_portugues`
2. Vá para a aba "Importar"
3. Selecione o arquivo: database/mais_portugues.sql
4. Clique em "Executar"
```

**Via linha de comando**:
```bash
mysql -u root -p mais_portugues < database/mais_portugues.sql
```

### 3. Verificar Configuração

Verifique o arquivo `database/config.php`:

```php
<?php
$servername = "localhost";
$usuario = "root";           // ← Ajuste se necessário
$senha = "";                 // ← Sua senha MySQL
$banco = "mais_portugues";
$port = 3306;
?>
```

Se suas credenciais MySQL forem diferentes, edite o arquivo acima.

---

## 🔐 Credenciais Padrão

### Usuários Pré-criados

| Email | Senha | Tipo | Questões |
|-------|-------|------|----------|
| `admin@admin.com` | `admin123` | Admin | 9 |
| `novo@teste.com` | `senha123` | Professor | 1 |

> **Segurança**: Todas as senhas são armazenadas com hash bcrypt `password_hash(PASSWORD_DEFAULT)`

---

## 🌐 Acessar a Aplicação

### URL Principal
```
http://localhost/Projeto%20+Portugues/
```

Ou direto no login:
```
http://localhost/Projeto%20+Portugues/front/tela_de_login.php
```

---

## 🧪 Testar Isolamento de Dados

### Teste Automatizado

```
http://localhost/Projeto%20+Portugues/beckend/teste_completo.php
```

Este teste verifica automaticamente:
- ✅ Login de novo usuário → vê 1 questão
- ✅ Login de admin → vê 9 questões
- ✅ Isolamento de dados funcionando

---

## 📚 Estrutura de Dados

### Tabela: usuarios
```sql
id                INT PRIMARY KEY AUTO_INCREMENT
email             VARCHAR(255) UNIQUE NOT NULL
senha             VARCHAR(255) NOT NULL (bcrypt hash)
nome              VARCHAR(100) NOT NULL
tipo              ENUM('professor', 'admin') DEFAULT 'professor'
status            TINYINT DEFAULT 1
criado_em         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
ultimo_login      DATETIME NULL
```

### Tabela: questoes
```sql
id                      INT PRIMARY KEY AUTO_INCREMENT
titulo                  VARCHAR(255) NOT NULL
tipo                    ENUM('objetiva', 'dissertativa') NOT NULL
status                  ENUM('rascunho', 'publicada') NOT NULL
genero                  ENUM(...) NOT NULL
subgenero               VARCHAR(100)
especificacao           VARCHAR(100)
enunciado               LONGTEXT NOT NULL
explicacao              LONGTEXT
resposta_correta        CHAR(1) NULL
imagem                  VARCHAR(255)
id_usuario_criador      INT NOT NULL (FK para usuarios.id) ← ISOLAMENTO
criado_em               TIMESTAMP DEFAULT CURRENT_TIMESTAMP
atualizado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Tabela: alternativas_objetivas
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
id_questao  INT NOT NULL (FK)
alternativa CHAR(1) NOT NULL (A-E)
texto       TEXT NOT NULL
criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

---

## 🔒 Segurança Implementada

### ✅ Isolamento por Usuário
- Cada usuário vê **APENAS** suas questões
- SQL: `WHERE id_usuario_criador = ?`
- Verificação em todos os endpoints

### ✅ Persistência de Sessão
- `credentials: 'include'` em fetch() JavaScript
- Cookies PHP sendo gerenciados corretamente

### ✅ Autenticação
- Hash bcrypt com `password_hash(PASSWORD_DEFAULT)`
- Verificação com `password_verify()`
- Verificação de sessão em operações críticas

---

## 📂 Estrutura de Pastas

```
Projeto +Portugues/
├── beckend/
│   ├── config.php           ← Configuração MySQL
│   ├── helpers.php          ← Classes BancoQuestoes, Resposta
│   ├── login.php            ← Autenticação
│   ├── logout.php           ← Sair
│   ├── sessao.php           ← Verificar sessão
│   ├── listar_questoes.php  ← Listar com isolamento ✨
│   ├── salvar_questao.php   ← Criar/editar
│   ├── deletar_questao.php  ← Deletar
│   └── teste_completo.php   ← Teste automatizado
├── front/
│   ├── index.php                          ← Home
│   ├── tela_de_login.php                  ← Login
│   ├── cadastro_do_usuario.php            ← Registro
│   ├── home_page.php                      ← Dashboard ✨
│   ├── criacao_de_questao_objetiva.php    ← Criar objetiva
│   ├── criacao_de_questao_dissertativa.php ← Criar dissertativa
│   └── ...
├── database/
│   ├── config.php           ← Configuração
│   └── mais_portugues.sql   ← Schema SQL
├── README.md                ← Documentação
├── TESTE_API.md             ← Exemplos de API
└── INSTALACAO.md            ← Este arquivo
```

---

## ⚠️ Troubleshooting

### Problema: "Conexão recusada"
**Solução**: Verifique se MySQL está rodando e as credenciais em `database/config.php`

### Problema: "Tabela não existe"
**Solução**: Reimporte o schema: `database/mais_portugues.sql`

### Problema: "Home page travando"
**Solução**: Abra DevTools (F12) e veja erros no Console. Hard refresh (Ctrl+F5).

### Problema: "Login falha mas sem erro"
**Solução**: Verifique se a senha é exatamente `admin123` (case-sensitive)

---

## 🚀 Próximos Passos

1. ✅ Testar login e isolamento
2. ✅ Criar algumas questões
3. ✅ Verificar que cada usuário vê apenas suas questões
4. ✅ Testar logout e re-login
5. ✅ Verificar admin dashboard com todas as questões

---

## 📞 Suporte

Se encontrar algum erro, verifique:
- `database/config.php` - Credenciais MySQL
- `DevTools (F12)` - Console para erros JavaScript
- Banco de dados está rodando
- XAMPP Apache está ativo
2. Se o schema SQL foi importado corretamente
3. Os logs do MySQL para erros de conexão

---

**Versão**: 1.0
**Última atualização**: 21/04/2026
