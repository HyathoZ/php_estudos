<?php
$mensagemErro = '';
if (isset($_GET['erro'])) {
    switch ($_GET['erro']) {
        case 'cpf':
            $mensagemErro = '⚠️ Informe o CPF.';
            break;
        case 'senha':
            $mensagemErro = '⚠️ Informe a senha.';
            break;
        case 'login':
            $mensagemErro = '❌ CPF ou senha incorretos.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Login</h2>

            <?php if (!empty($mensagemErro)): ?>
                <div class="erro"><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <input type="text" name="cpf" placeholder="CPF" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
