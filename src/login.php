<?php
include("conexao.php");

// Inicia a sessão no começo
session_start();

// Verifica se os campos foram enviados
if (!isset($_POST['cpf']) || empty($_POST['cpf'])) {
    die("Insira um CPF.");
}
if (!isset($_POST['senha']) || empty($_POST['senha'])) {
    die("Insira uma senha.");
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
    echo "Senha incorreta ou usuário não encontrado.";
}
?>
