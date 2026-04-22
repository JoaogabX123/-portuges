# 📚 +Português - Gerenciador de Questões

Uma plataforma robusta para professores gerenciarem, organizarem e reutilizarem suas questões de forma eficiente com banco de dados centralizado.

## 🎯 Objetivo

Criar uma solução inteligente que permite professores de todas as categorias de ensino:
- **Centralizar** todas as suas questões em um único lugar (banco de dados MySQL)
- **Organizar** questões por gênero textual, tipo e especificações
- **Filtrar** questões através de filtros customizados avançados
- **Reutilizar** questões em diferentes avaliações e contextos
- **Gerenciar** histórico e status de questões (rascunho/publicada)

---

## ✨ Funcionalidades Implementadas

### 🔐 Autenticação & Segurança
- ✅ Sistema de autenticação com email/senha
- ✅ Hash seguro de senhas com `password_hash()`
- ✅ Sessões PHP para manter usuário logado
- ✅ Logout funcional
- ✅ Usuário admin pré-criado (admin@admin.com / 123)

### 📋 Gerenciamento de Questões
- ✅ Criar questões (objetivas e dissertativas)
- ✅ Editar questões existentes
- ✅ Visualizar todas as questões
- ✅ Deletar questões
- ✅ Buscar questões por título/texto
- ✅ Filtrar por tipo, gênero e status
- ✅ Upload de imagens para questões
- ✅ Explicação detalhada para cada questão

### 🏷️ Tipos de Questões
- **Questões Objetivas (Múltipla Escolha)**
  - 5 alternativas (A, B, C, D, E)
  - Resposta correta definida
  - Alternativas armazenadas em tabela separada
  
- **Questões Dissertativas**
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
- **HTML5** + **CSS3**
- **JavaScript** (Vanilla JS)
- **PHP** para renderização de templates

### Backend
- **PHP 7.4+** com **MySQLi**
- **API via PHP (endpoints HTTP)**
- **Sessões PHP** para autenticação

### Banco de Dados
- **MySQL 5.7+** ou **MariaDB**
- **3 tabelas principais**:
  - `usuarios` - Autenticação
  - `questoes` - Armazenamento de questões
  - `alternativas_objetivas` - Alternativas das questões múltipla escolha

---

## 📦 Instalação & Configuração

### Pré-requisitos
- PHP 7.4+
- MySQL/MariaDB
- Servidor HTTP (Apache/Nginx) ou PHP built-in
- Git

### Passo 1: Clonar/Acessar o Repositório

```bash
cd portuges-feature-databese
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
3. Selecione: database/mais_portugues_corrigido.sql
4. Clique em "Executar"
```

**OU via linha de comando:**

```bash
mysql -u root -p mais_portugues < database/mais_portugues_corrigido.sql
```

### Passo 4: Verificar Configuração

Edite `database/config.php` com suas credenciais MySQL:

```php
$servername = "localhost";
$usuario = "root";
$senha = "sua_senha_aqui";  // Modifique se necessário
$banco = "mais_portugues";
```

### Passo 5: Iniciar o Servidor

```bash
# Via PHP built-in (para desenvolvimento)
php -S localhost:8000

# Ou acesse via Apache/Nginx
http://localhost/caminho/para/projeto
```

---

## 🔐 Credenciais Padrão

### Usuário Admin (já inserido no banco)
- **Email**: `admin@admin.com`
- **Senha**: `123`

---

## 📁 Estrutura do Projeto

```
portuges-feature-databese/
├── beckend/                    # Backend PHP
│   ├── config.php             # Configurações globais
│   ├── helpers.php            # Classe BancoQuestoes (migrada para MySQL)
│   ├── sessao.php             # Gerenciamento de sessão
│   ├── login.php              # Autenticação
│   ├── logout.php             # Saída do sistema
│   ├── salvar_questao.php     # Criar/editar questões
│   ├── listar_questoes.php    # Listar questões com filtros
│   ├── buscar_questao.php     # Busca por ID
│   ├── excluir_questao.php    # Deletar questões
│   ├── questoes.json          # [Legado] Já não utilizado
│   ├── uploads/               # Pasta para imagens das questões
│   └── verificacao.php        # Verificações auxiliares
│
├── database/                   # Banco de Dados
│   ├── config.php             # Conexão MySQL (corrigido)
│   └── mais_portugues_corrigido.sql  # Schema atualizado
│
├── front/                      # Frontend
│   ├── tela_de_login.php      # Página de login
│   ├── home_page.php          # Dashboard principal
│   ├── criacao_de_questao_objetiva.php
│   ├── criacao_de_questao_dissertativa.php
│   ├── editar_questao.php
│   ├── aba_questao_objetiva.php
│   ├── aba_questao_dissertativa.php
│   ├── css/                    # Estilos
│   └── js/                     # Scripts JavaScript
│
├── README.md                   # Este arquivo
├── INSTALACAO.md              # Guia detalhado de instalação
├── README_CORRECCOES.md       # Histórico de correções
└── TESTE_API.md               # Exemplos de requisições
```

---

## 🗄️ Estrutura do Banco de Dados

### Tabela: `usuarios`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | ID único auto-incremental |
| email | VARCHAR UNIQUE | Email para login |
| senha | VARCHAR | Hash da senha |
| nome | VARCHAR | Nome do usuário |
| tipo | ENUM | 'professor' ou 'admin' |
| status | TINYINT | 0=inativo, 1=ativo |
| criado_em | TIMESTAMP | Data de criação |
| ultimo_login | DATETIME | Último acesso |

### Tabela: `questoes`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | ID único auto-incremental |
| titulo | VARCHAR | Título da questão |
| tipo | ENUM | 'objetiva' ou 'dissertativa' |
| status | ENUM | 'rascunho' ou 'publicada' |
| genero | ENUM | Gênero textual |
| subgenero | VARCHAR | Subcategoria |
| especificacao | VARCHAR | Especificação customizada |
| enunciado | LONGTEXT | Texto da questão |
| explicacao | LONGTEXT | Explicação da resposta |
| resposta_correta | CHAR | 'A' a 'E' (NULL para dissertativas) |
| imagem | VARCHAR | Caminho da imagem |
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

## 🔄 Status do Desenvolvimento

### ✅ Concluído
- [x] Schema MySQL simplificado e corrigido
- [x] Backend PHP com MySQLi funcional
- [x] Autenticação (login/logout)
- [x] CRUD completo de questões
- [x] Suporte a questões objetivas (com alternativas A-E)
- [x] Suporte a questões dissertativas
- [x] Upload de imagens
- [x] Busca e filtros
- [x] Frontend com interface intuitiva
- [x] Classe BancoQuestoes migrada para MySQL

### 🔄 Em Progresso
- [ ] Testes completos da API
- [ ] Documentação de endpoints
- [ ] Sistema de categorias/matérias adicional
- [ ] Filtros avançados melhorados
- [ ] Estatísticas e relatórios

### 📋 Planejado
- [ ] Exportação de questões (PDF)
- [ ] Importação em lote
- [ ] Sistema de versioning de questões
- [ ] Compartilhamento entre professores
- [ ] Interface responsiva mobile
- [ ] API RESTful documentada (Swagger)

---

## 📞 Suporte

Para testes e documentação detalhada de endpoints, consulte:
- **INSTALACAO.md** - Guia passo a passo
- **README_CORRECCOES.md** - Histórico de mudanças
- **TESTE_API.md** - Exemplos de requisições cURL
```

---

## 🚀 Como Usar

### Para Professores

1. **Criar Conta**
   - Acesse a plataforma e registre-se com seu email

2. **Organizar Estrutura**
   - Configure suas matérias
   - Crie gêneros/assuntos customizados

3. **Adicionar Questões**
   - Clique em "Nova Questão"
   - Preencha título, enunciado e resposta
   - Categorize conforme sua estrutura criada

4. **Filtrar e Buscar**
   - Use os filtros para encontrar questões rapidamente
   - Salve filtros customizados para acesso futuro

5. **Exportar/Utilizar**
   - Gere listas de questões para provas
   - Exporte em diferentes formatos

---

## 📁 (Base) - Estrutura do Projeto

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
