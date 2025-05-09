<?php
include("conexao.php");
session_start();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $cpf = $_POST["cpf"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
        $mensagemErro = "Todos os campos são obrigatórios.";
    } else {
        // Verifica se o CPF ou email já está cadastrado
        $sqlVerifica = "SELECT cpf FROM usuarios WHERE cpf = '$cpf' OR email = '$email'";
        $resultado = $conn->query($sqlVerifica);

        if ($resultado->num_rows > 0) {
            $mensagemErro = "⚠️ Este CPF ou email já está cadastrado.";
        } else {
            // Insere os dados no banco de dados
            $sql = "INSERT INTO usuarios (nome, cpf, email, senha) VALUES ('$nome', '$cpf', '$email', '$senha')";
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
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Cadastrar-se</button>
            </form>
            <p class="cadastrar">Já tem uma conta? <a href="index.php">Faça login</a></p>
        </div>
    </div>
</body>
</html>