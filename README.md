# 📚 +Português - Gerenciador de Questões

Uma plataforma robusta com arquitetura **MVC moderna** para professores gerenciarem, organizarem e reutilizarem suas questões de forma eficiente com banco de dados centralizado.

## 🎯 Objetivo

Criar uma solução inteligente que permite professores de todas as categorias de ensino:
- **Centralizar** todas as suas questões em um único lugar (banco de dados MySQL)
- **Organizar** questões por gênero textual, tipo e especificações
- **Filtrar** questões através de filtros customizados avançados
- **Reutilizar** questões em diferentes avaliações e contextos
- **Gerenciar** histórico e status de questões (rascunho/publicada)
- **Isolar dados** - Cada usuário vê apenas suas próprias questões

---

## 📚 Documentação Rápida

| Documento | Conteúdo |
|-----------|----------|
| **[📖 INSTALACAO.md](docs/INSTALACAO.md)** | Guia passo a passo: criar banco, importar SQL, configurar servidor |
| **[🧪 TESTE_API.md](docs/TESTE_API.md)** | Exemplos de requisições cURL e testes de endpoints |
| **[🏗️ MVC_DOCUMENTATION.md](docs/MVC_DOCUMENTATION.md)** | Documentação detalhada da arquitetura MVC e componentes |

---

### 🔐 Autenticação & Segurança
- ✅ Sistema de autenticação com email/senha
- ✅ Hash seguro de senhas com `password_hash(PASSWORD_DEFAULT)`
- ✅ Sessões PHP com configuração segura (httponly, samesite)
- ✅ Logout funcional com session_destroy()
- ✅ **Isolamento de dados por usuário** (cada usuário vê APENAS suas questões)
- ✅ Suporte a cookies via `credentials: 'include'` em requisições fetch
- ✅ Múltiplos usuários (usuários independentes)

### 📋 Gerenciamento de Questões
- ✅ Criar questões (objetivas e dissertativas)
- ✅ Editar questões existentes
- ✅ Visualizar todas as questões do usuário
- ✅ Deletar questões
- ✅ Buscar questões por título/texto
- ✅ Filtrar por tipo, gênero e status
- ✅ Upload de imagens para questões (JPEG, PNG, WebP, GIF)
- ✅ Explicação detalhada para cada questão

### 🏷️ Tipos de Questões
- **Questões Objetivas (Múltipla Escolha)**
  - 5 alternativas (A, B, C, D, E)
  - Resposta correta definida
  - Alternativas armazenadas em tabela separada
  
- **Questões Dissertativa**
  - Enunciado e orientações para resposta
  - Explicação sobre a questão

### 📊 Organização de Conteúdo
- **Gêneros textuais**: Narrativo, Argumentativo, Descritivo, Expositivo, Instrucional
- **Status**: Rascunho ou Publicada
- **Especificação**: Categorização customizada
- **Subgênero**: Subcategorias específicas

---

## 🛠️ Tech Stack

### Frontend
- **HTML5** + **CSS3** (Responsivo)
- **JavaScript** (Vanilla JS com Fetch API)
- **PHP** para renderização de templates

### Backend
- **PHP 7.4+** com **MySQLi OOP**
- **Arquitetura MVC** (Models, Controllers, Views, Routes)
- **API via PHP (endpoints HTTP)**
- **Sessões PHP** com configuração segura

### Banco de Dados
- **MySQL 5.7+** ou **MariaDB**
- **3 tabelas principais**:
  - `usuarios` - Autenticação
  - `questoes` - Armazenamento de questões
  - `alternativas_objetivas` - Alternativas das questões múltipla escolha
- **Prepared Statements** para prevenir SQL Injection

---

## 📁 Estrutura do Projeto (MVC)

```
Projeto +Portugues/
├── public/                     # Document root (acessível via HTTP)
│   ├── index.php              # Router central - ponto único de entrada
│   ├── css/
│   │   └── style.css          # Estilos consolidados
│   └── uploads/               # Imagens das questões
│
├── app/
│   ├── config/
│   │   └── config.php         # Configuração global + helpers + autoloader
│   │
│   ├── models/                # Lógica de dados
│   │   ├── Usuario.php        # Model de usuários
│   │   ├── Questao.php        # Model de questões
│   │   └── Alternativa.php    # Model de alternativas
│   │
│   ├── controllers/           # Lógica de negócio
│   │   ├── LoginController.php
│   │   ├── LogoutController.php
│   │   ├── SessaoController.php
│   │   └── QuestaoController.php
│   │
│   ├── routes/                # Endpoints HTTP
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── usuarios.php
│   │   └── questoes.php
│   │
│   └── views/                 # Apresentação
│       ├── index.php          # Landing page
│       ├── login.php
│       ├── signup.php
│       ├── home.php           # Dashboard
│       ├── criacao_objetiva.php
│       ├── criacao_dissertativa.php
│       ├── editar_questao.php
│       ├── questao_objetiva.php
│       ├── questao_dissertativa.php
│       └── abas/
│           ├── questao_objetiva.php
│           └── questao_dissertativa.php
│
├── README.md                  # Este arquivo
└── INSTALACAO.md             # Guia de instalação
```

---

## 📦 Instalação & Configuração

### Pré-requisitos
- PHP 7.4+
- MySQL/MariaDB
- Apache com `mod_rewrite` (opcional, mas recomendado)
- Git

### Passo 1: Clonar/Acessar o Repositório

```bash
cd "Projeto +Portugues"
```

### Passo 2: Criar Banco de Dados

```bash
# Via phpMyAdmin:
1. Acesse http://localhost/phpmyadmin
2. Clique em "Nova" para criar novo banco
3. Nome do banco: `mais_portugues`
4. Charset: utf8mb4_general_ci
5. Clique em "Criar"
```

### Passo 3: Importar Schema

```bash
# Via phpMyAdmin:
1. Selecione o banco `mais_portugues`
2. Vá para a aba "Importar"
3. Cole o SQL abaixo ou importe do arquivo database/mais_portugues_corrigido.sql
```

**Script SQL:**

```sql
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ultimo_login DATETIME
);

CREATE TABLE questoes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(255) NOT NULL,
  tipo ENUM('objetiva', 'dissertativa') NOT NULL,
  status ENUM('rascunho', 'publicada') DEFAULT 'rascunho',
  genero VARCHAR(100),
  subgenero VARCHAR(100),
  especificacao VARCHAR(255),
  enunciado LONGTEXT,
  explicacao LONGTEXT,
  resposta_correta CHAR(1),
  imagem VARCHAR(255),
  id_usuario_criador INT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario_criador) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE alternativas_objetivas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_questao INT NOT NULL,
  alternativa CHAR(1) NOT NULL,
  texto LONGTEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_questao) REFERENCES questoes(id) ON DELETE CASCADE
);
```

### Passo 4: Verificar Configuração

A configuração está em `/app/config/config.php` e já vem pronta para MySQL local:

```php
'servername' => 'localhost',
'usuario'    => 'root',
'senha'      => '',  // Deixe vazio se não tiver senha
'banco'      => 'mais_portugues'
```

### Passo 5: Configurar Document Root (XAMPP)

**No XAMPP:**
1. Edite `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Procure por `Projeto +Portugues` e altere o `DocumentRoot`:
   ```apache
   DocumentRoot "C:/xampp/htdocs/Projeto +Portugues/public"
   <Directory "C:/xampp/htdocs/Projeto +Portugues/public">
   ```
3. Reinicie Apache

**Ou acesse direto:**
- http://localhost/Projeto+Portugues/public/

### Passo 6: Criar Usuário de Teste

Via phpMyAdmin ou SQL:

```sql
INSERT INTO usuarios (email, senha, nome) VALUES (
  'teste@teste.com',
  '$2y$10$6RYIekPXSIWWp7w7EF7WaOqF9HXEzaAMwFfGtDJZUlRZy7xP0NYxC',
  'Usuário Teste'
);
```

**Credenciais de Teste:**
- Email: `teste@teste.com`
- Senha: `Teste@123`

---

## 🗄️ Estrutura do Banco de Dados

### Tabela: `usuarios`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | ID único auto-incremental |
| email | VARCHAR UNIQUE | Email para login |
| senha | VARCHAR | Hash da senha |
| nome | VARCHAR | Nome do usuário |
| criado_em | TIMESTAMP | Data de criação |
| ultimo_login | DATETIME | Último acesso |

### Tabela: `questoes`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | ID único auto-incremental |
| titulo | VARCHAR | Título da questão |
| tipo | ENUM | 'objetiva' ou 'dissertativa' |
| status | ENUM | 'rascunho' ou 'publicada' |
| genero | VARCHAR | Gênero textual |
| subgenero | VARCHAR | Subcategoria |
| especificacao | VARCHAR | Especificação customizada |
| enunciado | LONGTEXT | Texto da questão |
| explicacao | LONGTEXT | Explicação da resposta |
| resposta_correta | CHAR | 'A' a 'E' (NULL para dissertativas) |
| imagem | VARCHAR | Caminho da imagem em `/public/uploads/` |
| id_usuario_criador | INT (FK) | Referência ao usuário criador |
| criado_em | TIMESTAMP | Data de criação |
| atualizado_em | TIMESTAMP | Última atualização |

### Tabela: `alternativas_objetivas`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | ID único auto-incremental |
| id_questao | INT (FK) | Referência à questão |
| alternativa | CHAR | 'A', 'B', 'C', 'D' ou 'E' |
| texto | LONGTEXT | Texto da alternativa |
| criado_em | TIMESTAMP | Data de criação |

---

## 🔄 Fluxo de Funcionamento

### 1. Ponto de Entrada
```
http://localhost/Projeto+Portugues/public/
  ↓
index.php (Router central)
  ↓
Valida página via GET parameter (?page=login)
  ↓
Carrega view correspondente
```

### 2. API Endpoints (Routes)
```
POST /app/routes/login.php
  → LoginController::fazer_login()
  → Autentica e seta $_SESSION['usuario_id']
  
GET /app/routes/usuarios.php?acao=verificar_sessao
  → SessaoController::verificar_sessao()
  → Retorna dados da sessão ativa
  
POST /app/routes/questoes.php?acao=salvar
  → QuestaoController::salvar()
  → Cria ou atualiza questão (com FormData + imagem)
  
GET /app/routes/questoes.php?acao=listar
  → QuestaoController::listar()
  → Retorna questões do usuário logado
```

### 3. Segurança
- ✅ Session iniciada em `config.php` (antes de qualquer output)
- ✅ Prepared Statements em todos os Models
- ✅ Validação de autenticação em controllers críticos
- ✅ User isolation: Filtro `WHERE id_usuario_criador = ?` obrigatório
- ✅ Password hash com PASSWORD_DEFAULT

---

## ✅ Status de Desenvolvimento

### Funcionando 100%
- [x] Autenticação (login/logout/signup)
- [x] CRUD completo de questões
- [x] Upload de imagens
- [x] Busca e filtros
- [x] Sessões seguras
- [x] Isolamento de dados por usuário
- [x] Roteador centralizado
- [x] Arquitetura MVC limpa
- [x] Responses JSON padronizadas

### Próximas Melhorias
- [ ] Testes automatizados
- [ ] API REST documentada (Swagger)
- [ ] Compartilhamento entre usuários
- [ ] Exportação PDF
- [ ] Relatórios estatísticos
- [ ] Dark mode

---

## 🚀 Como Usar a Plataforma

### 1. Registrar-se
- Acesse a página principal
- Clique em "Cadastre-se"
- Preencha email, nome e senha

### 2. Fazer Login
- Email e senha registrados
- Será redirecionado para o dashboard

### 3. Criar Questão Objetiva
- Clique em "+ Adicionar questão"
- Escolha "Objetiva"
- Preencha título, gênero, enunciado
- Adicione 5 alternativas (A-E)
- Escolha a resposta correta
- (Opcional) Faça upload de imagem
- Clique em "Salvar" ou "Postar"

### 4. Criar Questão Dissertativa
- Clique em "+ Adicionar questão"
- Escolha "Dissertativa"
- Preencha título, gênero, enunciado
- (Opcional) Faça upload de imagem
- Clique em "Salvar" ou "Postar"

### 5. Buscar/Filtrar
- Use a barra de busca no dashboard
- Ou use os filtros disponíveis

### 6. Editar Questão
- Clique na questão na lista
- Clique em "Editar"
- Faça as alterações
- Clique em "Salvar"

### 7. Deletar Questão
- Abra a questão
- Clique em "Editar"
- Clique em "Excluir"

---

## 📞 Suporte

Para informações detalhadas:
- **[📖 INSTALACAO.md](docs/INSTALACAO.md)** - Guia completo e passo a passo de instalação
- **[🧪 TESTE_API.md](docs/TESTE_API.md)** - Exemplos de requisições e testes de endpoints
- **[🏗️ MVC_DOCUMENTATION.md](docs/MVC_DOCUMENTATION.md)** - Documentação detalhada da arquitetura MVC


```
banco-questoes/
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/
│   │   └── App.jsx
│   └── package.json
├── backend/
│   ├── models/
│   ├── routes/
│   ├── controllers/
│   ├── middleware/
│   └── app.py (ou server.js)
├── database/
│   └── schema.sql
├── docs/
│   └── API.md
└── README.md
```

---

## 🔄 Fluxo de Desenvolvimento

### Fases Planejadas

**Fase 1: Infraestrutura & Autenticação** (Semanas 1-2)
- Setup do servidor
- Configuração do banco de dados
- Sistema de login/registro

**Fase 2: Tela Inicial & Visualização** (Semanas 2-3)
- Dashboard inicial
- Listagem de questões
- Filtros básicos

**Fase 3: CRUD de Questões** (Semanas 3-5)
- Criar questões
- Editar questões
- Deletar questões
- Gerenciamento de categorias

**Fase 4: Filtros Avançados** (Semanas 5-6)
- Filtros customizados
- Busca avançada
- Salvamento de filtros

**Fases 5+: Funcionalidades Extras**
- Provas/Avaliações
- Compartilhamento
- Relatórios
- API pública

---

## 👥 Equipe

- **Frontend**: João Gabriel, Maria Luísa
- **Backend/Banco de Dados**: Demais membros da equipe

---

## 📖 Documentação Adicional

- [Especificações Técnicas](./TRELLO_PROJETO_BANCO_QUESTOES.md)
- [Guia de Setup do Trello](./COMO_USAR_IMPORT_TRELLO.md)
- [Roadmap Detalhado](./TRELLO_PROJETO_BANCO_QUESTOES.md)

---

## 🔧 Desenvolvimento

### Configurar Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
# Database
DB_HOST=localhost
DB_PORT=5432
DB_NAME=banco_questoes
DB_USER=seu_usuario
DB_PASSWORD=sua_senha

# Backend
BACKEND_URL=http://localhost:5000
API_PORT=5000

# Frontend
VITE_API_URL=http://localhost:5000

# JWT
JWT_SECRET=sua_chave_secreta_aqui
JWT_EXPIRATION=24h
```

### Comandos Úteis

```bash
# Desenvolvimento
npm run dev          # Frontend
npm run dev:backend  # Backend

# Build para produção
npm run build

# Testes
npm test

# Linter
npm run lint
```

---

## 🐛 Reportar Problemas

Encontrou um bug? Abra uma [issue](https://github.com/seu-usuario/banco-questoes/issues) descrevendo:
- Comportamento esperado
- Comportamento atual
- Passos para reproduzir
- Screenshots (se aplicável)

---

## 💡 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Add: Minha feature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## 📝 Convenções de Código

- **Nomes em inglês** para variáveis, funções e classes
- **Commits semânticos**: `feat:`, `fix:`, `docs:`, `refactor:`
- **Mobile-first** no design responsivo
- **Testes** para novas funcionalidades

---

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE). Veja o arquivo LICENSE para mais detalhes.

---

## 📞 Contato & Suporte

Para dúvidas, sugestões ou problemas:
- Abra uma [issue](https://github.com/seu-usuario/banco-questoes/issues)
- Entre em contato com a equipe via [email]

---

## 🙏 Agradecimentos

Obrigado a todos que contribuíram para este projeto!

---

**Última atualização**: Abril de 2026
