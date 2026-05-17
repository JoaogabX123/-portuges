# +Português - Gerenciador de Questões

Aplicação PHP MVC para professores criarem, organizarem e reutilizarem questões objetivas e dissertativas com autenticação, upload de imagens e isolamento de dados por usuário.

## Status

- Autenticação com email e senha.
- Cadastro, login e logout.
- Sessões PHP com cookie `httponly` e `samesite=Lax`.
- CRUD de questões objetivas e dissertativas.
- Upload de imagens em `public/uploads/`.
- Busca e filtros por tipo, status, gênero e subgênero.
- Cada usuário acessa apenas as próprias questões.
- Página de configurações com atualização de perfil e alteração de senha.
- API pública centralizada em `public/api.php`.

## Documentação

| Arquivo | Conteúdo |
| --- | --- |
| [docs/INSTALACAO.md](docs/INSTALACAO.md) | Instalação no XAMPP, schema SQL e troubleshooting |
| [docs/MVC_DOCUMENTATION.md](docs/MVC_DOCUMENTATION.md) | Estrutura MVC, fluxo da API e endpoints |
| [docs/TESTE_API.md](docs/TESTE_API.md) | Exemplos de testes com curl |

## Como Acessar

Com Apache e MySQL ligados no XAMPP:

```text
http://localhost/mais_portugues/public/
```

Login:

```text
http://localhost/mais_portugues/public/?page=login
```

Cadastro:

```text
http://localhost/mais_portugues/public/?page=signup
```

## Stack

- PHP 7.4+
- MySQL/MariaDB
- MySQLi OOP
- HTML, CSS e JavaScript vanilla
- XAMPP como ambiente local

## Estrutura

```text
mais_portugues/
├── public/
│   ├── index.php
│   ├── api.php
│   ├── css/style.css
│   └── uploads/
├── app/
│   ├── config/config.php
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   └── views/
└── docs/
```

## API

Base local:

```text
http://localhost/mais_portugues/public/api.php?rota=
```

Exemplos:

```text
POST /api.php?rota=login
GET  /api.php?rota=usuarios&acao=verificar_sessao
GET  /api.php?rota=questoes&acao=listar
POST /api.php?rota=questoes&acao=salvar
POST /api.php?rota=questoes&acao=deletar
```

No frontend, as requisições usam `credentials: 'include'` para manter a sessão.

## Banco de Dados

Banco padrão:

```text
mais_portugues
```

Tabelas:

- `usuarios`
- `questoes`
- `alternativas_objetivas`

O schema completo está em [docs/INSTALACAO.md](docs/INSTALACAO.md).

## Segurança

- Senhas com `password_hash(PASSWORD_DEFAULT)`.
- Queries com prepared statements.
- Autenticação obrigatória em operações privadas.
- Questões filtradas por `id_usuario_criador`.
- Buscar, editar e excluir validam a propriedade da questão.

## Verificação Rápida

```powershell
C:\xampp\php\php.exe -l public\index.php
C:\xampp\php\php.exe -l public\api.php
C:\xampp\mysql\bin\mysql.exe -uroot -e "USE mais_portugues; SHOW TABLES;"
```

Atualizado em 17/05/2026.
