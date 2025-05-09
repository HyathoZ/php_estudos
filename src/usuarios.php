<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT id, nome, cpf, senha, email FROM usuarios WHERE nome LIKE '%$searchTerm%'";
    $resultado = $conn->query($sql);
} else {
    $sql = "SELECT id, nome, cpf, senha, email FROM usuarios";
    $resultado = $conn->query($sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['edit_id'];
    $nome = $conn->real_escape_string($_POST['edit_nome']);
    $cpf = $conn->real_escape_string($_POST['edit_cpf']);
    $email = $conn->real_escape_string($_POST['edit_email']);
    $senha = $conn->real_escape_string($_POST['edit_senha']);

    // Verifica se o CPF ou email já estão cadastrados
    $sqlVerifica = "SELECT id FROM usuarios WHERE (cpf = '$cpf' OR email = '$email') AND id != '$id'";
    $resultadoVerifica = $conn->query($sqlVerifica);

    if ($resultadoVerifica->num_rows > 0) {
        $mensagemErro = "⚠️ CPF ou email já estão cadastrados.";
    } else {
        $sql = "UPDATE usuarios SET nome='$nome', cpf='$cpf', email='$email', senha='$senha' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $mensagemSucesso = "✅ Usuário atualizado com sucesso!";
        } else {
            $mensagemErro = "Erro ao atualizar usuário: " . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $nome = $conn->real_escape_string($_POST['edit_nome']);
    $cpf = $conn->real_escape_string($_POST['edit_cpf']);
    $email = $conn->real_escape_string($_POST['edit_email']);
    $senha = $conn->real_escape_string($_POST['edit_senha']);

    // Verifica se o CPF ou email já estão cadastrados
    $sqlVerifica = "SELECT id FROM usuarios WHERE cpf = '$cpf' OR email = '$email'";
    $resultadoVerifica = $conn->query($sqlVerifica);

    if ($resultadoVerifica->num_rows > 0) {
        $mensagemErro = "⚠️ CPF ou email já estão cadastrados.";
    } else {
        $sql = "INSERT INTO usuarios (nome, cpf, email, senha) VALUES ('$nome', '$cpf', '$email', '$senha')";
        if ($conn->query($sql) === TRUE) {
            $mensagemSucesso = "✅ Usuário criado com sucesso!";
        } else {
            $mensagemErro = "Erro ao criar usuário: " . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id = intval($_POST['delete_id']);
    $sql = "DELETE FROM usuarios WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $mensagemSucesso = "✅ Usuário excluído com sucesso!";
    } else {
        $mensagemErro = "Erro ao excluir usuário: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/modal.css">
    <title>Usuários</title>
</head>
<body>
<header>
    <div class="container-header">
        <h2>Bem-vindo, <?php echo $_SESSION["nome"]; ?>!</h2>
        <div class="sair">
            <div class="config">
                <img src="../icones/config.svg" alt="Configurações" class="config-icon" id="config-icon">
                <div class="dropdown" id="dropdown-menu">
                    <ul>
                        <li><a href="./usuarios.php">Usuários</a></li>
                        <li><a href="./movies.php">Filmes</a></li>
                        <li><a href="#">Ajuda</a></li>
                    </ul>
                </div>
            </div>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </div>
</header>

<div style="margin: 20px; text-align: right;">
    <button class="btn-novo-usuario" onclick="openCreateModal()" style="
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
        margin-right: 10px;
    ">Novo Usuário</button>
    <a href="principal.php" class="btn-voltar" style="
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    ">Voltar</a>
</div>

<main class="container">
    <section class="usuarios">
        <h3>Lista de Usuários</h3>
        <div class="search-bar">
            <form method="post" action="usuarios.php">
                <input type="text" name="search" placeholder="Pesquisar por nome" id="search-input">
                <button type="submit" class="btn-search">Pesquisar</button>
                <a href="usuarios.php" class="btn-clear">Limpar</a>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Email</th>
                    <th>Senha</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($usuario = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['nome']; ?></td>
                            <td><?php echo $usuario['cpf']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $usuario['senha']; ?></td>
                            <td>
                                <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($usuario)); ?>)">Editar</button>
                                <button class="btn-delete" onclick="openConfirmDeleteModal(<?php echo $usuario['id']; ?>)">Excluir</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php if (!empty($mensagemSucesso) && isset($_POST['create_user'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editModal');
        modal.classList.add('show');
        document.getElementById('editForm').style.display = 'none';
        document.getElementById('modalTitle').textContent = '';
        document.getElementById('modalMessage').textContent = '<?php echo $mensagemSucesso; ?>';
        document.getElementById('modalMessage').style.display = 'block';
        // Ao fechar o modal, recarrega a página
        const closeModal = document.getElementById('closeModal');
        closeModal.onclick = function() {
            modal.classList.remove('show');
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('modalMessage').style.display = 'none';
            location.reload();
        };
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
                document.getElementById('editForm').style.display = 'block';
                document.getElementById('modalMessage').style.display = 'none';
                location.reload();
            }
        };
    });
</script>
<?php endif; ?>

<!-- Modal para mensagens de sucesso/erro -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeMessageModal">&times;</span>
        <div id="messageModalText" style="font-size:24px;font-weight:bold;text-align:center;"></div>
    </div>
</div>

<?php if (!empty($mensagemSucesso) && isset($_POST['delete_user'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messageModal = document.getElementById('messageModal');
        const messageModalText = document.getElementById('messageModalText');
        messageModalText.textContent = '<?php echo $mensagemSucesso; ?>';
        messageModal.classList.add('show');
        setTimeout(() => {
            messageModal.classList.remove('show');
            window.location.href = 'usuarios.php';
        }, 2000);
    });
</script>
<?php endif; ?>

<!-- Modal para criação/edição de usuário -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h3 id="modalTitle">Editar Usuário</h3>
        <div id="modalMessage" style="display:none;font-size:24px;font-weight:bold;text-align:center;"></div>
        <form id="editForm" method="post" action="usuarios.php">
            <input type="hidden" name="edit_id" id="edit_id">
            <label for="edit_nome">Nome:</label>
            <input type="text" name="edit_nome" id="edit_nome" required>
            <label for="edit_cpf">CPF:</label>
            <input type="text" name="edit_cpf" id="edit_cpf" required>
            <label for="edit_email">Email:</label>
            <input type="email" name="edit_email" id="edit_email" required>
            <label for="edit_senha">Senha:</label>
            <input type="password" name="edit_senha" id="edit_senha" required>
            <button type="submit" id="modalSubmitButton" name="update_user">Salvar Alterações</button>
        </form>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div id="confirmDeleteModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeConfirmDeleteModal">&times;</span>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir este usuário?</p>
        <form id="deleteUserForm" method="post" action="usuarios.php">
            <input type="hidden" name="delete_id" id="delete_id_modal">
            <button type="submit" name="delete_user" class="btn-delete">Excluir</button>
            <button type="button" id="cancelDeleteBtn">Cancelar</button>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
<script src="../scripts/openEditModal.js"></script>
</body>
</html>