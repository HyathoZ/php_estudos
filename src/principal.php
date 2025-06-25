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
$filmesAtivos = $conn->query("SELECT * FROM filmes WHERE ativoNaHome = 1");
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
                    <div class="filme-card">
                        <?php if ($filme['imagem']): ?>
                            <img src="<?php echo htmlspecialchars($filme['imagem']); ?>" alt="<?php echo htmlspecialchars($filme['titulo']); ?>" class="filme-poster">
                        <?php endif; ?>
                        <div class="filme-info">
                            <h4><?php echo htmlspecialchars($filme['titulo']); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars($filme['descricao'])); ?></p>
                            <?php if (!empty($filme['elenco'])): ?>
                                <p><strong>Elenco:</strong> <?php echo htmlspecialchars($filme['elenco']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($filme['avaliacao'])): ?>
                                <p><strong>Avaliação:</strong> <?php echo htmlspecialchars($filme['avaliacao']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center;">Nenhum destaque disponível no momento.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
</body>
</html>