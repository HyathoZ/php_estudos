<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

// Criação da tabela de filmes se não existir (apenas para exemplo, remova em produção)
$conn->query("CREATE TABLE IF NOT EXISTS filmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    diretor VARCHAR(255),
    ano INT,
    genero VARCHAR(100)
)");

// CRUD Filmes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_movie'])) {
    $titulo = $conn->real_escape_string($_POST['movie_titulo']);
    $diretor = $conn->real_escape_string($_POST['movie_diretor']);
    $ano = intval($_POST['movie_ano']);
    $genero = $conn->real_escape_string($_POST['movie_genero']);
    $sql = "INSERT INTO filmes (titulo, diretor, ano, genero) VALUES ('$titulo', '$diretor', $ano, '$genero')";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    $id = intval($_POST['movie_id']);
    $titulo = $conn->real_escape_string($_POST['movie_titulo']);
    $diretor = $conn->real_escape_string($_POST['movie_diretor']);
    $ano = intval($_POST['movie_ano']);
    $genero = $conn->real_escape_string($_POST['movie_genero']);
    $sql = "UPDATE filmes SET titulo='$titulo', diretor='$diretor', ano=$ano, genero='$genero' WHERE id=$id";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_movie'])) {
    $id = intval($_POST['delete_id']);
    $sql = "DELETE FROM filmes WHERE id=$id";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_movie'])) {
    $searchTerm = $conn->real_escape_string($_POST['search_movie']);
    $sql = "SELECT * FROM filmes WHERE titulo LIKE '%$searchTerm%'";
    $filmes = $conn->query($sql);
} else {
    $filmes = $conn->query("SELECT * FROM filmes");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Filmes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/modal.css">
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
    <button class="btn-novo-usuario" onclick="openCreateMovieModal()" style="
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
        margin-right: 10px;">
        Novo Filme
    </button>
    <a href="principal.php" class="btn-voltar" style="
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;">
        Voltar
    </a>
</div>
<main class="container">
    <section class="usuarios">
        <h3>Lista de Filmes</h3>
        <div class="search-bar">
            <form method="post" action="movies.php">
                <input type="text" name="search_movie" placeholder="Pesquisar por título" id="search-movie-input">
                <button type="submit" class="btn-search">Pesquisar</button>
                <a href="movies.php" class="btn-clear">Limpar</a>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Diretor</th>
                    <th>Ano</th>
                    <th>Gênero</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($filme = $filmes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($filme['titulo']) ?></td>
                    <td><?= htmlspecialchars($filme['diretor']) ?></td>
                    <td><?= htmlspecialchars($filme['ano']) ?></td>
                    <td><?= htmlspecialchars($filme['genero']) ?></td>
                    <td>
                        <button class="btn-edit" onclick='openEditMovieModal(<?= json_encode($filme) ?>)'>Editar</button>
                        <button class="btn-delete" onclick="openConfirmDeleteMovieModal(<?= $filme['id'] ?>)">Excluir</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
<!-- Modal de criação/edição de filme -->
<div id="movieModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeMovieModal">&times;</span>
        <h3 id="movieModalTitle">Novo Filme</h3>
        <form id="movieForm" method="post" action="movies.php">
            <input type="hidden" name="movie_id" id="movie_id">
            <label for="movie_titulo">Título:</label>
            <input type="text" name="movie_titulo" id="movie_titulo" required>
            <label for="movie_diretor">Diretor:</label>
            <input type="text" name="movie_diretor" id="movie_diretor">
            <label for="movie_ano">Ano:</label>
            <input type="number" name="movie_ano" id="movie_ano">
            <label for="movie_genero">Gênero:</label>
            <input type="text" name="movie_genero" id="movie_genero">
            <button type="submit" id="movieModalSubmitButton" name="create_movie">Cadastrar Filme</button>
        </form>
    </div>
</div>
<!-- Modal de confirmação de exclusão de filme -->
<div id="confirmDeleteMovieModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeConfirmDeleteMovieModal">&times;</span>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir este filme?</p>
        <form method="post" action="movies.php">
            <input type="hidden" name="delete_id" id="delete_movie_id_modal">
            <button type="submit" name="delete_movie" class="btn-delete">Excluir</button>
            <button type="button" id="cancelDeleteMovieBtn">Cancelar</button>
        </form>
    </div>
</div>
<script src="../scripts/dropdown.js"></script>
<script src="../scripts/openEditModal.js"></script>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
</body>
</html>
