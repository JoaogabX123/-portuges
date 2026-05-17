# Guia de Instalação - +Português

Este projeto é uma aplicação PHP MVC para gerenciar questões objetivas e dissertativas com autenticação por sessão, upload de imagens e isolamento de dados por usuário.

## Requisitos

- XAMPP com Apache, PHP 7.4+ e MySQL/MariaDB
- Navegador moderno
- Projeto dentro de `C:\xampp\htdocs\mais_portugues`

## 1. Banco de Dados

Crie o banco pelo phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Nome do banco:

```text
mais_portugues
```

Charset recomendado:

```text
utf8mb4_general_ci
```

Ou pelo terminal:

```bash
C:\xampp\mysql\bin\mysql.exe -uroot -e "CREATE DATABASE mais_portugues CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

## 2. Schema SQL

Execute este SQL no banco `mais_portugues`:

```sql
CREATE TABLE IF NOT EXISTS usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(120) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  nome VARCHAR(100) NOT NULL,
  tipo ENUM('professor', 'admin') NOT NULL DEFAULT 'professor',
  status TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ultimo_login DATETIME NULL
);

CREATE TABLE IF NOT EXISTS questoes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(255) NOT NULL,
  tipo ENUM('objetiva', 'dissertativa') NOT NULL,
  status ENUM('rascunho', 'publicada') NOT NULL DEFAULT 'rascunho',
  genero VARCHAR(100) NOT NULL,
  subgenero VARCHAR(100) NULL,
  especificacao VARCHAR(100) NULL,
  enunciado LONGTEXT NOT NULL,
  explicacao LONGTEXT NULL,
  resposta_correta CHAR(1) NULL,
  imagem VARCHAR(255) NULL,
  id_usuario_criador INT NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_questoes_usuario (id_usuario_criador),
  CONSTRAINT fk_questoes_usuario
    FOREIGN KEY (id_usuario_criador) REFERENCES usuarios(id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS alternativas_objetivas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_questao INT NOT NULL,
  alternativa CHAR(1) NOT NULL,
  texto LONGTEXT NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_alternativas_questao (id_questao),
  CONSTRAINT fk_alternativas_questao
    FOREIGN KEY (id_questao) REFERENCES questoes(id)
    ON DELETE CASCADE
);
```

## 3. Configuração

A configuração fica em:

```text
app/config/config.php
```

Valores padrão para XAMPP:

```php
$db_config = [
    'servername' => 'localhost',
    'usuario'    => 'root',
    'senha'      => '',
    'banco'      => 'mais_portugues',
    'port'       => 3306,
    'charset'    => 'utf8mb4'
];
```

Se o seu MySQL tiver senha, altere o campo `senha`.

## 4. Acessar

Com Apache e MySQL ligados no XAMPP, abra:

```text
http://localhost/mais_portugues/public/
```

Login direto:

```text
http://localhost/mais_portugues/public/?page=login
```

Cadastro:

```text
http://localhost/mais_portugues/public/?page=signup
```

## 5. API Pública

O frontend usa uma entrada única:

```text
public/api.php
```

Formato:

```text
http://localhost/mais_portugues/public/api.php?rota=usuarios&acao=verificar_sessao
```

Rotas disponíveis:

- `rota=login`
- `rota=logout`
- `rota=usuarios`
- `rota=questoes`

## 6. Usuário de Teste

Você pode criar um usuário pela tela de cadastro. Se quiser inserir manualmente:

```sql
INSERT INTO usuarios (email, senha, nome, tipo, status) VALUES (
  'teste@teste.com',
  '$2y$10$6RYIekPXSIWWp7w7EF7WaOqF9HXEzaAMwFfGtDJZUlRZy7xP0NYxC',
  'Usuário Teste',
  'professor',
  1
);
```

Credenciais:

- Email: `teste@teste.com`
- Senha: `Teste@123`

## Troubleshooting

- `Class "mysqli" not found`: use o PHP do XAMPP ou habilite a extensão `mysqli`.
- Erro de conexão: confirme se o MySQL está ligado e se `app/config/config.php` está correto.
- Página abre, mas ações falham: verifique se a URL está em `/mais_portugues/public/`.
- Upload não funciona: confirme se existe a pasta `public/uploads/` e se o Apache pode gravar nela.
- Sessão expirada: faça login novamente.

## Verificação Rápida

No PowerShell:

```powershell
C:\xampp\php\php.exe -l public\index.php
C:\xampp\php\php.exe -l public\api.php
C:\xampp\mysql\bin\mysql.exe -uroot -e "USE mais_portugues; SHOW TABLES;"
```

Versão atualizada em 17/05/2026.
