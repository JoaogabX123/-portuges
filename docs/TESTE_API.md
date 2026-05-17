# Teste Manual da API

Base local:

```text
http://localhost/mais_portugues/public/api.php?rota=
```

Os exemplos abaixo usam `curl` com cookie jar para manter a sessão.

## 1. Criar Usuário

```bash
curl -c cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=usuarios&acao=criar" ^
  -H "Content-Type: application/json" ^
  -d "{\"nome\":\"Usuário Teste\",\"email\":\"teste@teste.com\",\"senha\":\"Teste@123\"}"
```

Resposta esperada:

```json
{
  "ok": true,
  "mensagem": "Cadastro realizado com sucesso"
}
```

Se o usuário já existir, use outro email ou faça login.

## 2. Login

```bash
curl -c cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"teste@teste.com\",\"senha\":\"Teste@123\"}"
```

Resposta esperada:

```json
{
  "ok": true,
  "mensagem": "Login realizado com sucesso"
}
```

## 3. Verificar Sessão

```bash
curl -b cookies.txt "http://localhost/mais_portugues/public/api.php?rota=usuarios&acao=verificar_sessao"
```

Resposta esperada:

```json
{
  "ok": true,
  "mensagem": "Usuario autenticado",
  "dados": {
    "usuario_id": 1,
    "usuario_email": "teste@teste.com",
    "usuario": {
      "id": 1,
      "email": "teste@teste.com",
      "nome": "Usuário Teste"
    }
  }
}
```

## 4. Criar Questão Objetiva

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=salvar" ^
  -F "tipo=objetiva" ^
  -F "acao=salvar" ^
  -F "titulo=O que é semântica?" ^
  -F "genero=descritivo" ^
  -F "enunciado=Semântica é o estudo do significado das palavras. Qual alternativa melhor define?" ^
  -F "explicacao=Semântica estuda o significado dos signos linguísticos." ^
  -F "especificacao=Conceitos linguísticos" ^
  -F "subgenero=Definição" ^
  -F "correta=A" ^
  -F "alt_A=Estudo do significado das palavras" ^
  -F "alt_B=Estudo da pronúncia" ^
  -F "alt_C=Estudo da gramática" ^
  -F "alt_D=Estudo da ortografia" ^
  -F "alt_E=Estudo da sintaxe"
```

Resposta esperada:

```json
{
  "ok": true,
  "mensagem": "Questão criada com sucesso",
  "dados": {
    "id": 1
  }
}
```

## 5. Criar Questão Dissertativa

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=salvar" ^
  -F "tipo=dissertativa" ^
  -F "acao=salvar" ^
  -F "titulo=Análise de texto" ^
  -F "genero=argumentativo" ^
  -F "enunciado=Leia o texto e produza uma análise crítica." ^
  -F "explicacao=A resposta deve considerar tese, argumentos e conclusão." ^
  -F "especificacao=Análise textual" ^
  -F "subgenero=Crítica"
```

## 6. Listar Questões

```bash
curl -b cookies.txt "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=listar"
```

Com filtros:

```bash
curl -b cookies.txt "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=listar&tipo=objetiva&status=rascunho&busca=semântica"
```

Resposta esperada:

```json
{
  "ok": true,
  "dados": {
    "total": 1,
    "questoes": [
      {
        "id": 1,
        "titulo": "O que é semântica?",
        "tipo": "objetiva",
        "status": "rascunho",
        "genero": "descritivo",
        "alternativas": {
          "A": "Estudo do significado das palavras"
        },
        "correta": "A"
      }
    ]
  }
}
```

## 7. Buscar Questão por ID

Troque `1` pelo ID retornado na criação/listagem:

```bash
curl -b cookies.txt "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=buscar&id=1"
```

## 8. Atualizar Questão

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=salvar" ^
  -F "id=1" ^
  -F "tipo=objetiva" ^
  -F "acao=postar" ^
  -F "titulo=O que é semântica? (atualizada)" ^
  -F "genero=descritivo" ^
  -F "enunciado=Qual alternativa melhor define semântica?" ^
  -F "explicacao=Semântica estuda significados." ^
  -F "correta=A" ^
  -F "alt_A=Estudo do significado das palavras" ^
  -F "alt_B=Estudo da pronúncia" ^
  -F "alt_C=Estudo da gramática" ^
  -F "alt_D=Estudo da ortografia" ^
  -F "alt_E=Estudo da sintaxe"
```

## 9. Deletar Questão

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=questoes&acao=deletar" ^
  -H "Content-Type: application/json" ^
  -d "{\"id\":1}"
```

Resposta esperada:

```json
{
  "ok": true,
  "mensagem": "Questão deletada com sucesso"
}
```

## 10. Atualizar Perfil

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=usuarios&acao=atualizar_perfil" ^
  -H "Content-Type: application/json" ^
  -d "{\"nome\":\"Usuário Atualizado\",\"email\":\"teste@teste.com\"}"
```

## 11. Alterar Senha

```bash
curl -b cookies.txt -X POST "http://localhost/mais_portugues/public/api.php?rota=usuarios&acao=alterar_senha" ^
  -H "Content-Type: application/json" ^
  -d "{\"senha_atual\":\"Teste@123\",\"nova_senha\":\"NovaSenha1\"}"
```

## 12. Logout

```bash
curl -b cookies.txt "http://localhost/mais_portugues/public/api.php?rota=logout"
```

## Notas

- Sempre use `-b cookies.txt` depois do login.
- No JavaScript, use `credentials: 'include'`.
- Buscar, editar e deletar só funcionam para questões do usuário logado.
- Uma resposta `401` significa sessão ausente ou expirada.

Atualizado em 17/05/2026.
