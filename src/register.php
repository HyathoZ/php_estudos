<?php
include("conexao.php");
session_start();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $cpf = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($cpf) || empty($senha)) {
        $mensagemErro = "Todos os campos são obrigatórios.";
    } else {
        // Verifica se o CPF já está cadastrado
        $sqlVerifica = "SELECT cpf FROM usuarios WHERE cpf = '$cpf'";
        $resultado = $conn->query($sqlVerifica);

        if ($resultado->num_rows > 0) {
            $mensagemErro = "⚠️ Este CPF já está cadastrado.";
        } else {
            // Insere os dados no banco de dados
            $sql = "INSERT INTO usuarios (nome, cpf, senha) VALUES ('$nome', '$cpf', '$senha')";
            if ($conn->query($sql) === TRUE) {
                $mensagemSucesso = "✅ Usuário cadastrado com sucesso!";
            } else {
                $mensagemErro = "Erro ao cadastrar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Cadastro</h2>

            <?php if (!empty($mensagemErro)): ?>
                <div class="erro"><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagemSucesso)): ?>
                <div class="sucesso"><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>

            <form method="post" action="register.php">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="cpf" placeholder="CPF" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Cadastrar-se</button>
            </form>
            <p class="cadastrar">Já tem uma conta? <a href="index.php">Faça login</a></p>
        </div>
    </div>
</body>
</html>