<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>+Português – Login</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(30deg, cyan, rgb(35, 115, 235));
        }
        div {
            background-color: rgba(0, 0, 0, 0.8);
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            padding: 75px;
            border-radius: 15px;
            color: aliceblue;
        }
        h1 { text-align: center; }
        input {
            padding: 15px;
            border: none;
            outline: none;
            font-size: 15px;
            width: 100%;
            border-radius: 5px;
        }
        button {
            background-color: rgb(82, 202, 250);
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover { background-color: rgb(60, 141, 248); }
        .erro {
            background: rgba(200, 50, 50, 0.85);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            display: none;
        }
    </style>
</head>
<body>
    <div>
        <h1>Login</h1>
        <div class="erro" id="msg_erro"></div>
        <input type="email" id="email" placeholder="E-mail">
        <br><br>
        <input type="password" id="senha" placeholder="Senha">
        <br><br>
        <button onclick="fazerLogin()">Entrar</button>
    </div>

    <script>
        const BASE = '../beckend';

        async function fazerLogin() {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();

            const res  = await fetch(`${BASE}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, senha })
            });
            const data = await res.json();

            if (data.ok) {
                window.location.href = 'home_page.php';
            } else {
                const el = document.getElementById('msg_erro');
                el.textContent = data.erro;
                el.style.display = 'block';
            }
        }

        // Permite pressionar Enter para logar
        document.addEventListener('keydown', e => { if (e.key === 'Enter') fazerLogin(); });
    </script>
</body>
</html>