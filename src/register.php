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
            // Validação de CPF
            function validar_cpf($cpf) {
                $cpf = preg_replace('/[^0-9]/', '', $cpf);
                if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$c] != $d) return false;
                }
                return true;
            }
            if (!validar_cpf($cpf)) {
                $mensagemErro = '⚠️ CPF inválido.';
            }
            // Validação de senha forte
            if (empty($mensagemErro)) {
                $senhaValida = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/', $senha);
                if (!$senhaValida) {
                    $mensagemErro = '⚠️ A senha deve ter no mínimo 6 caracteres, incluindo uma maiúscula, uma minúscula, um número e um caractere especial.';
                }
            }
            // Insere os dados no banco de dados
            if (empty($mensagemErro)) {
                $sql = "INSERT INTO usuarios (nome, cpf, email, senha) VALUES ('$nome', '$cpf', '$email', '$senha')";
                if ($conn->query($sql) === TRUE) {
                    $mensagemSucesso = "✅ Usuário cadastrado com sucesso!";
                } else {
                    $mensagemErro = "Erro ao cadastrar: " . $conn->error;
                }
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
                <input type="text" name="cpf" placeholder="CPF" required maxlength="14" id="cpf">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required minlength="6" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^a-zA-Z\\d]).{6,}$" title="Mínimo 6 caracteres, 1 maiúscula, 1 minúscula, 1 número e 1 especial" id="senha">
                <span id="senhaHelp" style="color:#d00;font-size:13px;display:none;"></span>
                <button type="submit">Cadastrar-se</button>
            </form>
            <p class="cadastrar">Já tem uma conta? <a href="index.php">Faça login</a></p>
        </div>
    </div>
</body>
<script>
// Máscara de CPF
const cpfInput = document.getElementById('cpf');
if (cpfInput) {
    cpfInput.addEventListener('input', function(e) {
        let v = cpfInput.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0,11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
        v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        cpfInput.value = v;
    });
}
// Validação de senha forte
const senhaInput = document.getElementById('senha');
const senhaHelp = document.getElementById('senhaHelp');
if (senhaInput && senhaHelp) {
    senhaInput.addEventListener('input', function() {
        const val = senhaInput.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/;
        if (!regex.test(val)) {
            senhaHelp.style.display = 'block';
            senhaHelp.textContent = 'A senha deve ter no mínimo 6 caracteres, incluindo uma maiúscula, uma minúscula, um número e um caractere especial.';
        } else {
            senhaHelp.style.display = 'none';
        }
    });
}
</script>
</html>