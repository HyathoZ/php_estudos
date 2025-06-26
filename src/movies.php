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
    $apikey = '79a177ce'; // Chave OMDb real
    $searchTerm = isset($_GET['imdb_search']) ? trim($_GET['imdb_search']) : '';
    if (strlen($searchTerm) < 3) {
        echo json_encode([
            'Search' => [],
            'Response' => 'False',
            'Error' => 'Termo muito curto. Digite pelo menos 3 caracteres.'
        ]);
        exit;
    }
    $byId = isset($_GET['by_id']) && $_GET['by_id'] == '1';
    $url = $byId
        ? "http://www.omdbapi.com/?apikey=$apikey&i=" . urlencode($searchTerm) . "&plot=full&r=json"
        : "http://www.omdbapi.com/?apikey=$apikey&s=" . urlencode($searchTerm) . "&type=movie&r=json";
    $result = @file_get_contents($url);
    if ($result === false) {
        echo json_encode([
            'Search' => [],
            'Response' => 'False',
            'Error' => 'Erro ao consultar a API externa.',
            'url' => $url
        ]);
        exit;
    }
    $data = json_decode($result, true);
    // Trata erro Too many results
    if (!$byId && isset($data['Error']) && strpos($data['Error'], 'Too many results') !== false) {
        echo json_encode([
            'Search' => [],
            'Response' => 'False',
            'Error' => 'Muitos resultados. Seja mais específico na busca.'
        ]);
        exit;
    }
    // Normaliza a resposta para sempre ter Search como array
    if (!$byId) {
        if (!isset($data['Search']) || !is_array($data['Search'])) {
            $data['Search'] = [];
        }
        $data['Response'] = (count($data['Search']) > 0) ? 'True' : 'False';
    }
    echo json_encode($data);
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
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($filme = $filmes->fetch_assoc()): ?>
                <tr>
                    <td><?php if (!empty($filme['poster'])): ?><img src="<?php echo htmlspecialchars($filme['poster']); ?>" alt="Poster" style="width:60px;max-height:90px;object-fit:cover;"/><?php endif; ?></td>
                    <td><?= htmlspecialchars($filme['title']) ?></td>
                    <td style="max-width:200px;white-space:pre-line;overflow:auto;"> <?= htmlspecialchars($filme['plot']) ?> </td>
                    <td><?= htmlspecialchars($filme['actors']) ?></td>
                    <td><?= isset($filme['avaliacao']) ? htmlspecialchars($filme['avaliacao']) : 'N/A' ?></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" class="toggle-switch" data-id="<?= htmlspecialchars($filme['imdbID']) ?>" <?= $filme['exibirNaHome'] ? 'checked' : '' ?> onchange="atualizarExibirNaHome(this)">
                            <span class="slider"></span>
                        </label>
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
<script src="../scripts/atualizar_home.js"></script>
<script>
function toggleExibirNaHome(imdbID, checked) {
    fetch('atualizar_home.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ imdbID: imdbID, exibirNaHome: checked ? 1 : 0 })
    })
    .then(r => r.json())
    .then data => {
        if (!data.success) {
            alert('Erro ao atualizar destaque: ' + (data.message || 'Erro desconhecido.'));
        } else {
            console.log('Atualizado!');
        }
    })
    .catch(() => alert('Erro de comunicação com o servidor.'));
}
// Função para abrir o modal de busca
function openImdbSearchModal() {
    var imdbModal = document.getElementById('imdbSearchModal');
    if (imdbModal) imdbModal.classList.add('show');
}
// Função para fechar o modal de busca
function closeImdbSearchModal() {
    var imdbModal = document.getElementById('imdbSearchModal');
    if (imdbModal) imdbModal.classList.remove('show');
}
// Garante que o botão de fechar está funcional
if (document.getElementById('closeImdbSearchModal')) {
    document.getElementById('closeImdbSearchModal').onclick = closeImdbSearchModal;
}
// Fecha o modal ao clicar fora do conteúdo
window.addEventListener('click', function(event) {
    var imdbModal = document.getElementById('imdbSearchModal');
    if (imdbModal && event.target === imdbModal) {
        imdbModal.classList.remove('show');
    }
});
// Função de busca de filmes
function searchImdbMovie() {
    const queryInput = document.getElementById('imdbQuery');
    const resultsDiv = document.getElementById('imdbResults');
    if (!queryInput || !resultsDiv) return;
    const query = queryInput.value.trim();
    if (query.length < 2) {
        resultsDiv.innerHTML = '<p style="color:#d00;text-align:center;">Digite pelo menos 2 caracteres.</p>';
        return;
    }
    resultsDiv.innerHTML = '<div style="color:#007bff;text-align:center;padding:20px;">Carregando...</div>';
    fetch('movies.php?imdb_search=' + encodeURIComponent(query))
        .then r => r.json())
        .then(data => {
            console.log('Resposta da API:', data);
            let html = '';
            if (data && data.Search && Array.isArray(data.Search) && data.Search.length) {
                html = data.Search.map(filme => renderImdbResultCard(filme)).join('');
            } else {
                html = '<p style="color:#888;text-align:center;">Nenhum filme encontrado.</p>';
            }
            resultsDiv.innerHTML = html;
        })
        .catch((err) => {
            console.log('Erro na busca:', err);
            resultsDiv.innerHTML = '<p style="color:#d00;text-align:center;">Erro ao buscar filmes. Tente novamente.</p>';
        });
}
// Renderiza cada card de resultado
function renderImdbResultCard(filme) {
    const img = filme.Poster && filme.Poster !== 'N/A' ? filme.Poster : '';
    return `<div style='display:flex;align-items:center;gap:18px;padding:12px 0;border-bottom:1px solid #e3e7ed;'>` +
        (img ? `<img src='${img}' style='width:60px;height:90px;object-fit:cover;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.08);'>` : `<div style='width:60px;height:90px;background:#e3e7ed;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#b0b8c1;font-size:13px;'>Sem imagem</div>`) +
        `<div style='flex:1;'><div style='font-size:17px;font-weight:600;color:#222;'>${filme.Title}</div>` +
        `<div style='color:#007bff;font-size:14px;'>${filme.Year}</div>` +
        `<div style='color:#444;font-size:13px;margin:4px 0 8px 0;'>${filme.Type === 'movie' ? 'Filme' : filme.Type}</div>` +
        `<button onclick='fetchImdbDetailsAndOpenModal(${JSON.stringify(filme.imdbID)})' style='background:#222e3a;color:#fff;padding:7px 16px;border:none;border-radius:5px;font-size:15px;font-weight:500;cursor:pointer;transition:background 0.2s;'>Selecionar</button></div></div>`;
}
// Busca detalhes e abre modal de cadastro
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
            closeImdbSearchModal();
            document.getElementById('movieModal').classList.add('show');
        });
}
// Garante que o botão de buscar do modal está funcional
if (document.getElementById('imdbSearchBtn')) {
    document.getElementById('imdbSearchBtn').onclick = function(e) {
        e.preventDefault();
        searchImdbMovie();
    };
}
// Garante que o botão principal "Pesquisar" abre o modal
if (document.getElementById('pesquisar')) {
    document.getElementById('pesquisar').onclick = function(e) {
        e.preventDefault();
        openImdbSearchModal();
    };
}
</script>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}
.switch input {display:none;}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #28a745;
}
input:checked + .slider:before {
  transform: translateX(20px);
}
</style>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
</body>
</html>
