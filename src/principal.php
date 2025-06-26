<?php
include('tmdb_api.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

$tmdb = new TMDB();
$popularMovies = $tmdb->getPopularMovies();
 
// Exibe apenas filmes ativos na home
include('conexao.php');
// Busca apenas filmes com exibirNaHome = true
$stmt = $conn->prepare("SELECT * FROM filmes WHERE exibirNaHome = 1 ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$filmesAtivos = $result;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/modal.css">
    <title>Unifilmes</title>
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
                        <li><a href="./usuarios.php">Usuarios</a></li>
                        <li><a href="./movies.php">Filmes</a></li>
                        <li><a href="#">Ajuda</a></li>
                    </ul>
                </div>
            </div>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </div>
</header>

<main class="container">
    <aside class="menu">
        <h3>Filmes Populares</h3>
        <ul>
            <?php foreach ($popularMovies['results'] as $movie): ?>
                <li><?php echo $movie['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section class="filmes">
        <h3>Filmes em Destaque</h3>
        <div class="filmes-grid">
            <?php if ($filmesAtivos && $filmesAtivos->num_rows > 0): ?>
                <?php while ($filme = $filmesAtivos->fetch_assoc()): ?>
                    <a href="description.php?id=<?= urlencode($filme['imdbID']) ?>" class="card-link">
                        <div class="card" style="box-shadow:0 2px 8px rgba(0,0,0,0.08);border-radius:10px;overflow:hidden;background:#fff;max-width:320px;margin:0 auto 24px;">
                            <?php if (!empty($filme['poster'])): ?>
                                <img src="<?= htmlspecialchars($filme['poster']) ?>" alt="<?= htmlspecialchars($filme['title']) ?>" style="width:100%;height:auto;display:block;object-fit:cover;">
                            <?php endif; ?>
                            <div class="card-body" style="padding:16px;">
                                <h3 style="margin:0 0 8px 0; color:#007bff; font-size:20px;">
                                    <?= htmlspecialchars($filme['title']) ?><?php if (!empty($filme['year'])): ?> (<?= htmlspecialchars($filme['year']) ?>)<?php endif; ?>
                                </h3>
                                <p style="margin:0 0 8px 0; color:#333; font-size:15px;">
                                    <?= htmlspecialchars($filme['plot']) ?>
                                </p>
                                <?php if (!empty($filme['actors'])): ?>
                                <small style="color:#555;"><strong>Elenco:</strong> <?= htmlspecialchars($filme['actors']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center;">Nenhum filme disponível na home ainda.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<div style="margin: 20px; text-align: right;">
    <button class="btn-novo-usuario" id="botaoPesquisar" style="
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
</div>

<!-- Modal de pesquisa de filmes -->
<div id="modalPesquisa" class="modal">
    <div class="modal-content" style="max-width:600px;min-width:350px;position:relative;">
        <span class="close" id="fecharModalPesquisa" tabindex="0" aria-label="Fechar modal">&times;</span>
        <h3 style="color:#007bff;font-weight:600;">Pesquisar Filme</h3>
        <form id="formPesquisaFilme" onsubmit="return false;" style="display:flex;gap:10px;align-items:center;justify-content:center;margin-bottom:16px;">
            <input type="text" id="inputPesquisaFilme" placeholder="Digite o título do filme" style="flex:1;padding:10px 14px;border-radius:6px;border:1px solid #b0b8c1;font-size:16px;background:#f7fafd;">
            <button type="button" id="btnBuscarFilme" style="background:#007bff;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:600;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.07);display:flex;align-items:center;gap:6px;">Buscar título</button>
        </form>
        <div id="resultadosPesquisaFilme" style="max-height:350px;overflow-y:auto;"></div>
    </div>
</div>

<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
<script src="../scripts/openEditModal.js"></script>
<script src="../scripts/pesquisaFilmeModal.js"></script>
</body>
</html>