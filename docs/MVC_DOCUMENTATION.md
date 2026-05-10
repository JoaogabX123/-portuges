# +Português - Documentação MVC

## 📁 Estrutura do Projeto

O projeto foi refatorado seguindo o padrão **MVC (Model-View-Controller)**:

```
Projeto +Portugues/
├── index.php                          # Entry point - redireciona para login/home
├── public/
│   ├── css/
│   │   └── style.css                 # Estilos unificados
│   ├── js/
│   │   └── api.js                    # (futuro) Cliente API centralizado
│   └── uploads/                       # Diretório para uploads de imagens
├── app/
│   ├── config/
│   │   └── config.php                # Configuração unificada + autoloader
│   ├── models/
│   │   ├── Usuario.php               # CRUD de usuários + autenticação
│   │   ├── Questao.php               # CRUD de questões com transações
│   │   └── Alternativa.php           # Gerenciamento de alternativas
│   ├── controllers/
│   │   ├── LoginController.php       # Autenticação de usuários
│   │   ├── LogoutController.php      # Logout
│   │   ├── SessaoController.php      # Verificação de sessão + criação de usuário
│   │   └── QuestaoController.php     # CRUD de questões com upload de imagem
│   ├── routes/
│   │   ├── login.php                 # Dispatcher para login
│   │   ├── logout.php                # Dispatcher para logout
│   │   ├── usuarios.php              # Dispatcher para operações de usuário
│   │   └── questoes.php              # Dispatcher para operações de questão
│   └── views/
│       ├── login.php                 # Formulário de login
│       ├── signup.php                # Formulário de cadastro
│       ├── home.php                  # Dashboard com lista de questões
│       ├── editar_questao.php        # Edição de questão
│       ├── criacao_objetiva.php      # Criação de questão objetiva
│       ├── criacao_dissertativa.php  # Criação de questão dissertativa
│       └── abas/
│           ├── questao_objetiva.php  # Visualização de questão objetiva
│           └── questao_dissertativa.php # Visualização de questão dissertativa
└── database/
    └── config.php                    # Configuração do banco (schema)
```

## 🔄 Fluxo de Dados

```
┌─────────────────────────────────────────────────┐
│                    index.php                     │
│          (Entry point - redirect baseado      │
│           na sessão do usuário)                 │
└────────────────┬────────────────────────────────┘
                 │
         ┌───────┴────────┐
         │                │
    [Autenticado]    [Sem Login]
         │                │
    home.php         login.php
         │                │
         └───────┬────────┘
                 │
          ┌──────▼──────────────────────────────────────┐
          │   app/routes/*.php                         │
          │ (Dispatcher - recebe parâmetro "acao")     │
          └──────┬──────────────────────────────────────┘
                 │
          ┌──────▼──────────────────────────────────────┐
          │   app/controllers/*Controller.php           │
          │ (Lógica de negócio - processa requisição) │
          └──────┬──────────────────────────────────────┘
                 │
          ┌──────▼──────────────────────────────────────┐
          │   app/models/*.php                         │
          │ (Acesso ao banco de dados)                │
          └──────────────────────────────────────────────┘
```

## 📡 Endpoints da API

### Login
```
POST /app/routes/login.php
Content-Type: application/json

{
  "email": "usuario@example.com",
  "senha": "senha123"
}

Resposta:
{
  "ok": true,
  "mensagem": "Login realizado com sucesso"
}
```

### Logout
```
GET /app/routes/logout.php

Resposta:
{
  "ok": true,
  "mensagem": "Logout realizado com sucesso"
}
```

### Verificar Sessão
```
GET /app/routes/usuarios.php?acao=verificar_sessao

Resposta:
{
  "usuario_id": "123",
  "usuario_email": "usuario@example.com",
  "usuario_nome": "Nome do Usuário",
  "tempo_sessao": 3600
}
```

### Criar Usuário
```
POST /app/routes/usuarios.php?acao=criar
Content-Type: application/json

{
  "nome": "Novo Usuário",
  "email": "novo@example.com",
  "senha": "Senha@123"
}

Resposta:
{
  "ok": true,
  "mensagem": "Usuário criado com sucesso"
}
```

### Listar Questões
```
GET /app/routes/questoes.php?acao=listar&tipo=&status=&genero=&busca=

Parâmetros (opcionais):
- tipo: "objetiva" ou "dissertativa"
- status: "rascunho" ou "publicada"
- genero: narrativo, argumentativo, descritivo, expositivo, instrucional
- busca: string de busca

Resposta:
{
  "ok": true,
  "dados": [
    {
      "id": "1",
      "titulo": "Questão 1",
      "tipo": "objetiva",
      "genero": "narrativo",
      ...
    }
  ]
}
```

### Buscar Questão por ID
```
GET /app/routes/questoes.php?acao=buscar&id=X

Resposta:
{
  "ok": true,
  "dados": {
    "id": "1",
    "titulo": "Questão",
    "tipo": "objetiva",
    "enunciado": "...",
    "alternativas": {"A": "...", "B": "...", ...},
    "correta": "A",
    "explicacao": "...",
    "imagem": "public/uploads/..."
  }
}
```

### Salvar Questão (Create/Update)
```
POST /app/routes/questoes.php?acao=salvar
Content-Type: multipart/form-data

Campos:
- id (opcional, para update)
- tipo: "objetiva" ou "dissertativa"
- acao: "salvar" ou "postar"
- titulo: string
- genero: string
- enunciado: string
- especificacao: string (opcional)
- subgenero: string (opcional)
- explicacao: string (opcional)
- alt_A, alt_B, alt_C, alt_D, alt_E (para objetiva)
- correta: "A"-"E" (para objetiva)
- imagem: file (opcional)

Resposta:
{
  "ok": true,
  "id": "2",
  "mensagem": "Questão salva com sucesso"
}
```

### Deletar Questão
```
POST /app/routes/questoes.php?acao=deletar
Content-Type: application/json

{
  "id": "1"
}

Resposta:
{
  "ok": true,
  "mensagem": "Questão deletada com sucesso"
}
```

## 🔐 Autenticação

- Senhas são armazenadas com `password_hash(PASSWORD_DEFAULT)`
- Todas as requisições usam `credentials: 'include'` para manter sessão
- Verificação de autenticação obrigatória em controllers críticos
- User isolation: cada usuário só vê suas próprias questões

## 📝 Padrões do Código

### Models
```php
class Usuario {
    public static function buscarPorEmail($email) { }
    public static function buscarPorId($id) { }
    public static function criar($dados) { }
}
```

### Controllers
```php
class LoginController {
    public static function fazer_login() {
        // Processa e valida
        // Chama Model
        // Retorna JSON
    }
}
```

### Routes
```php
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    LoginController::fazer_login();
}
```

### Helpers (em config.php)
```php
resposta_sucesso($dados, $mensagem = '')
resposta_erro($erro)
resposta_validacao($erros)
sanitizarTexto($texto)
obterDadosJSON()
verificarAutenticacao()
```

## 🗄️ Banco de Dados

### Tabelas
- `usuarios` - Usuários do sistema
- `questoes` - Questões criadas
- `alternativas_objetivas` - Alternativas de questões objetivas

### User Isolation
Todas as queries filtram por `id_usuario_criador` obrigatoriamente, garantindo que usuários só acessem seus próprios dados.

## 🚀 Como Usar

1. **Acessar o sistema**: Abra `http://localhost/Projeto%20+Portugues/index.php`
2. **Login**: Use credenciais de usuário cadastrado
3. **Dashboard**: Visualize e gerencie suas questões
4. **Criar questão**: Clique no botão "+ Adicionar questão"
5. **Editar/Visualizar**: Clique na questão desejada

## ⚙️ Configuração

Edite `app/config/config.php` para:
- Credenciais do banco de dados
- Constantes de aplicação
- Limite de tamanho de upload
- Tipos de arquivo permitidos

## 📋 Validações

- Emails únicos no sistema
- Senhas com mínimo 8 caracteres (letra maiúscula, minúscula, número)
- Questões objetivas requerem 5 alternativas e 1 correta
- Uploads limitados a 5MB
- Formatos de imagem: JPEG, PNG, WebP, GIF

## 🐛 Troubleshooting

- **Erro de conexão ao banco**: Verifique `app/config/config.php`
- **Upload não funciona**: Verifique permissões de `public/uploads/`
- **Sessão expirada**: Aumente `TEMPO_SESSAO` em `config.php`
- **Erro 500**: Habilite `DEBUG_MODE: true` em `config.php` para logs

---

**Versão**: 1.0 MVC Refactor
**Data**: 2024
**Padrão**: PHP 7.4+ com MySQLi OOP
