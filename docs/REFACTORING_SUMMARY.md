# Sumário de Refatoração MVC

Este documento resume o estado atual da refatoração do +Português.

## Resultado

O projeto foi organizado em uma arquitetura MVC simples:

- `public/index.php`: router das telas.
- `public/api.php`: entrada pública da API.
- `app/config/config.php`: configuração global, sessão, banco, helpers e autoload.
- `app/models`: acesso ao banco.
- `app/controllers`: regras de negócio.
- `app/routes`: dispatchers internos da API.
- `app/views`: telas da aplicação.

## Melhorias Entregues

- Autenticação por sessão PHP.
- Cadastro, login e logout.
- CRUD completo de questões.
- Questões objetivas com alternativas A-E.
- Questões dissertativas.
- Upload de imagens para `public/uploads/`.
- Busca e filtros no dashboard.
- Página de configurações com edição de perfil e alteração de senha.
- API pública centralizada em `public/api.php`.
- Isolamento de dados por usuário.
- Validação de propriedade ao buscar, editar e excluir questões.

## Segurança

- Senhas armazenadas com `password_hash(PASSWORD_DEFAULT)`.
- Login com `password_verify()`.
- Prepared statements com MySQLi.
- Sessão iniciada antes do output.
- Cookies com `httponly` e `samesite=Lax`.
- Upload validado por MIME type, extensão e tamanho máximo.
- Questões sempre filtradas por `id_usuario_criador`.

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
├── docs/
└── README.md
```

## Endereço Local

```text
http://localhost/mais_portugues/public/
```

## API

Base:

```text
http://localhost/mais_portugues/public/api.php?rota=
```

Rotas:

- `login`
- `logout`
- `usuarios`
- `questoes`

## Documentos Atualizados

- `README.md`
- `docs/INSTALACAO.md`
- `docs/MVC_DOCUMENTATION.md`
- `docs/TESTE_API.md`
- `docs/REFACTORING_SUMMARY.md`

Atualizado em 17/05/2026.
