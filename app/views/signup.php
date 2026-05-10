<?php
/**
 * VIEW: Cadastro (Signup)
 * GET /app/views/signup.php
 */

// Sessão já foi iniciada em config.php
if (isset($_SESSION['usuario_id'])) {
    header('Location: ./?page=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - +Português</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>';</script>
</head>
<body>
    <div class="container-form">
        <div class="header-mini">
            <h1>+Português</h1>
            <p>Gerenciador de Questões</p>
        </div>

        <div class="signup-card">
            <h2>Cadastro</h2>
            <p>Crie sua conta para começar</p>

            <form id="form_signup" onsubmit="fazerCadastro(event)">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" id="nome" placeholder="Seu nome..." required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" placeholder="seu@email.com" required>
                </div>

                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" id="senha" placeholder="Mínimo 8 caracteres..." required>
                    <div class="password-strength">
                        <div class="password-strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-length">
                            <span class="requirement-icon"></span> 8+ caracteres
                        </div>
                        <div class="requirement" id="req-upper">
                            <span class="requirement-icon"></span> Uma maiúscula
                        </div>
                        <div class="requirement" id="req-lower">
                            <span class="requirement-icon"></span> Uma minúscula
                        </div>
                        <div class="requirement" id="req-number">
                            <span class="requirement-icon"></span> Um número
                        </div>
                    </div>
                </div>

                <div class="erro" id="msg_erro" style="display: none;"></div>

                <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
            </form>

            <p class="login-link">Já tem conta? <a href="?page=login">Faça login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('senha').addEventListener('input', (e) => {
            const senha = e.target.value;
            
            const temLength = senha.length >= 8;
            const temMaiuscula = /[A-Z]/.test(senha);
            const temMinuscula = /[a-z]/.test(senha);
            const temNumero = /\d/.test(senha);
            
            document.getElementById('req-length').classList.toggle('met', temLength);
            document.getElementById('req-upper').classList.toggle('met', temMaiuscula);
            document.getElementById('req-lower').classList.toggle('met', temMinuscula);
            document.getElementById('req-number').classList.toggle('met', temNumero);
            
            const requisitos = [temLength, temMaiuscula, temMinuscula, temNumero];
            const atendidos = requisitos.filter(r => r).length;
            const percentual = (atendidos / 4) * 100;
            
            const fill = document.getElementById('strength-fill');
            fill.style.width = percentual + '%';
            
            if (percentual <= 25) {
                fill.style.backgroundColor = '#f44336';
            } else if (percentual <= 50) {
                fill.style.backgroundColor = '#ff9800';
            } else if (percentual <= 75) {
                fill.style.backgroundColor = '#ffc107';
            } else {
                fill.style.backgroundColor = '#4caf50';
            }
        });

        async function fazerCadastro(event) {
            event.preventDefault();
            
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;

            const el_erro = document.getElementById('msg_erro');
            el_erro.style.display = 'none';

            try {
                const res = await fetch(`${BASE_URL}app/routes/usuarios.php?acao=criar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, email, senha })
                });
                
                const data = await res.json();

                if (data.ok) {
                    alert('Cadastro realizado com sucesso! Faça login agora.');
                    window.location.href = './?page=login';
                } else {
                    if (data.erros && Array.isArray(data.erros)) {
                        el_erro.textContent = data.erros.join('\n');
                    } else {
                        el_erro.textContent = data.erro || 'Erro ao cadastrar';
                    }
                    el_erro.style.display = 'block';
                }
            } catch (erro) {
                el_erro.textContent = 'Erro de conexão: ' + erro.message;
                el_erro.style.display = 'block';
            }
        }
    </script>
</body>
</html>
