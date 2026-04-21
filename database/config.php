<?php
// conection to database
$servername = "localhost";
$usuaio = "root";
$senha = "";
$banco = "mais_portugues";

// criar conexao
$conecao = new mysqli($servername, $usuaio, $senha, $banco);

if ($conecao->connect_error) {
    die("Conexão falhou: " . $conecao->connect_error);
}

echo "Conexão bem-sucedida!";
?>
