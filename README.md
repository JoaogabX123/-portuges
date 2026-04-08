# 📚 Banco de Questões Docente

Uma plataforma moderna para professores gerenciarem, organizarem e reutilizarem suas questões de forma eficiente.

## 🎯 Objetivo

Criar uma solução inteligente que permite professores de todas as categorias de ensino:
- **Centralizar** todas as suas questões em um único lugar
- **Organizar** questões por matéria, tipo, gênero e assunto customizado
- **Filtrar** questões através de filtros customizados avançados
- **Reutilizar** questões em diferentes avaliações e contextos
- **Gerenciar** versões e histórico de questões

---

## ✨ Funcionalidades Principais

### 🔐 Autenticação & Segurança
- Sistema de autenticação com email/senha
- Validação de email
- Tokens JWT para sessões seguras
- Soft delete para preservar histórico

### 📋 Gerenciamento de Questões
- Criar, editar, visualizar e deletar questões
- Suporte para múltiplos tipos:
  - Questões Objetivas (múltipla escolha)
  - Questões Discursivas
  - Questões Dissertativas
- Marcar questões favoritas
- Histórico de modificações

### 🏷️ Organização Inteligente
- **Estrutura hierárquica**: Matéria → Gênero → Assunto
- Criar categorias e filtros customizados
- Filtro rápido por matéria, tipo e dificuldade
- Busca avançada de questões

### 📊 Dashboard
- Visualização intuitiva de todas as questões
- Estatísticas sobre questões criadas
- Acesso rápido a questões recentes

---

## 🛠️ Tech Stack

### Frontend
- **React** + Vite
- **CSS** para estilização responsiva
- **JavaScript** moderno (ES6+)

### Backend
- **Node.js / Python** (a ser definido)
- **API RESTful**
- **JWT** para autenticação

### Banco de Dados
- **PostgreSQL / MySQL / MongoDB** (a ser definido)
- **Schema relacional** para hierarquia de matérias/assuntos

---

## 📦 Instalação

### Pré-requisitos
- Node.js v16+ ou Python 3.8+
- npm ou pip
- Git

### Setup Frontend

```bash
# Clonar repositório
git clone https://github.com/seu-usuario/banco-questoes.git
cd banco-questoes

# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

### Setup Backend

```bash
# Instalar dependências (Python)
pip install -r requirements.txt

# Ou (Node.js)
npm install

# Configurar banco de dados
# Adicionar variáveis de ambiente em .env

# Iniciar servidor
python app.py
# Ou
npm start
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
