<?php
/**
 * VIEW: Login
 * Página de autenticação de usuários
 * GET /app/views/login.php
 */

// Sessão já foi iniciada em config.php
if (isset($_SESSION['usuario_id'])) {
    header('Location: ./?page=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>+Português – Login</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>const BASE_URL = '<?php echo BASE_URL; ?>'; const API_URL = '<?php echo API_URL; ?>'; const UPLOAD_URL = '<?php echo UPLOAD_URL; ?>';</script>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>+Português</h1>
            <div class="erro" id="msg_erro"></div>
            
            <form id="form_login" onsubmit="fazerLogin(event)">
                <div class="form-group">
                    <input type="email" id="email" placeholder="E-mail" required>
                </div>
                
                <div class="form-group">
                    <input type="password" id="senha" placeholder="Senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            
            <p class="signup-link">Não tem conta? <a href="?page=signup">Cadastre-se</a></p>
        </div>
    </div>

    <script>
        async function fazerLogin(event) {
            event.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();

            try {
                const res = await fetch(`${API_URL}login`, {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, senha })
                });
                
                const data = await res.json();

                if (data.ok) {
                    // Redirecionar imediatamente, sem delay
                    window.location.href = './?page=home';
                    return;
                } else {
                    const el = document.getElementById('msg_erro');
                    el.textContent = data.erro || 'Erro ao fazer login';
                    el.style.display = 'block';
                }
            } catch (erro) {
                console.error('Erro no login:', erro);
                const el = document.getElementById('msg_erro');
                el.textContent = 'Erro na resposta do servidor: ' + erro.message;
                el.style.display = 'block';
            }
        }
    </script>
</body>
</html>
