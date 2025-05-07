<?php
include("conexao.php");
session_start();

// Verifica se os campos foram enviados
if (!isset($_POST['cpf']) || empty($_POST['cpf'])) {
    header("Location: index.php?erro=cpf");
    exit;
}
if (!isset($_POST['senha']) || empty($_POST['senha'])) {
    header("Location: index.php?erro=senha");
    exit;
}

$cpf = $_POST["cpf"];
$senha = $_POST["senha"];

$sql = "SELECT nome FROM usuarios WHERE cpf = '$cpf' AND senha = '$senha'";
$resultado = $conn->query($sql);
$row = $resultado->fetch_assoc();

if ($row && !empty($row['nome'])) {
    $_SESSION["cpf"] = $cpf;
    $_SESSION["senha"] = $senha;
    $_SESSION["nome"] = $row['nome'];
    header("Location: principal.php");
    exit;
} else {
    header("Location: index.php?erro=login");
    exit;
}
?> 
<a href="register.php">Cadastrar novo usuário</a>