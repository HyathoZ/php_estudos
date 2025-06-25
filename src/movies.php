<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

// Criação da tabela de filmes com campos para integração com API e ativação na home
$conn->query("CREATE TABLE IF NOT EXISTS filmes (
    id VARCHAR(64) PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(512),
    ativoNaHome BOOLEAN DEFAULT FALSE
)");

// CRUD Filmes atualizado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_movie'])) {
    $id = $conn->real_escape_string($_POST['movie_id']);
    $titulo = $conn->real_escape_string($_POST['movie_titulo']);
    $descricao = $conn->real_escape_string($_POST['movie_descricao']);
    $imagem = $conn->real_escape_string($_POST['movie_imagem']);
    $ativoNaHome = isset($_POST['movie_ativoNaHome']) ? 1 : 0;
    $sql = "INSERT INTO filmes (id, titulo, descricao, imagem, ativoNaHome) VALUES ('$id', '$titulo', '$descricao', '$imagem', $ativoNaHome) ON DUPLICATE KEY UPDATE titulo='$titulo', descricao='$descricao', imagem='$imagem', ativoNaHome=$ativoNaHome";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    $id = $conn->real_escape_string($_POST['movie_id']);
    $titulo = $conn->real_escape_string($_POST['movie_titulo']);
    $descricao = $conn->real_escape_string($_POST['movie_descricao']);
    $imagem = $conn->real_escape_string($_POST['movie_imagem']);
    $ativoNaHome = isset($_POST['movie_ativoNaHome']) ? 1 : 0;
    $sql = "UPDATE filmes SET titulo='$titulo', descricao='$descricao', imagem='$imagem', ativoNaHome=$ativoNaHome WHERE id='$id'";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_ativo'])) {
    $id = $conn->real_escape_string($_POST['toggle_id']);
    $ativo = intval($_POST['toggle_ativo']);
    $sql = "UPDATE filmes SET ativoNaHome=$ativo WHERE id='$id'";
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

// Endpoint para busca TMDB via AJAX
if (isset($_GET['search'])) {
    require_once 'tmdb_api.php';
    $tmdb = new TMDB();
    $result = $tmdb->searchMovie($_GET['search']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
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
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Ativo na Home</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($filme = $filmes->fetch_assoc()): ?>
                <tr>
                    <td><?php if ($filme['imagem']): ?><img src="<?php echo htmlspecialchars($filme['imagem']); ?>" alt="Poster" style="width:60px;max-height:90px;object-fit:cover;"/><?php endif; ?></td>
                    <td><?= htmlspecialchars($filme['titulo']) ?></td>
                    <td style="max-width:200px;white-space:pre-line;overflow:auto;"> <?= htmlspecialchars($filme['descricao']) ?> </td>
                    <td>
                        <form method="post" action="movies.php" style="display:inline;">
                            <input type="hidden" name="toggle_id" value="<?= htmlspecialchars($filme['id']) ?>">
                            <input type="hidden" name="toggle_ativo" value="<?= $filme['ativoNaHome'] ? 0 : 1 ?>">
                            <button type="submit" name="toggle_ativo" style="background:none;border:none;cursor:pointer;">
                                <span style="display:inline-block;width:40px;height:24px;background:<?= $filme['ativoNaHome'] ? '#28a745' : '#ccc' ?>;border-radius:12px;position:relative;vertical-align:middle;transition:background 0.2s;">
                                    <span style="position:absolute;left:<?= $filme['ativoNaHome'] ? '20px' : '2px' ?>;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,0.2);"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td>
                        <button class="btn-edit" onclick='openEditMovieModal(<?= json_encode($filme) ?>)'>Editar</button>
                        <button class="btn-delete" onclick="openConfirmDeleteMovieModal('<?= $filme['id'] ?>')">Excluir</button>
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
            <label for="movie_descricao">Descrição:</label>
            <textarea name="movie_descricao" id="movie_descricao" required></textarea>
            <label for="movie_imagem">URL da Imagem:</label>
            <input type="text" name="movie_imagem" id="movie_imagem">
            <label for="movie_ativoNaHome" style="display:block;margin:10px 0 5px;">Ativo na Home:</label>
            <input type="checkbox" name="movie_ativoNaHome" id="movie_ativoNaHome" value="1">
            <button type="submit" id="movieModalSubmitButton" name="create_movie">Cadastrar Filme</button>
        </form>
        <div style="margin-top:10px;">
            <button onclick="openImdbSearchModal()" style="background:#007bff;color:#fff;padding:8px 16px;border:none;border-radius:5px;">Buscar na IMDb</button>
        </div>
    </div>
</div>
<!-- Modal de busca IMDb -->
<div id="imdbSearchModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeImdbSearchModal">&times;</span>
        <h3>Buscar Filme na IMDb</h3>
        <form id="imdbSearchForm" onsubmit="return false;">
            <input type="text" id="imdbQuery" placeholder="Título ou ID IMDb">
            <button type="button" onclick="searchImdbMovie()">Buscar</button>
        </form>
        <div id="imdbResults"></div>
    </div>
</div>
<script src="../scripts/dropdown.js"></script>
<script src="../scripts/openEditModal.js"></script>
<script>
function openImdbSearchModal() {
    document.getElementById('imdbSearchModal').classList.add('show');
}
document.getElementById('closeImdbSearchModal').onclick = function() {
    document.getElementById('imdbSearchModal').classList.remove('show');
};
// Busca filme na TMDB (pode ser adaptado para IMDb se necessário)
function searchImdbMovie() {
    const query = document.getElementById('imdbQuery').value;
    if (!query) return;
    fetch('tmdb_api.php?search=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(data => {
            let html = '';
            if (data && data.results && data.results.length) {
                data.results.forEach(filme => {
                    html += `<div style='border:1px solid #ccc;padding:8px;margin:8px 0;display:flex;align-items:center;'>` +
                        `<img src='https://image.tmdb.org/t/p/w92${filme.poster_path}' style='margin-right:10px;'>` +
                        `<div><b>${filme.title}</b><br>${filme.overview.substring(0,120)}...<br>` +
                        `<button onclick='selectImdbMovie(${JSON.stringify(filme)})'>Selecionar</button></div></div>`;
                });
            } else {
                html = '<p>Nenhum filme encontrado.</p>';
            }
            document.getElementById('imdbResults').innerHTML = html;
        });
}
function selectImdbMovie(filme) {
    document.getElementById('movie_id').value = filme.id;
    document.getElementById('movie_titulo').value = filme.title;
    document.getElementById('movie_descricao').value = filme.overview;
    document.getElementById('movie_imagem').value = filme.poster_path ? 'https://image.tmdb.org/t/p/w500' + filme.poster_path : '';
    document.getElementById('imdbSearchModal').classList.remove('show');
}
</script>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
</body>
</html>
