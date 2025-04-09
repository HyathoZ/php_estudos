<?php
    // Conexão com o banco de dados
    // Defina as credenciais do banco de dados
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $dbname = "cadastro_filmes";
    // Cria a conexão
    // Cria a conexão com o banco de dados
    $conn = new mysqli($servidor,$usuario,$senha,$dbname);
    if($conn->connect_error){
        die("Falha na conexão".$conn->connect_error);
    }

?>