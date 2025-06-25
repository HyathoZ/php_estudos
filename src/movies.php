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
    $elenco = isset($_POST['movie_elenco']) ? $conn->real_escape_string($_POST['movie_elenco']) : '';
    $avaliacao = isset($_POST['movie_avaliacao']) ? $conn->real_escape_string($_POST['movie_avaliacao']) : '';
    $ativoNaHome = isset($_POST['movie_ativoNaHome']) ? 1 : 0;
    $sql = "INSERT INTO filmes (id, titulo, descricao, imagem, elenco, avaliacao, ativoNaHome) VALUES ('$id', '$titulo', '$descricao', '$imagem', '$elenco', '$avaliacao', $ativoNaHome) ON DUPLICATE KEY UPDATE titulo='$titulo', descricao='$descricao', imagem='$imagem', elenco='$elenco', avaliacao='$avaliacao', ativoNaHome=$ativoNaHome";
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
// Endpoint para busca na OMDb API (IMDb)
if (isset($_GET['imdb_search'])) {
    $apikey = 'demo'; // Troque por sua chave OMDb real
    $query = urlencode($_GET['imdb_search']);
    $byId = isset($_GET['by_id']) && $_GET['by_id'] == '1';
    $url = $byId
        ? "http://www.omdbapi.com/?apikey=$apikey&i=$query&plot=full&r=json"
        : "http://www.omdbapi.com/?apikey=$apikey&s=$query&type=movie&r=json";
    $result = file_get_contents($url);
    header('Content-Type: application/json');
    echo $result;
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
    <button class="btn-novo-usuario" id="abrirModalPesquisa" style="
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
        margin-right: 10px;">
        Pesquisar Filme
    </button>
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
        <!-- Campo de busca removido daqui -->
        <table>
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Elenco</th>
                    <th>Avaliação</th>
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
                    <td><?= htmlspecialchars($filme['elenco']) ?></td>
                    <td><?= htmlspecialchars($filme['avaliacao']) ?></td>
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
            <label for="movie_elenco">Elenco:</label>
            <input type="text" name="movie_elenco" id="movie_elenco">
            <label for="movie_avaliacao">Avaliação:</label>
            <input type="text" name="movie_avaliacao" id="movie_avaliacao" placeholder="Ex: 8.5">
            <label for="movie_ativoNaHome" style="display:block;margin:10px 0 5px;">Exibir na Home:</label>
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
    <div class="modal-content" style="max-width:600px;min-width:350px;">
        <span class="close" id="closeImdbSearchModal">&times;</span>
        <h3 style="color:#007bff;font-weight:600;">Buscar Filme na IMDb</h3>
        <form id="imdbSearchForm" onsubmit="return false;" style="display:flex;gap:10px;align-items:center;justify-content:center;margin-bottom:16px;">
            <input type="text" id="imdbQuery" placeholder="Digite o título do filme" style="flex:1;padding:10px 14px;border-radius:6px;border:1px solid #b0b8c1;font-size:16px;background:#f7fafd;">
            <button type="button" onclick="searchImdbMovie()" style="background:#007bff;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:600;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.07);display:flex;align-items:center;gap:6px;">🔍 Buscar</button>
        </form>
        <div id="imdbResults" style="max-height:350px;overflow-y:auto;"></div>
    </div>
</div>
<!-- Modal de pesquisa de filmes -->
<div id="modalPesquisa" class="modal">
    <div class="modal-content" style="max-width:600px;min-width:350px;position:relative;">
        <span class="close" id="fecharModalPesquisa" tabindex="0" aria-label="Fechar modal">&times;</span>
        <h3 style="color:#007bff;font-weight:600;">Pesquisar Filme</h3>
        <form id="formPesquisaFilme" onsubmit="return false;" style="display:flex;gap:10px;align-items:center;justify-content:center;margin-bottom:16px;">
            <input type="text" id="inputPesquisaFilme" placeholder="Digite o título do filme" style="flex:1;padding:10px 14px;border-radius:6px;border:1px solid #b0b8c1;font-size:16px;background:#f7fafd;">
            <button type="button" id="btnBuscarFilme" style="background:#007bff;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:600;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.07);display:flex;align-items:center;gap:6px;">🔍 Buscar título</button>
        </form>
        <div id="resultadosPesquisaFilme" style="max-height:350px;overflow-y:auto;"></div>
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

function searchImdbMovie() {
    const query = document.getElementById('imdbQuery').value.trim();
    const resultsDiv = document.getElementById('imdbResults');
    const imdbModal = document.getElementById('imdbSearchModal');
    if (!query || query.length < 3) {
        resultsDiv.innerHTML = '<p style="color:#d00;text-align:center;">Digite pelo menos 3 caracteres para buscar.</p>';
        imdbModal.classList.add('show');
        return;
    }
    resultsDiv.innerHTML = '<div style="color:#007bff;text-align:center;padding:20px;">Carregando...</div>';
    imdbModal.classList.add('show');
    fetch('movies.php?imdb_search=' + encodeURIComponent(query))
        .then(async r => {
            if (!r.ok) throw new Error('Erro HTTP: ' + r.status);
            const data = await r.json();
            console.log('Resposta da API:', data); // Debug
            return data;
        })
        .then(data => {
            let html = '';
            if (data && data.Search && data.Search.length) {
                html = data.Search.map(filme => renderImdbResultCard(filme)).join('');
                imdbModal.classList.add('show');
            } else {
                html = '<p style="color:#888;text-align:center;">Nenhum filme encontrado com esse título.</p>';
            }
            resultsDiv.innerHTML = html;
        })
        .catch(err => {
            console.log('Erro ao buscar filmes:', err);
            resultsDiv.innerHTML = '<p style="color:#d00;text-align:center;">Erro ao buscar filmes. Tente novamente.</p>';
            imdbModal.classList.add('show');
        });
}
function renderImdbResultCard(filme) {
    const img = filme.Poster && filme.Poster !== 'N/A' ? filme.Poster : '';
    return `<div style='display:flex;align-items:center;gap:18px;padding:12px 0;border-bottom:1px solid #e3e7ed;'>` +
        (img ? `<img src='${img}' style='width:60px;height:90px;object-fit:cover;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.08);'>` : `<div style='width:60px;height:90px;background:#e3e7ed;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#b0b8c1;font-size:13px;'>Sem imagem</div>`) +
        `<div style='flex:1;'><div style='font-size:17px;font-weight:600;color:#222;'>${filme.Title}</div>` +
        `<div style='color:#007bff;font-size:14px;'>${filme.Year}</div>` +
        `<div style='color:#444;font-size:13px;margin:4px 0 8px 0;'>${filme.Type === 'movie' ? 'Filme' : filme.Type}</div>` +
        `<button onclick='fetchImdbDetailsAndOpenModal(${JSON.stringify(filme.imdbID)})' style='background:#222e3a;color:#fff;padding:7px 16px;border:none;border-radius:5px;font-size:15px;font-weight:500;cursor:pointer;transition:background 0.2s;'>Selecionar</button></div></div>`;
}
function fetchImdbDetailsAndOpenModal(imdbID) {
    fetch('movies.php?imdb_search=' + encodeURIComponent(imdbID) + '&by_id=1')
        .then(r => r.json())
        .then(data => {
            document.getElementById('movie_id').value = data.imdbID || '';
            document.getElementById('movie_titulo').value = data.Title || '';
            document.getElementById('movie_descricao').value = data.Plot || '';
            document.getElementById('movie_imagem').value = (data.Poster && data.Poster !== 'N/A') ? data.Poster : '';
            if(document.getElementById('movie_elenco')) document.getElementById('movie_elenco').value = data.Actors || '';
            if(document.getElementById('movie_avaliacao')) document.getElementById('movie_avaliacao').value = data.imdbRating || '';
            document.getElementById('movie_ativoNaHome').checked = false;
            document.getElementById('modalPesquisa').classList.remove('show');
            document.getElementById('movieModal').classList.add('show');
        });
}

document.addEventListener('DOMContentLoaded', function () {
    // Corrige erro de função não definida e garante robustez
    window.openCreateMovieModal = function() {
        // Limpa os campos do modal de filme
        var id = document.getElementById('movie_id');
        var titulo = document.getElementById('movie_titulo');
        var descricao = document.getElementById('movie_descricao');
        var imagem = document.getElementById('movie_imagem');
        var elenco = document.getElementById('movie_elenco');
        var avaliacao = document.getElementById('movie_avaliacao');
        var ativo = document.getElementById('movie_ativoNaHome');
        if (id) id.value = '';
        if (titulo) titulo.value = '';
        if (descricao) descricao.value = '';
        if (imagem) imagem.value = '';
        if (elenco) elenco.value = '';
        if (avaliacao) avaliacao.value = '';
        if (ativo) ativo.checked = false;
        var modal = document.getElementById('movieModal');
        if (modal) modal.classList.add('show');
    };

    // Corrige erro de null ao adicionar onclick
    var closeMovieModalBtn = document.getElementById('closeMovieModal');
    var movieModal = document.getElementById('movieModal');
    if (closeMovieModalBtn && movieModal) {
        closeMovieModalBtn.addEventListener('click', function () {
            movieModal.classList.remove('show');
        });
    }

    // Busca de filmes ao clicar no botão 'Buscar' do modal IMDb
    var imdbSearchBtn = document.querySelector('#imdbSearchForm button[type="button"]');
    var imdbInput = document.getElementById('imdbQuery');
    if (imdbSearchBtn && imdbInput) {
        imdbSearchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            searchImdbMovie();
        });
    }

    // Garante que o modal de busca IMDb fecha corretamente
    var closeImdbBtn = document.getElementById('closeImdbSearchModal');
    var imdbModal = document.getElementById('imdbSearchModal');
    if (closeImdbBtn && imdbModal) {
        closeImdbBtn.addEventListener('click', function () {
            imdbModal.classList.remove('show');
        });
    }

    // Abrir modal de pesquisa
    var btnAbrir = document.getElementById('abrirModalPesquisa');
    var modalPesquisa = document.getElementById('modalPesquisa');
    var fecharModal = document.getElementById('fecharModalPesquisa');
    var inputPesquisa = document.getElementById('inputPesquisaFilme');
    var btnBuscar = document.getElementById('btnBuscarFilme');
    var resultadosDiv = document.getElementById('resultadosPesquisaFilme');
    if (btnAbrir && modalPesquisa) {
        btnAbrir.addEventListener('click', function () {
            modalPesquisa.classList.add('show');
            resultadosDiv.innerHTML = '';
            if(inputPesquisa) inputPesquisa.value = '';
            if(inputPesquisa) inputPesquisa.focus();
        });
    }
    if (fecharModal && modalPesquisa) {
        fecharModal.addEventListener('click', function () {
            modalPesquisa.classList.remove('show');
        });
        fecharModal.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                modalPesquisa.classList.remove('show');
            }
        });
    }
    // Fecha ao clicar fora do conteúdo
    window.addEventListener('click', function (event) {
        if (event.target === modalPesquisa) {
            modalPesquisa.classList.remove('show');
        }
    });
    // Busca de filmes
    if (btnBuscar && inputPesquisa && resultadosDiv) {
        btnBuscar.addEventListener('click', function () {
            const termo = inputPesquisa.value.trim();
            if (termo.length < 3) {
                resultadosDiv.innerHTML = '<p style="color:#d00;text-align:center;">Digite pelo menos 3 caracteres.</p>';
                return;
            }
            resultadosDiv.innerHTML = '<div style="color:#007bff;text-align:center;padding:20px;">Carregando...</div>';
            fetch('movies.php?imdb_search=' + encodeURIComponent(termo))
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    if (data && data.Search && data.Search.length) {
                        html = data.Search.map(filme => renderImdbResultCard(filme)).join('');
                    } else {
                        html = '<p style="color:#888;text-align:center;">Nenhum filme encontrado com esse título.</p>';
                    }
                    resultadosDiv.innerHTML = html;
                })
                .catch((err) => {
                    resultadosDiv.innerHTML = '<p style="color:#d00;text-align:center;">Erro ao buscar filmes. Tente novamente.</p>';
                    console.log('Erro na busca:', err);
                });
        });
    }
});
</script>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
</body>
</html>
