# Teste Manual da API

Este arquivo contém exemplos de requisições para testar toda a API do projeto.

## 1. Login

### Requisição
```bash
curl -X POST http://localhost/Projeto\ +Portugues/portuges-feature-databese/beckend/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@admin.com",
    "senha": "123"
  }'
```

### Resposta Esperada
```json
{
  "ok": true,
  "mensagem": "Login realizado com sucesso"
}
```

---

## 2. Criar Questão (Objetiva)

### Requisição
```bash
curl -X POST http://localhost/.../beckend/salvar_questao.php \
  -H "Cookie: PHPSESSID=seu_session_id_aqui" \
  -F "id=" \
  -F "tipo=objetiva" \
  -F "acao=salvar" \
  -F "titulo=O que é semântica?" \
  -F "genero=descritivo" \
  -F "enunciado=Semântica é o estudo do significado das palavras. Qual alternativa melhor define?" \
  -F "explicacao=Semântica estuda o significado dos signos linguísticos." \
  -F "especificacao=Conceitos linguísticos" \
  -F "subgenero=Definição" \
  -F "correta=A" \
  -F "alt_A=Estudo do significado das palavras" \
  -F "alt_B=Estudo da pronúncia" \
  -F "alt_C=Estudo da gramática" \
  -F "alt_D=Estudo da ortografia" \
  -F "alt_E=Estudo da sintaxe"
```

### Resposta Esperada
```json
{
  "ok": true,
  "mensagem": "Questão criada com sucesso",
  "dados": {
    "id": 1
  }
}
```

---

## 3. Criar Questão (Dissertativa)

### Requisição
```bash
curl -X POST http://localhost/.../beckend/salvar_questao.php \
  -H "Cookie: PHPSESSID=seu_session_id_aqui" \
  -F "id=" \
  -F "tipo=dissertativa" \
  -F "acao=salvar" \
  -F "titulo=Análise de Texto" \
  -F "genero=argumentativo" \
  -F "enunciado=Leia o texto a seguir e faça uma análise crítica..." \
  -F "explicacao=A resposta esperada deve considerar os pontos principais..." \
  -F "especificacao=Análise textual" \
  -F "subgenero=Crítica"
```

---

## 4. Listar Questões

### Requisição
```bash
# Todas as questões
curl http://localhost/.../beckend/listar_questoes.php

# Com filtros
curl "http://localhost/.../beckend/listar_questoes.php?tipo=objetiva&status=publicada"

# Com busca
curl "http://localhost/.../beckend/listar_questoes.php?busca=semântica"
```

### Resposta Esperada
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
        "enunciado": "Semântica é...",
        "explicacao": "Semântica estuda...",
        "resposta_correta": "A",
        "alternativas": {
          "A": "Estudo do significado das palavras",
          "B": "Estudo da pronúncia",
          "C": "Estudo da gramática",
          "D": "Estudo da ortografia",
          "E": "Estudo da sintaxe"
        }
      }
    ]
  }
}
```

---

## 5. Buscar Questão por ID

### Requisição
```bash
curl "http://localhost/.../beckend/buscar_questao.php?id=1"
```

### Resposta Esperada
```json
{
  "ok": true,
  "dados": {
    "id": 1,
    "titulo": "O que é semântica?",
    "tipo": "objetiva",
    ...
  }
}
```

---

## 6. Atualizar Questão

### Requisição
```bash
curl -X POST http://localhost/.../beckend/salvar_questao.php \
  -H "Cookie: PHPSESSID=seu_session_id_aqui" \
  -F "id=1" \
  -F "tipo=objetiva" \
  -F "acao=salvar" \
  -F "titulo=O que é SEMÂNTICA? (ATUALIZADO)" \
  -F "genero=descritivo" \
  -F "enunciado=Semântica é o estudo do significado das palavras..." \
  -F "explicacao=Explicação atualizada..." \
  -F "correta=A" \
  -F "alt_A=Estudo do significado das palavras" \
  -F "alt_B=Estudo da pronúncia" \
  -F "alt_C=Estudo da gramática" \
  -F "alt_D=Estudo da ortografia" \
  -F "alt_E=Estudo da sintaxe"
```

---

## 7. Deletar Questão

### Requisição
```bash
curl -X POST http://localhost/.../beckend/excluir_questao.php \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=seu_session_id_aqui" \
  -d '{"id": 1}'
```

### Resposta Esperada
```json
{
  "ok": true,
  "mensagem": "Questão deletada com sucesso"
}
```

---

## 8. Verificar Sessão

### Requisição
```bash
curl -X POST http://localhost/.../beckend/sessao.php \
  -H "Cookie: PHPSESSID=seu_session_id_aqui"
```

### Resposta Esperada
```json
{
  "ok": true,
  "mensagem": "Usuário autenticado",
  "dados": {
    "usuario_id": 1,
    "usuario_email": "admin@admin.com",
    "tempo_sessao": 3600
  }
}
```

---

## 9. Logout

### Requisição
```bash
curl -X POST http://localhost/.../beckend/logout.php \
  -H "Cookie: PHPSESSID=seu_session_id_aqui"
```

### Resposta Esperada
```json
{
  "ok": true,
  "mensagem": "Logout realizado com sucesso"
}
```

---

## Usando Postman

1. Importe as requisições acima no Postman
2. No primeiro login, salve o cookie PHPSESSID
3. Use o ambiente Postman para adicionar a variável `{{base_url}}`
4. Teste cada endpoint em sequência

## Usando cURL com Cookies

```bash
# Login e salva cookies
curl -c cookies.txt -X POST http://localhost/.../beckend/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@admin.com","senha":"123"}'

# Usar cookies em requisições subsequentes
curl -b cookies.txt http://localhost/.../beckend/sessao.php
```

---

**Nota**: Substitua `http://localhost/.../` pelo caminho correto do seu projeto.
