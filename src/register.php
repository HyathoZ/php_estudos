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
            // Validação de CPF (backend)
            function validar_cpf($cpf) {
                $cpf = preg_replace('/[^0-9]/', '', $cpf);
                if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
                for ($t = 9; $t < 11; $t++) {
                    $d = 0;
                    for ($c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$t] != $d) return false;
                }
                return true;
            }
            // Validação de senha forte (backend)
            function validar_senha($senha) {
                // Corrigido: Aceita qualquer caractere especial, mínimo 6, pelo menos 1 maiúscula, 1 minúscula, 1 número e 1 especial
                return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/u', $senha);
            }
            // Validação de CPF e senha forte ao cadastrar
            if (!validar_cpf($cpf)) {
                $mensagemErro = '⚠️ CPF inválido.';
            } elseif (empty($mensagemErro) && !validar_senha($senha)) {
                $mensagemErro = '⚠️ A senha deve ter no mínimo 6 caracteres, incluindo uma maiúscula, uma minúscula, um número e um caractere especial.';
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
                <div class="senha-container">
                    <input type="password" name="senha" placeholder="Senha" required minlength="6" title="Mínimo 6 caracteres, 1 maiúscula, 1 minúscula, 1 número e 1 especial" id="senha">
                    <button type="button" id="toggleSenha" class="toggle-senha">👁️</button>
                </div>
                <span id="senhaHelp" style="color:#d00;font-size:13px;display:none;"></span>
                <input type="password" name="senha" id="senha" placeholder="Senha" required minlength="6" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-=\[\]{};':&quot;\\|,.<>\/?]).{6,}$" title="A senha deve ter no mínimo 6 caracteres, incluindo 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caractere especial.">

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
const form = document.querySelector('form');
if (senhaInput && senhaHelp && form) {
    function validarSenha() {
        const val = senhaInput.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/;
        if (!regex.test(val)) {
            senhaHelp.style.display = 'block';
            senhaHelp.textContent = 'A senha deve ter no mínimo 6 caracteres, incluindo uma maiúscula, uma minúscula, um número e um caractere especial.';
            return false;
        } else {
            senhaHelp.style.display = 'none';
            return true;
        }
    }
    senhaInput.addEventListener('input', validarSenha);
    form.addEventListener('submit', function(e) {
        if (!validarSenha()) {
            senhaInput.focus();
            e.preventDefault();
        }
    });
}
// Visualizar senha
const toggleSenha = document.getElementById('toggleSenha');
if (toggleSenha && senhaInput) {
    toggleSenha.addEventListener('click', function() {
        if (senhaInput.type === 'password') {
            senhaInput.type = 'text';
            toggleSenha.textContent = '🙈';
        } else {
            senhaInput.type = 'password';
            toggleSenha.textContent = '👁️';
        }
    });
}
</script>
</html>