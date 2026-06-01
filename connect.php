<?php

$host ="localhost";
$porta = "3307";
$banco ="aula_univille";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta; dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );
    echo "Deu certo meu bom, agora já podemos se jogar da ponte. VAMOOOOOOOOO";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}


