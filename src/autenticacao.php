<?php

session_start();
// Verifica se o usuário está logado
// Se não estiver, redireciona para a página de login
if(!isset($_SESSION['cpf']) || $_SESSION['cpf'] == ''){
    header("Location: index.php");
    die;
}
// Verifica se a senha está definida
// Se não estiver, redireciona para a página de login
if(!isset($_SESSION['senha']) || $_SESSION['senha'] == ''){
    header("Location: index.php");
    die;
}

?>